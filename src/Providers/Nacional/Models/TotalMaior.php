<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Total maior (pTotMaior) — valores líquidos e carga tributária total.
 */
class TotalMaior
{
    public function __construct(
        /** Valor líquido = valorRecebido - valorDesconto - deduções */
        public readonly string $valorLiquido,
        /** Total de tributos em valor absoluto */
        public readonly string $valorCargaTributaria,
        /** Percentual da carga tributária ex: "0.0500" (5%) */
        public readonly string $percentualCargaTributaria,
    ) {
    }
}
