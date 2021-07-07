<?php

namespace NFePHP\NFSe\Models\IPM;

/**
 * Classe para definicao de informacoes
 * para operacoes de cancelamentos de notas
 * 
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\IPM\Rps
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

 use \DateTime;
use Respect\Validation\Validator;
use NFePHP\NFSe\Common\Rps as RpsBase;

class CancelarRps extends RpsBase
{
    const CANCELAR = 'C';

    const SIM = 1;
    const NAO = 2;

    /**
     * @var string
     */
    public $infCpfCnpjPrestador;
    
    /**
     * @var int
     */
    public $infNumeroNfse;

    /**
     * @var int
     */
    public $infSerieNfse;

    /**
     * @var string
     */
    public $infSituacao;

    /**
     * @var float
     */
    public $infObservacao;
    
    /**
     * @var int
     */
    public $infNumeroNfseSubstituta;

    /**
     * @var int
     */
    public $infSerieNfseSubstituta;

    /**
     * @var CancelarRps[] 
     */
    public $infDocumentos = [];

    /**
     * Set servico conforme Lista (código DMS). 
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function cpfCnpjPrestador($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O cpf cnpj não pode ser vazia e deve ter entre 11 ou 14 números.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter entre 11 ou 14 números. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::length(1, 14)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }        

        $this->infCpfCnpjPrestador = $value;
    }

    /**
     * Set number of nfse
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function numeroNfse($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O numero do RPS deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infNumeroNfse = $value;
    }

    public function situacao($value = self::CANCELAR, $campo = "")
    {
        if (!$campo) {
            $msg = "A situacao deve ser um character alfabetico e deve ter 1 caracteres.";
        } else {
            $msg = "O item '$campo' deve ser um character alfabetico e deve ter 1 caractere. Informado: '$value'";
        }

        $value = preg_replace("/\d+/","", $value);
        if (!Validator::stringType()->length(1, 1)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infSituacao = $value;
    }

    /**
     * Motivo do cancelamento da NFS-e
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function observacao($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O motivo de cancelamento da NFS-e não pode ser vazia e deve ter até 1000 caracteres.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter até 1000 caracteres. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 1000)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infObservacao = $value;
    }

    /**
     * Set serie of RPS
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function serieNfse($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O numero do RPS deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infSerieNfse = $value;
    }

    /**
     * Set number of RPS
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function infNumeroNfseSubstituta($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O numero do RPS deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infNumeroNfseSubstituta = $value;
    }

    /**
     * Set series of RPS
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function serieNfseSubstituta($value, $campo = null)
    {
        if (!$campo) {
            $msg = "A série do RPS deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infSerieNfseSubstituta = $value;
    }

    /**
     * Set inf generic inf
     * @param string titulo
     * @param string descricao
     */
    public function addDocumentos(
        CancelarRps $item
    ) {
        $this->infDocumentos[] = $item;
    }
}
