<?php

namespace NFePHP\NFSe\Models\Abrasf;

use DateTime;
use Respect\Validation\Validator;

/**
 * @author Tiago Franco
 * Servico para
 */
class NfseServicoPrestado
{
    /**
     * @var array
     */
    public $infPrestador = ['tipo' => '', 'cnpjcpf' => '', 'im' => ''];

    /**
     * @var array
     */
    public $infTomador = ['tipo' => '', 'cnpjcpf' => '', 'im' => ''];

    /**
     * @var array
     */
    public $infIntermediario = ['tipo' => '', 'cnpjcpf' => '', 'im' => ''];

    /**
     * @var int
     */
    public $infNumeroNfse;

    /**
     * @var DateTime
     */
    public $infDataEmissaoInicial;

    /**
     * @var DateTime
     */
    public $infDataEmissaoFinal;

    /**
     * @var DateTime
     */
    public $infDataCompetenciaInicial;

    /**
     * @var DateTime
     */
    public $infDataCompetenciaFinal;

    /**
     * @var int
     */
    public $infPagina;

    /**
     * Set informations of provider
     * @param int $tipo
     * @param string $cnpjcpf
     * @param string $im
     */
    public function prestador($tipo, $cnpjcpf, $im)
    {
        $this->infPrestador = [
            'tipo' => $tipo,
            'cnpjcpf' => $cnpjcpf,
            'im' => $im
        ];
    }

    /**
     * Set number of RPS
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function numeroNfse($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O numero da nota fiscal deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infNumeroNfse = $value;
    }

    /**
     * Set number of RPS
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function pagina($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O numero da pagina deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infPagina = $value;
    }

    /**
     * Set date of issue
     * @param DateTime $inicial
     * @param DateTime $final
     */
    public function dataEmissao(DateTime $inicial, DateTime $final)
    {
        $this->infDataEmissaoInicial = $inicial;
        $this->infDataEmissaoFinal   = $final;
    }

    /**
     * Set date of issue
     * @param DateTime $inicial
     * @param DateTime $final
     */
    public function dataCompetencia(DateTime $inicial, DateTime $final)
    {
        $this->infDataCompetenciaInicial = $inicial;
        $this->infDataCompetenciaFinal   = $final;
    }

    /**
     * Set informations of customer
     * @param int $tipo
     * @param string $cnpjcpf
     * @param string $im
     */
    public function tomador($tipo, $cnpjcpf, $im)
    {
        $this->infTomador = [
            'tipo' => $tipo,
            'cnpjcpf' => $cnpjcpf,
            'im' => $im
        ];
    }

    /**
     * Set informations of intermediary
     * @param string $tipo
     * @param string $cnpjcpf
     * @param string $im
     * @param string $razao
     */
    public function intermediario($tipo, $cnpjcpf, $im, $razao)
    {
        $this->infIntermediario = [
            'tipo' => $tipo,
            'cnpjcpf' => $cnpjcpf,
            'im' => $im,
            'razao' => $razao
        ];
    }
}
