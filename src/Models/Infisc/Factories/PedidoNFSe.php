<?php

namespace NFePHP\NFSe\Models\Infisc\Factories;

use NFePHP\NFSe\Models\Infisc\Factories\Factory;

class PedidoNFSe extends Factory
{
    public function render(
        $versao,
        $CNPJ,
        $chave
    ) {
        $xsd = 'SchemaCaxias-NFSe';
        $method = "pedidoNFSe";
        $content = "<$method versao=\"1.0\">";
        $content .= "<CNPJ>$CNPJ</CNPJ>";
        $content .= "<chvAcessoNFS-e>$chave</chvAcessoNFS-e>";
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
