<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Tests\Providers\Nacional\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;
use NFePHP\NFSe\Providers\Nacional\NacionalClient;
use NFePHP\NFSe\Providers\Nacional\Exceptions\ValidationException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\AuthException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\NotFoundException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\AdnException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\TimeoutException;
use PHPUnit\Framework\TestCase;

class ExceptionMappingTest extends TestCase
{
    private function makeConfig(): ConfiguracaoNacional
    {
        return new ConfiguracaoNacional(
            certificadoP12: '',
            senhaCertificado: '',
            ambiente: ConfiguracaoNacional::HOMOLOGACAO,
        );
    }

    private function makeClientWithMock(MockHandler $mock): NacionalClient
    {
        $handler = HandlerStack::create($mock);
        $guzzle  = new Client(['handler' => $handler]);

        return NacionalClient::withGuzzle($this->makeConfig(), $guzzle);
    }

    public function testHttp400LancaValidationException(): void
    {
        $body = json_encode(['erros' => [['codigo' => 'E001', 'mensagem' => 'CNPJ inválido']]]);
        $mock = new MockHandler([new Response(400, [], $body)]);

        $this->expectException(ValidationException::class);
        $this->makeClientWithMock($mock)->post('/api/v1/nfse', []);
    }

    public function testHttp422LancaValidationException(): void
    {
        $body = json_encode(['erros' => [['codigo' => 'E002', 'mensagem' => 'Campo obrigatório ausente']]]);
        $mock = new MockHandler([new Response(422, [], $body)]);

        $this->expectException(ValidationException::class);
        $this->makeClientWithMock($mock)->post('/api/v1/nfse', []);
    }

    public function testHttp401LancaAuthException(): void
    {
        $mock = new MockHandler([new Response(401, [], '{"erro":"Não autorizado"}')]);

        $this->expectException(AuthException::class);
        $this->makeClientWithMock($mock)->post('/api/v1/nfse', []);
    }

    public function testHttp403LancaAuthException(): void
    {
        $mock = new MockHandler([new Response(403, [], '{"erro":"Acesso negado"}')]);

        $this->expectException(AuthException::class);
        $this->makeClientWithMock($mock)->get('/api/v1/nfse/123');
    }

    public function testHttp404LancaNotFoundException(): void
    {
        $mock = new MockHandler([new Response(404, [], '{"erro":"NFS-e não encontrada"}')]);

        $this->expectException(NotFoundException::class);
        $this->makeClientWithMock($mock)->get('/api/v1/nfse/123');
    }

    public function testHttp500LancaAdnException(): void
    {
        $mock = new MockHandler([new Response(500, [], '{"erro":"Erro interno"}')]);

        $this->expectException(AdnException::class);
        $this->makeClientWithMock($mock)->post('/api/v1/nfse', []);
    }

    public function testHttp503LancaAdnException(): void
    {
        $mock = new MockHandler([new Response(503, [], '{"erro":"Serviço indisponível"}')]);

        $this->expectException(AdnException::class);
        $this->makeClientWithMock($mock)->get('/api/v1/nfse/123');
    }

    public function testTimeoutLancaTimeoutException(): void
    {
        $request = new Request('POST', '/api/v1/nfse');
        $mock    = new MockHandler([
            new ConnectException('cURL error 28: Operation timed out', $request),
        ]);

        $this->expectException(TimeoutException::class);
        $this->makeClientWithMock($mock)->post('/api/v1/nfse', []);
    }
}
