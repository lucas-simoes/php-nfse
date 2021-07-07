<?php

namespace NFePHP\NFSe\Models\IPM\Factories\v100;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\IPM\Factories\Factory;

class ConsultarNumeroSerieCadastro extends Factory
{
    /**
     * Método usado para consultar nota
     * @param int $numero
     * @param int $serie
     * @param int $cadastro
     * @return string
     */
    public function render(
        int $numero,
        int $serie,
        int $cadastro
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
            'numero',
            $numero,
            true,
            "Número da nota fiscal",
            true
        );

        $dom->addChild(
            $pesquisa,
            'serie',
            $serie,
            true,
            "Série da nota fiscal",
            true
        );

        $dom->addChild(
            $pesquisa,
            'cadastro',
            $cadastro,
            true,
            "Cadastro economico do prestador",
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
