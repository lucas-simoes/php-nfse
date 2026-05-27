<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Informações complementares do serviço prestado (campo opcional).
 */
class ComplementoServico
{
    public function __construct(
        public readonly ?string $textoComplemento,
        public readonly ?string $codigoIncentivoBeneficio,
    ) {
    }
}
