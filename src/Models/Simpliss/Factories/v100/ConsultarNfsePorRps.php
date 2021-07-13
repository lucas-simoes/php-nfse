<?php

namespace NFePHP\NFSe\Models\Simpliss\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Simpliss\Factories\Factory;

class ConsultarNfsePorRps extends Factory
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF
     * @param $im
     * @param $numerorRps,
     * @param $serie,
     * @param $tipo
     * @return mixed
     */
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $im,
        $numerorRps,
        $serie,
        $tipo
    ) {
        $xsd = "servico_consultar_nfse_rps_envio";
        $dom = new Dom('1.0', 'utf-8');

        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseRpsEnvio');
        
        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        /** Identificação da RPS **/
        $identificacaoRps = $dom->createElement('IdentificacaoRps');

        $dom->addChild(
            $identificacaoRps,
            'Numero',
            $numerorRps,
            true,
            "Numero do RPS",
            false
        );
        $dom->addChild(
            $identificacaoRps,
            'Serie',
            $serie,
            true,
            "Serie do RPS",
            false
        );
        $dom->addChild(
            $identificacaoRps,
            'Tipo',
            $tipo,
            true,
            "Tipo do RPS",
            false
        );
        $dom->appChild($root, $identificacaoRps, 'Adicionando tag IdentificacaoRPS');

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');

        //Adiciona o Cnpj na tag Cnpj
        $dom->addChild(
            $prestador,
            'Cnpj',
            $remetenteCNPJCPF,
            true,
            "Cnpj",
            true
        );

        //Adiciona a InscricaoMunicipal na tag InscricaoMunicipal
        $dom->addChild(
            $prestador,
            'InscricaoMunicipal',
            $im,
            true,
            "InscricaoMunicipal",
            true
        );

        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $prestador, 'Adicionando tag Prestador');
        
        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '', $this->cmun);
        echo '<pre>'.print_r('teste').'</pre>';die;
        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }
}
