<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories;

use NFePHP\NFSe\Models\Abrasf\Factories\Header;
use NFePHP\NFSe\Models\Abrasf\Factories\Factory;

abstract class CancelarNfse extends Factory
{
    protected $xmlns;
    protected $schemeFolder;
    protected $cmun;

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteTipoDoc
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     * @return string
     */
    abstract public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $nfseNumero
    );

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
}
