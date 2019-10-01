<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories;

abstract class ConsultarLoteRps extends Factory
{
    protected $xmlns;

    /**
     * Método usado para gerar o XML do Soap Request
     * @param $cnpj
     * @param $im
     * @param $protocolo
     * @return mixed
     */
    abstract public function render(
        $cnpj,
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