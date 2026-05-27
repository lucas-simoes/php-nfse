<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional;

use NFePHP\NFSe\Providers\Nacional\Interfaces\NacionalProviderInterface;
use NFePHP\NFSe\Providers\Nacional\Models\Dps;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaEmissao;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaConsulta;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaCancelamento;

/**
 * Provedor Nacional NFS-e — ponto de entrada público da integração com o ADN.
 *
 * Implementa NacionalProviderInterface usando NacionalClient (HTTP/mTLS)
 * e NacionalTransformer (DPS → JSON array).
 *
 * Os métodos emitir(), consultar() e cancelar() são esqueletos lançando
 * BadMethodCallException até as fases de implementação (US1, US2, US3).
 */
class Nacional implements NacionalProviderInterface
{
    /** @phpstan-ignore-next-line property.onlyWritten (será lida em T023 — US1) */
    private NacionalClient      $client;
    /** @phpstan-ignore-next-line property.onlyWritten (será lida em T022/T023 — US1) */
    private NacionalTransformer $transformer;

    public function __construct(ConfiguracaoNacional $config)
    {
        $this->client      = NacionalClient::create($config);
        $this->transformer = new NacionalTransformer();
    }

    /**
     * Emite uma NFS-e pelo Padrão Nacional (ADN).
     * Implementação completa será feita em T023 (US1).
     *
     * @throws \BadMethodCallException até T023 ser implementado
     */
    public function emitir(Dps $dps): RespostaEmissao
    {
        throw new \BadMethodCallException('not implemented');
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
