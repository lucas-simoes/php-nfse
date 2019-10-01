<?php

namespace NFePHP\NFSe\Models\Infisc\Factories;

use NFePHP\NFSe\Models\Infisc\Factories\Factory;
use NFePHP\NFSe\Models\Infisc\RenderRPS;

class EnviarLoteNotas extends Factory
{
    public function render(
        $versao,
        $CNPJ,
        $dhTrans,
        $rpss
    ) {
        $xsd = 'SchemaCaxias-NFSe';
        $method = "envioLote";
        $content = "<$method versao=\"1.0\">";
            $content .= "<CNPJ>$CNPJ</CNPJ>";
            $content .= "<dhTrans>$dhTrans</dhTrans>";
        foreach ($rpss as $rps) {
            $content .= RenderRPS::toXml($rps, $this->algorithm);
        }
        
        $content .= "</$method>";
        
        $content = \NFePHP\Common\Strings::clearXmlString($content);
        $body = \NFePHP\Common\Signer::sign(
            $this->certificate,
            $content,
            $method,
            '',
            $this->algorithm,
            [false,false,null,null]
        );
        $body = $this->clear($body);
        //error_log(print_r($body, TRUE) . PHP_EOL, 3, '/var/www/tests/sped-nfse/post.xml');
        $this->validar($versao, $body, 'Infisc', $xsd, '');
        return $body;
    }
}
