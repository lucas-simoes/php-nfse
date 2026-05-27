<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;

/**
 * Builder fluente para construção de Dps.
 */
class DpsBuilder
{
    private string              $id              = '';
    private int                 $ambiente        = ConfiguracaoNacional::HOMOLOGACAO;
    private \DateTimeImmutable  $dataEmissao;
    private string              $competencia     = '';
    private string              $versaoAplicacao = '1.00';
    private ?Substituicao       $substituicao    = null;
    private ?Emitente           $emitente        = null;
    private ?Tomador            $tomador         = null;
    private ?Servico            $servico         = null;
    private ?Valores            $valores         = null;

    public function __construct()
    {
        $this->dataEmissao = new \DateTimeImmutable();
    }

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function ambiente(int $ambiente): static
    {
        $this->ambiente = $ambiente;
        return $this;
    }

    public function dataEmissao(\DateTimeImmutable $dataEmissao): static
    {
        $this->dataEmissao = $dataEmissao;
        return $this;
    }

    public function competencia(string $competencia): static
    {
        $this->competencia = $competencia;
        return $this;
    }

    public function versaoAplicacao(string $versaoAplicacao): static
    {
        $this->versaoAplicacao = $versaoAplicacao;
        return $this;
    }

    public function substituicao(?Substituicao $substituicao): static
    {
        $this->substituicao = $substituicao;
        return $this;
    }

    public function emitente(Emitente $emitente): static
    {
        $this->emitente = $emitente;
        return $this;
    }

    public function tomador(Tomador $tomador): static
    {
        $this->tomador = $tomador;
        return $this;
    }

    public function servico(Servico $servico): static
    {
        $this->servico = $servico;
        return $this;
    }

    public function valores(Valores $valores): static
    {
        $this->valores = $valores;
        return $this;
    }

    public function build(): Dps
    {
        // Validação: id não pode estar vazio
        if ($this->id === '') {
            throw new \InvalidArgumentException(
                'O campo id da DPS não pode estar vazio.'
            );
        }

        // Validação: competencia deve ter formato YYYY-MM (4 dígitos, hífen, 2 dígitos)
        if (!preg_match('/^\d{4}-\d{2}$/', $this->competencia)) {
            throw new \InvalidArgumentException(
                "Competencia inválida: '{$this->competencia}'. O formato obrigatório é YYYY-MM (ex: 2026-05)."
            );
        }

        // Validação: dataEmissao >= primeiro dia da competência.
        // Comparação feita no calendário local (YYYY-MM) para respeitar o fuso
        // do emitente: um documento emitido em 2026-04-30 (horário local) não
        // pode pertencer à competência 2026-05.
        $dataEmissaoMes = $this->dataEmissao->format('Y-m');
        if ($dataEmissaoMes < $this->competencia) {
            throw new \InvalidArgumentException(
                sprintf(
                    'dataEmissao (%s) não pode ser anterior ao mês da competência (%s).',
                    $this->dataEmissao->format(\DateTimeInterface::ATOM),
                    $this->competencia
                )
            );
        }

        if ($this->emitente === null) {
            throw new \InvalidArgumentException('Emitente é obrigatório na DPS.');
        }
        if ($this->tomador === null) {
            throw new \InvalidArgumentException('Tomador é obrigatório na DPS.');
        }
        if ($this->servico === null) {
            throw new \InvalidArgumentException('Servico é obrigatório na DPS.');
        }
        if ($this->valores === null) {
            throw new \InvalidArgumentException('Valores é obrigatório na DPS.');
        }

        return new Dps(
            id: $this->id,
            ambiente: $this->ambiente,
            dataEmissao: $this->dataEmissao,
            competencia: $this->competencia,
            versaoAplicacao: $this->versaoAplicacao,
            substituicao: $this->substituicao,
            emitente: $this->emitente,
            tomador: $this->tomador,
            servico: $this->servico,
            valores: $this->valores,
        );
    }
}
