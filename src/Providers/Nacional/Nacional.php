<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional;

use NFePHP\NFSe\Providers\Nacional\Interfaces\NacionalProviderInterface;
use NFePHP\NFSe\Providers\Nacional\Models\Dps;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaCancelamento;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaConsulta;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaEmissao;

/**
 * Provedor Nacional NFS-e — ponto de entrada público da integração com o ADN.
 *
 * Implementa NacionalProviderInterface usando NacionalClient (HTTP/mTLS)
 * e NacionalTransformer (DPS → JSON array).
 *
 * Uso:
 *   $nacional = new Nacional($configuracao);
 *   $resposta = $nacional->emitir($dps);
 *
 * Em testes, injete NacionalClient diretamente:
 *   $nacional = new Nacional($configuracao, $clientMock);
 */
class Nacional implements NacionalProviderInterface
{
    private NacionalClient      $client;
    private NacionalTransformer $transformer;

    /**
     * @param NacionalClient|null $client Injeção opcional (produção usa NacionalClient::create)
     */
    public function __construct(ConfiguracaoNacional $config, ?NacionalClient $client = null)
    {
        $this->client      = $client ?? NacionalClient::create($config);
        $this->transformer = new NacionalTransformer();
    }

    /**
     * Emite uma NFS-e pelo Padrão Nacional (ADN).
     *
     * Fluxo:
     * 1. Transforma Dps em array infDPS via NacionalTransformer
     * 2. Envia POST /api/v1/nfse com o payload
     * 3. Mapeia a resposta:
     *    - Presença de chave 'nfse' → emissão síncrona (HTTP 201), status EMITIDA
     *    - Presença de chave 'protocolo' sem 'nfse' → processamento assíncrono (HTTP 202), status ACEITA
     *
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\ValidationException em caso de HTTP 400/422
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\AuthException em caso de HTTP 401/403
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\AdnException em caso de HTTP 500/503
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\TimeoutException em caso de timeout
     */
    public function emitir(Dps $dps): RespostaEmissao
    {
        $payload = $this->transformer->transform($dps);
        $data    = $this->client->post('/api/v1/nfse', $payload);

        // HTTP 201 — emissão síncrona: response contém objeto 'nfse' com chaveAcesso e numero
        if (isset($data['nfse']) && is_array($data['nfse'])) {
            return new RespostaEmissao(
                protocolo:   $data['nfse']['protocolo'] ?? '',
                status:      'EMITIDA',
                chaveAcesso: $data['nfse']['chaveAcesso'] ?? null,
                numeroNfse:  $data['nfse']['numero'] ?? null,
                erros:       [],
            );
        }

        // HTTP 202 — processamento assíncrono: response contém apenas 'protocolo' e 'mensagem'
        return new RespostaEmissao(
            protocolo:   (string) ($data['protocolo'] ?? ''),
            status:      'ACEITA',
            chaveAcesso: null,
            numeroNfse:  null,
            erros:       [],
        );
    }

    /**
     * Consulta uma NFS-e pelo padrão nacional.
     * Implementação completa será feita em T025 (US2).
     *
     * @throws \BadMethodCallException até T025 ser implementado
     */
    public function consultar(string $chaveAcesso): RespostaConsulta
    {
        throw new \BadMethodCallException('not implemented');
    }

    /**
     * Cancela uma NFS-e emitida pelo padrão nacional.
     * Implementação completa será feita em T027 (US3).
     *
     * @throws \BadMethodCallException até T027 ser implementado
     */
    public function cancelar(string $chaveAcesso, string $codigoMotivo): RespostaCancelamento
    {
        throw new \BadMethodCallException('not implemented');
    }
}
