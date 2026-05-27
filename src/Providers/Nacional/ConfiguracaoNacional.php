<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional;

/**
 * Value object de configuração para o Provedor Nacional NFS-e (ADN).
 *
 * Constantes de ambiente:
 *   PRODUCAO    = 1  → https://www.nfse.gov.br
 *   HOMOLOGACAO = 2  → https://hom.nfse.gov.br
 */
class ConfiguracaoNacional
{
    public const PRODUCAO    = 1;
    public const HOMOLOGACAO = 2;

    private const URL_PRODUCAO    = 'https://www.nfse.gov.br';
    private const URL_HOMOLOGACAO = 'https://hom.nfse.gov.br';

    public function __construct(
        private readonly string $certificadoP12,
        private readonly string $senhaCertificado,
        private readonly int    $ambiente     = self::HOMOLOGACAO,
        private readonly int    $timeout      = 30,
        private readonly string $versaoSchema = '1.00',
    ) {
    }

    public function getUrlBase(): string
    {
        return $this->ambiente === self::PRODUCAO
            ? self::URL_PRODUCAO
            : self::URL_HOMOLOGACAO;
    }

    public function getAmbiente(): int
    {
        return $this->ambiente;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getVersaoSchema(): string
    {
        return $this->versaoSchema;
    }

    public function getCertificadoP12(): string
    {
        return $this->certificadoP12;
    }

    public function getSenhaCertificado(): string
    {
        return $this->senhaCertificado;
    }
}
