<?php

namespace NFePHP\NFSe\Models\Infisc\Factories;

use NFePHP\NFSe\Models\Infisc\Factories\Factory;

class PedidoCancelaNFSe extends Factory
{
    public function render(
        $versao,
        $CNPJ,
        $chave,
        $motivo
    ) {
        $xsd = 'SchemaCaxias-NFSe';
        $method = "pedCancelaNFSe";
        $content = "<$method versao=\"1.0\">";
        $content .= "<CNPJ>$CNPJ</CNPJ>";
        $content .= "<chvAcessoNFS-e>$chave</chvAcessoNFS-e>";
        $content .= "<motivo>$motivo</motivo>";
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
