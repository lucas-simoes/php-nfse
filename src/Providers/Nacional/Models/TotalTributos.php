<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Total de tributos (totTrib) — resumo da carga tributária.
 */
class TotalTributos
{
    public function __construct(
        /** Ex: "0.1500" (15%) */
        public readonly string $percentualTotalTributos,
        public readonly string $valorTotalTributos,
        /** 1=Calculado pela lib, 2=Informado pelo contribuinte */
        public readonly int    $indicadorTotalTributos,
    ) {
    }
}
