<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Tributação municipal — ISSQN (tribMun).
 */
class TributacaoMunicipal
{
    public function __construct(
        /** 1=Normal, 2=Imune, 3=Isento, 4=Exportação, 5=Suspensão */
        public readonly int    $tributacaoIssqn,
        /** Código IBGE do município de incidência */
        public readonly string $codigoLocalIncidencia,
        /** Alíquota ex: "0.0500" (5%) */
        public readonly string $aliquota,
        /** 1=Sem retenção, 2=Retido pelo tomador */
        public readonly int    $tipoRetencaoBM,
    ) {
    }
}
