<?php

namespace NFePHP\NFSe\Models\Infisc\Factories;

use NFePHP\NFSe\Models\Infisc\Factories\Factory;

class PedidoNFSePDF extends Factory
{
    public function render(
        $versao,
        $CNPJ,
        $notaInicial,
        $notaFinal,
        $serie
    ) {
        $xsd = 'SchemaCaxias-NFSe';
        $method = "pedidoNFSePDF";
        $content = "<$method versao=\"1.0\">";
        $content .= "<CNPJ>$CNPJ</CNPJ>";
        $content .= "<notaInicial>$notaInicial</notaInicial>";
        $content .= "<notaFinal>$notaFinal</notaFinal>";
        $content .= "<serieNotaFiscal>$serie</serieNotaFiscal>";
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
