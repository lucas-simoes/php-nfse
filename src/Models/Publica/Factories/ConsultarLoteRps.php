<?php

namespace NFePHP\NFSe\Models\Publica\Factories;

abstract class ConsultarLoteRps extends Factory
{
    protected $xmlns;

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $remetenteCNPJCPF
     * @param $im
     * @param $protocolo
     * @return mixed
     */
    abstract public function render(
        $versao,
        $remetenteCNPJCPF,
        $im,
        $protocolo
    );

    /**
     * @param $xmlns
     */
    public function setXmlns($xmlns)
    {
        $this->xmlns = $xmlns;
    }
}