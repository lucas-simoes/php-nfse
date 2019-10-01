<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Goiania\Factories\v02\RenderRps;

abstract class GerarNfse extends Factory
{

    protected $xmlns;
    protected $schemeFolder;
    protected $cmun;

    /**
     * @param $xmlns
     */
    public function setXmlns($xmlns)
    {
        $this->xmlns = $xmlns;
    }

    /**
     * @param $schemeFolder
     */
    public function setSchemeFolder($schemeFolder)
    {
        $this->schemeFolder = $schemeFolder;
    }

    /**
     * @param $cmun
     */
    public function setCodMun($cmun)
    {
        $this->cmun = $cmun;
    }

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $rps
     * @return bool|string
     */
    public function render(
        $versao,
        $rps
    ) {
        $xsd = "nfse_v{$versao}";

        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('GerarNfseEnvio');
        $root->setAttribute('xmlns', $this->xmlns);

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        RenderRps::appendRps($rps, $this->timezone, $this->certificate, $this->algorithm, $dom, $root);

        //Parse para XML
        $xml = substr(str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML()), 1);
        $xml = $this->clear($xml);
        $this->validar($versao, $xml, $this->schemeFolder, $xsd, '');

        return $xml;
    }
}