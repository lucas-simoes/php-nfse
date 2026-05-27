<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\AdnException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\AuthException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\NotFoundException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\TimeoutException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\ValidationException;

/**
 * Cliente HTTP Guzzle para comunicação com o ADN (Ambiente de Dados Nacional).
 *
 * Gerencia o ciclo de vida dos arquivos PEM temporários para mTLS:
 * criados no construtor, removidos no __destruct().
 */
class NacionalClient
{
    private Client  $guzzle;
    private string  $baseUrl;

    /** @var list<string> Caminhos dos arquivos PEM temporários */
    private array $tempFiles = [];

    private function __construct(ConfiguracaoNacional $config, Client $guzzle)
    {
        $this->baseUrl = rtrim($config->getUrlBase(), '/');
        $this->guzzle  = $guzzle;
    }

    /**
     * Construtor principal — cria cliente Guzzle com mTLS via arquivos PEM temporários.
     */
    public static function create(ConfiguracaoNacional $config): static
    {
        [$certPath, $keyPath] = static::extractPemFiles($config);

        $guzzle = new Client([
            'timeout'     => $config->getTimeout(),
            'cert'        => $certPath,
            'ssl_key'     => $keyPath,
            'headers'     => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);

        $instance             = new static($config, $guzzle);
        $instance->tempFiles  = [$certPath, $keyPath];

        return $instance;
    }

    /**
     * Factory para injeção de Guzzle customizado em testes (sem mTLS real).
     */
    public static function withGuzzle(ConfiguracaoNacional $config, Client $guzzle): static
    {
        return new static($config, $guzzle);
    }

    /**
     * HTTP POST — envia payload JSON e retorna array decodificado.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $payload): array
    {
        try {
            $response = $this->guzzle->post(
                $this->baseUrl . $endpoint,
                ['json' => $payload]
            );

            return $this->decode((string) $response->getBody(), $response->getStatusCode());
        } catch (ConnectException $e) {
            throw new TimeoutException('Timeout ao conectar ao ADN: ' . $e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            throw $this->mapHttpException($e);
        }
    }

    /**
     * HTTP GET — consulta endpoint e retorna array decodificado.
     *
     * @return array<string, mixed>
     */
    public function get(string $endpoint): array
    {
        try {
            $response = $this->guzzle->get($this->baseUrl . $endpoint);

            return $this->decode((string) $response->getBody(), $response->getStatusCode());
        } catch (ConnectException $e) {
            throw new TimeoutException('Timeout ao conectar ao ADN: ' . $e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            throw $this->mapHttpException($e);
        }
    }

    /**
     * Remove arquivos PEM temporários ao destruir a instância.
     */
    public function __destruct()
    {
        foreach ($this->tempFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    /**
     * Extrai certificado P12 e gera arquivos PEM temporários com chmod 0600.
     *
     * @return array{string, string} [certPath, keyPath]
     */
    private static function extractPemFiles(ConfiguracaoNacional $config): array
    {
        $p12Content = $config->getCertificadoP12();
        $password   = $config->getSenhaCertificado();

        $certs = [];
        if (!openssl_pkcs12_read($p12Content, $certs, $password)) {
            throw new AuthException('Não foi possível ler o certificado P12. Verifique o conteúdo e a senha.');
        }

        $certPath = tempnam(sys_get_temp_dir(), 'nfse_cert_');
        $keyPath  = tempnam(sys_get_temp_dir(), 'nfse_key_');

        if ($certPath === false || $keyPath === false) {
            throw new \RuntimeException('Não foi possível criar arquivos temporários para o certificado.');
        }

        file_put_contents($certPath, $certs['cert']);
        file_put_contents($keyPath, $certs['pkey']);
        chmod($certPath, 0600);
        chmod($keyPath, 0600);

        return [$certPath, $keyPath];
    }

    /**
     * Decodifica JSON da resposta ou retorna array vazio se body estiver vazio.
     *
     * @return array<string, mixed>
     */
    private function decode(string $body, int $statusCode): array
    {
        if ($body === '') {
            return [];
        }

        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new AdnException($statusCode, 'Resposta inesperada do ADN (JSON inválido): ' . $body);
        }

        return $data;
    }

    /**
     * Mapeia exceção Guzzle (HTTP error) para exceção tipada da hierarquia Nacional.
     */
    private function mapHttpException(RequestException $e): \NFePHP\NFSe\Providers\Nacional\Exceptions\NacionalException
    {
        $response   = $e->getResponse();
        $statusCode = $response ? $response->getStatusCode() : 0;
        $body       = $response ? (string) $response->getBody() : '';

        return match (true) {
            in_array($statusCode, [400, 422], true) => $this->makeValidationException($body, $e),
            in_array($statusCode, [401, 403], true) => new AuthException('Falha de autenticação ou permissão. HTTP ' . $statusCode, $statusCode, $e),
            $statusCode === 404                      => new NotFoundException('NFS-e não encontrada. HTTP 404', 404, $e),
            in_array($statusCode, [500, 503], true)  => new AdnException($statusCode, 'Erro interno do ADN. HTTP ' . $statusCode, 0, $e),
            default                                  => new AdnException($statusCode, 'Erro inesperado do ADN. HTTP ' . $statusCode, 0, $e),
        };
    }

    /**
     * Constrói ValidationException a partir do body JSON de erro.
     */
    private function makeValidationException(string $body, RequestException $previous): ValidationException
    {
        $data  = json_decode($body, true);
        /** @var array<int, array{codigo: string, mensagem: string, campo?: string}> $erros */
        $erros = (is_array($data) && isset($data['erros']) && is_array($data['erros']))
            ? $data['erros']
            : [];

        return new ValidationException($erros, '', 0, $previous);
    }
}
