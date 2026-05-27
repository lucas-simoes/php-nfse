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
    private ConfiguracaoNacional $config;
    private NacionalClient       $client;
    private NacionalTransformer  $transformer;

    /**
     * @param NacionalClient|null $client Injeção opcional (produção usa NacionalClient::create)
     */
    public function __construct(ConfiguracaoNacional $config, ?NacionalClient $client = null)
    {
        $this->config      = $config;
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
     * Consulta uma NFS-e pelo padrão nacional (ADN).
     *
     * Fluxo:
     * 1. GET /api/v1/nfse/{chaveAcesso}
     * 2. Mapeia HTTP 200 → RespostaConsulta com chaveAcesso, numeroNfse,
     *    status, dataEmissao (DateTimeImmutable) e dpsOriginal (array)
     * 3. HTTP 404 → NotFoundException propagada diretamente do NacionalClient
     *
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\NotFoundException quando chave não existe
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\AuthException em caso de HTTP 401/403
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\AdnException em caso de HTTP 500/503
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\TimeoutException em caso de timeout
     */
    public function consultar(string $chaveAcesso): RespostaConsulta
    {
        $data = $this->client->get('/api/v1/nfse/' . $chaveAcesso);

        return new RespostaConsulta(
            chaveAcesso:  $data['nfse']['chaveAcesso'],
            numeroNfse:   $data['nfse']['numero'],
            status:       $data['nfse']['status'],
            dataEmissao:  new \DateTimeImmutable($data['nfse']['dataEmissao']),
            dpsOriginal:  $data['nfse']['dps'],
        );
    }

    /**
     * Cancela uma NFS-e emitida pelo padrão nacional (ADN).
     *
     * Fluxo:
     * 1. Monta payload infEvento com os campos obrigatórios do ADN
     * 2. POST /api/v1/nfse/{chaveAcesso}/cancelamento
     * 3. Mapeia HTTP 200 → RespostaCancelamento
     * 4. HTTP 400 → ValidationException propagada diretamente do NacionalClient
     *
     * Estrutura do payload (contracts/api-nacional.md §POST /cancelamento):
     *   infEvento.cOrgao       — UF do emitente (2 primeiros dígitos da chave → código IBGE)
     *   infEvento.tpAmb        — ambiente (1=produção, 2=homologação)
     *   infEvento.CNPJ         — CNPJ do emitente (dígitos 5-18 da chave de acesso, 14 dígitos)
     *   infEvento.chNFSe       — chave de acesso completa (44 dígitos)
     *   infEvento.dhEvento     — data/hora UTC-3 no momento do evento
     *   infEvento.nSeqEvento   — sempre 1 (primeiro evento de cancelamento)
     *   infEvento.tpEvento     — sempre '010100' (tipo cancelamento)
     *   infEvento.verEvento    — sempre '1.00'
     *   infEvento.detEvento    — {cMotivo, xMotivo}
     *
     * Mapa de motivos (contracts/api-nacional.md §Códigos de Motivo):
     *   1 → Erro na emissão
     *   2 → Serviço não prestado
     *   3 → Duplicidade de nota
     *   4 → Erro de tributação
     *
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\ValidationException para HTTP 400
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\AuthException para HTTP 401/403
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\AdnException para HTTP 500/503
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\TimeoutException em caso de timeout
     */
    public function cancelar(string $chaveAcesso, string $codigoMotivo): RespostaCancelamento
    {
        /** @var array<string, string> */
        $motivoMap = [
            '1' => 'Erro na emissão',
            '2' => 'Serviço não prestado',
            '3' => 'Duplicidade de nota',
            '4' => 'Erro de tributação',
        ];

        $xMotivo = $motivoMap[$codigoMotivo] ?? 'Cancelamento solicitado';

        // cOrgao: código UF extraído dos 2 primeiros dígitos da chave de acesso (cUF)
        $cOrgao = substr($chaveAcesso, 0, 2);

        // CNPJ do emitente: dígitos 6-19 da chave (0-indexed: posição 6, 14 chars)
        $cnpjEmitente = substr($chaveAcesso, 6, 14);

        // Data/hora do evento em UTC-3 (horário de Brasília)
        $dhEvento = (new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))
            ->format(\DateTimeInterface::RFC3339);

        $payload = [
            'infEvento' => [
                'cOrgao'     => $cOrgao,
                'tpAmb'      => $this->config->getAmbiente(),
                'CNPJ'       => $cnpjEmitente,
                'chNFSe'     => $chaveAcesso,
                'dhEvento'   => $dhEvento,
                'nSeqEvento' => 1,
                'tpEvento'   => '010100',
                'verEvento'  => '1.00',
                'detEvento'  => [
                    'cMotivo' => $codigoMotivo,
                    'xMotivo' => $xMotivo,
                ],
            ],
        ];

        $data = $this->client->post('/api/v1/nfse/' . $chaveAcesso . '/cancelamento', $payload);

        return new RespostaCancelamento(
            protocolo:  (string) ($data['evento']['protocolo'] ?? ''),
            dataEvento: new \DateTimeImmutable($data['evento']['dataEvento']),
            status:     (string) ($data['evento']['status'] ?? ''),
        );
    }
}
