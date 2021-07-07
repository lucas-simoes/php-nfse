<?php

namespace NFePHP\NFSe\Models\IPM\Factories\v100;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\IPM\Factories\Factory;

class ConsultarCidadeSerieNumeroRps extends Factory
{
    /**
     * Método usado para consultar nota
     * @param int $cidade
     * @param int $serie
     * @param int $numero
     * @return string
     */
    public function render(
        int $cidade,
        int $serie,
        int $numero
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
            'cidade',
            $cidade,
            true,
            "Código Tom do municipio",
            true
        );

        $dom->addChild(
            $pesquisa,
            'serie_rps',
            $serie,
            true,
            "Série do rps da nota fiscal",
            true
        );

        $dom->addChild(
            $pesquisa,
            'numero_rps',
            $numero,
            true,
            "número do rps da nota fiscal",
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
