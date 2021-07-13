<?php

namespace NFePHP\NFSe\Models\IPM\Factories\v100;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\IPM\Factories\Factory;

class ConsultarCodigoAutenticidade extends Factory
{
    /**
     * Método usado para consultar nota
     * @param $codigoAutenticidade
     * @return string
     */
    public function render(
        string $codigoAutenticidade
    ) {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento nfse
        $root = $dom->createElement('nfse');
        
        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        //Cria o elemento pesquisa
        $pesquisa = $dom->createElement('pesquisa');

        $dom->addChild(
            $pesquisa,
            'codigo_autenticidade',
            $codigoAutenticidade,
            true,
            "Código de autenticidade da nota fiscal",
            true
        );

        //Adiciona as tags ao DOM
        $root->appendChild($pesquisa);

        $body = $dom->saveXML();
        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        return '<?xml version="1.0" encoding="ISO-8859-1"?>' . $body;
    }
}
