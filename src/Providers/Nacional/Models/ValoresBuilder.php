<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Builder fluente para Valores.
 */
class ValoresBuilder
{
    private ?ValorServico $valorServicoPrestado = null;
    private ?TotalMaior   $totalMaior           = null;
    private ?Tributacao   $tributacao            = null;

    public function valorServicoPrestado(ValorServico $valorServico): static
    {
        $this->valorServicoPrestado = $valorServico;
        return $this;
    }

    public function totalMaior(TotalMaior $totalMaior): static
    {
        $this->totalMaior = $totalMaior;
        return $this;
    }

    public function tributacao(Tributacao $tributacao): static
    {
        $this->tributacao = $tributacao;
        return $this;
    }

    public function build(): Valores
    {
        if ($this->valorServicoPrestado === null) {
            throw new \InvalidArgumentException('ValorServico é obrigatório em Valores.');
        }
        if ($this->totalMaior === null) {
            throw new \InvalidArgumentException('TotalMaior é obrigatório em Valores.');
        }
        if ($this->tributacao === null) {
            throw new \InvalidArgumentException('Tributacao é obrigatória em Valores.');
        }

        return new Valores(
            valorServicoPrestado: $this->valorServicoPrestado,
            totalMaior: $this->totalMaior,
            tributacao: $this->tributacao,
        );
    }
}
