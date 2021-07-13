<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories;

abstract class ConsultarNfsePorFaixa extends Factory
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
     * @param $numeroNfseInicial
     * @param $numeroNfseFinal
     * @param $pagina
     * @return mixed
     */
    abstract public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $numeroNfseInicial,
        $numeroNfseFinal,
        $pagina
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
