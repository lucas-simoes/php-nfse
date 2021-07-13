<?php

namespace NFePHP\NFSe\Models\Publica\Factories;

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
    abstract public function render(
        $versao,
        $rps
    );
}