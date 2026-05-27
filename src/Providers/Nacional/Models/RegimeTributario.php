<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Regime tributário do emitente.
 */
class RegimeTributario
{
    public function __construct(
        /** 1=Não optante, 2=Optante sem IRPJ/CSLL, 3=Optante com IRPJ/CSLL */
        public readonly int    $opcaoSimplesNacional,
        /** CNAE principal do emitente (7 dígitos) */
        public readonly string $cnae,
        /** Código IBGE do município de emissão (7 dígitos) */
        public readonly string $codigoLocalEmissao,
    ) {
    }
}
