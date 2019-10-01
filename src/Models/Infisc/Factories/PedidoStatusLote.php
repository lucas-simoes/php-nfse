<?php

namespace NFePHP\NFSe\Models\Infisc\Factories;

use NFePHP\NFSe\Models\Infisc\Factories\Factory;

class PedidoStatusLote extends Factory
{
    public function render(
        $versao,
        $CNPJ,
        $lote
    ) {
        $xsd = 'SchemaCaxias-NFSe';
        $method = "pedidoStatusLote";
        $content = "<$method versao=\"1.0\">";
        $content .= "<CNPJ>$CNPJ</CNPJ>";
        $content .= "<cLote>$lote</cLote>";
        $content .= "</$method>";
        
        $body = \NFePHP\Common\Signer::sign(
            $this->certificate,
            $content,
            $method,
            '',
            $this->algorithm,
            [false,false,null,null]
        );
        $this->validar($versao, $body, 'Infisc', $xsd, '');
        
        return $body;
    }
}
