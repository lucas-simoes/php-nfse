<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Responses;

/**
 * Resposta da operação de cancelamento de NFS-e via ADN.
 *
 * NÃO usa readonly class (PHP 8.2+) — usa readonly properties (PHP 8.1).
 */
class RespostaCancelamento
{
    public function __construct(
        public readonly string             $protocolo,
        public readonly \DateTimeImmutable $dataEvento,
        /** 'Aceito' | 'Rejeitado' */
        public readonly string             $status,
    ) {
    }

    /**
     * Retorna true se o cancelamento foi aceito pelo ADN.
     */
    public function foiAceito(): bool
    {
        return $this->status === 'Aceito';
    }
}
