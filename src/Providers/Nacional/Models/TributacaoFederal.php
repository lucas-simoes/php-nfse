<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Tributação federal — opcional, apenas quando aplicável.
 * PIS/COFINS, IRPJ, CSLL são campos livres (array) pois o schema federal
 * varia por regime tributário. Null = não aplicável / não informado.
 */
class TributacaoFederal
{
    public function __construct(
        /** @var array<string, mixed>|null */
        public readonly ?array $pisCofins,
        /** @var array<string, mixed>|null */
        public readonly ?array $irpj,
        /** @var array<string, mixed>|null */
        public readonly ?array $csll,
    ) {
    }
}
