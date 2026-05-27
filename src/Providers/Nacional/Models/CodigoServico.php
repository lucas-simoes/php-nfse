<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Código de serviço conforme tabela LC116 e classificação municipal.
 */
class CodigoServico
{
    public function __construct(
        /** cTribNac — 6 dígitos (LC116) */
        public readonly string $codigoTributacaoNacional,
        /** cTribMun — código local do município */
        public readonly string $codigoTributacaoMunicipal,
        /** CNAE da atividade (7 dígitos) */
        public readonly string $cnae,
        /** Descrição livre do serviço, máx 2000 chars */
        public readonly string $descricaoServico,
    ) {
    }
}
