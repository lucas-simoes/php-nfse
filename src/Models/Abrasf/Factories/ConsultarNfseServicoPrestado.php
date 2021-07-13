<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories;

use NFePHP\NFSe\Models\Abrasf\NfseServicoPrestado;

abstract class ConsultarNfseServicoPrestado extends Factory
{
    protected $xmlns;
    protected $schemeFolder;
    protected $cmun;

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @return mixed
     */
    abstract public function render(
        $versao,
        NfseServicoPrestado $nsPrestado
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
