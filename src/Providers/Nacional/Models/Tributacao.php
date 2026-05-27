<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Bloco de tributação da DPS (trib).
 */
class Tributacao
{
    public function __construct(
        public readonly TributacaoMunicipal  $tributacaoMunicipal,
        public readonly ?TributacaoFederal   $tributacaoFederal,
        public readonly TotalTributos        $totalTributos,
    ) {
    }
}
