<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Responses;

/**
 * Resposta da operação de consulta de NFS-e via ADN (GET /api/v1/nfse/{chaveAcesso}).
 *
 * NÃO usa readonly class (PHP 8.2+) — usa readonly properties (PHP 8.1).
 */
class RespostaConsulta
{
    public function __construct(
        public readonly string             $chaveAcesso,
        public readonly string             $numeroNfse,
        /** 'Ativa' | 'Cancelada' | 'Substituida' */
        public readonly string             $status,
        public readonly \DateTimeImmutable $dataEmissao,
        /** @var array<string, mixed> JSON da DPS original registrado no ADN */
        public readonly array              $dpsOriginal,
    ) {
    }
}
