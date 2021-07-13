<?php

namespace NFePHP\NFSe\Models\IPM;

/**
 * Classe a construção do xml dos RPS
 * para o modelo IPM
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

class Rps extends RpsBase
{
    const TOMADORPJ = 'J';
    const TOMADORPF = 'F';
    const TOMADORES = 'E';

    const AVISTA         = 1;
    const APRAZO         = 2;
    const NAAPRESENTACAO = 3;
    const CARTAODEBITO   = 4;
    const CARTAOCREDITO = 5;

    const SIM = 1;
    const NAO = 2;

    /**
     * @var string
     */
    public $infCpfCnpjPrestador;
    /**
     * @var array
     */
    public $infTomador = ['tipo' => '', 'cpfcnpj' => '', 'ie' => '', 'nome_razao_social' => '', 'sobrenome_nome_fantasia' => '', 'email' => ''];

    /**
     * @var array
     */
    public $infTomadorEstrangeiro;

    /**
     * @var array
     */
    public $infTomadorEndereco = [
        'end' => '',
        'numero' => '',
        'complemento' => '',
        'bairro' => '',
        'cmun' => '',
        'uf' => '',
        'cep' => ''
    ];

    /**
     * @var array
     */
    public $infTomadorTelefone = [
        'ddd_fone_comercial' => '',
        'fone_comercial' => '',
        'ddd_fone_residencial' => '',
        'fone_residencial' => '',
        'ddd_fax' => '',
        'fone_fax' => ''
    ];

    /**
     * @var array
     */
    public $infPedagio;


    /**
     * @var array
     */
    public $infGenericos = [];

    /**
     * @var array
     */
    public $infFormasPagamentos;

    /**
     * @var array
     */
    public $infProdutos;

    /**
     * @var ItensRps[] 
     */
    public $infItens = [];

    /**
     * @var string
     */
    public $infIdentificador;

    /**
     * @var int
     */
    public $infNumero;

    /**
     * @var inta
     */
    public $infSerie;

    /**
     * @var DateTime
     */
    public $infDataEmissao;

    /**
     * @var DateTime
     */
    public $infDataFatoGerador;

    /**
     * @var float
     */
    public $infValorTotal;

    /**
     * @var float
     */
    public $infValorDesconto;

    /**
     * @var float
     */
    public $infValorIr;

    /**
     * @var float
     */
    public $infValorInss;

    /**
     * @var float
     */
    public $infValorContribuicaoSocial;

    /**
     * @var float
     */
    public $infValorRps;

    /**
     * @var float
     */
    public $infValorPis;
    /**
     * @var float
     */
    public $infValorCofins;

    /**
     * @var float
     */
    public $infObservacao;
    
    /**
     * Set informations of customer
     * @param string $tipo
     * @param string $cpfcnpj
     * @param string $ie
     * @param string $nome_razao_social
     * @param string $sobrenome_nome_fantasia
     * @param string $email
     */
    public function tomador($tipo, $cpfcnpj, $ie, $nome_razao_social, $sobrenome_nome_fantasia, $email)
    {
        $this->infTomador = [
            'tipo' => $tipo,
            'cpfcnpj' => $this->getCpfCnpj($cpfcnpj, 'cpf/cnpj do tomador'),
            'ie' => $ie,
            'nome_razao_social' => $nome_razao_social,
            'sobrenome_nome_fantasia' => $sobrenome_nome_fantasia,
            'email' => $email,
        ];
    }

    /**
     * Set informations of foreign customer
     * @param string $identificador
     * @param string $estado
     * @param string $pais
     * @param string $cpfcnpj
     */
    public function tomadorEstrangeiro($identificador, $estado, $pais)
    {
        $this->infTomadorEstrangeiro = [
            'identificador' => $identificador,
            'estado'        => $estado,
            'pais'          => $pais,            
        ];
    }

    /**
     * Set address of customer
     * @param string $endereco_informado
     * @param string $logradouro
     * @param string $numero_residencia
     * @param string $complemento
     * @param string $ponto_referencia
     * @param string $bairro
     * @param int $cidade     
     * @param int $cep
     */
    public function tomadorEndereco(
        $endereco_informado,
        $logradouro,
        $numero_residencia, 
        $complemento,
        $ponto_referencia,
        $bairro,
        $cidade,
        $cep
    ) {
        $this->infTomadorEndereco = [
            'endereco_informado' => $endereco_informado,
            'logradouro' => $logradouro,
            'numero_residencia' => $numero_residencia,
            'complemento' => $complemento,
            'ponto_referencia' => $ponto_referencia,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'cep' => $cep
        ];
    }

    /**
     * Set phones of customer
     * @param string ddd_fone_comercial
     * @param string fone_comercial
     * @param string ddd_fone_residencial
     * @param string fone_residencial
     * @param string ddd_fax
     * @param string fone_fax
     */
    public function tomadorTelefone(
        $ddd_fone_comercial = '',
        $fone_comercial = '',
        $ddd_fone_residencial = '',
        $fone_residencial = '',
        $ddd_fax = '',
        $fone_fax = ''
    ) {
        $this->infTomadorTelefone = [
            'ddd_fone_comercial'   => $this->getDddFone($ddd_fone_comercial, 'ddd_fone_comercial'),
            'fone_comercial'       => $this->getFone($fone_comercial, 'fone_comercial'),
            'ddd_fone_residencial' => $this->getDddFone($ddd_fone_residencial, 'ddd_fone_residencial'),
            'fone_residencial'     => $this->getFone($fone_residencial, 'fone_residencial'),
            'ddd_fax'              => $this->getDddFone($ddd_fax, 'ddd_fax'),
            'fone_fax'             => $this->getFone($fone_fax, 'fone_fax'),
        ];
    }

    /**
     * Set inf generic inf
     * @param string titulo
     * @param string descricao
     */
    public function addItens(
        ItensRps $item
    ) {
        $this->infItens[] = $item;
    }

    /**
     * Set inf generic inf
     * @param string titulo
     * @param string descricao
     */
    public function addLinhaGenerico(
        $titulo,
        $descricao
    ) {
        $this->infGenericos[] = [
            'titulo'    => $titulo,
            'descricao' => $descricao
        ];
    }

    /**
     * Set inf of products
     * @param string descricao
     * @param string valor
     */
    public function produtos(
        $descricao,
        $valor
    ) {
        $this->infProdutos = [
            'valor'     => $this->getValorFormatado($valor),
            'descricao' => $descricao
        ];
    }

    /**
     * Set inf of products
     * @param string tipo_pagamento
     */
    public function formaPagamento(
        $tipo_pagamento
    ) {
        $this->infFormasPagamentos = [
            'tipo_pagamento' => $tipo_pagamento,
            'parcelas'       => []
        ];
    }

    /**
     * Set inf of products
     * @param int $numero
     * @param float $valor
     * @param DateTime $data_vencimento
     */
    public function addParcela(
        int $numero,
        float $valor,
        DateTime $data_vencimento
    ) {
        $this->infFormasPagamentos['parcelas'][] = [
            'numero'          => $numero,
            'valor'           => $valor,
            'data_vencimento' => $data_vencimento
        ];
    }

    /**
     * Set servico conforme Lista (código DMS). 
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function cpfCnpjPrestador($value, $campo = null)
    {
        $this->infCpfCnpjPrestador = $this->getCpfCnpj($value, $campo);
    }

    protected function getCpfCnpj($value, $campo)
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
        return $value;
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

    /**
     * Set informations of intermediary
     * @param string $codEquipamento
     */
    public function pedagio($codEquipamento)
    {
        $this->infPedagio = [
            'cod_equipamento_automatico' => $this->codEquipamento($codEquipamento)
        ];
    }

    /**
     * Informações referentes ao código do equipamento eletrônico de cobrança automática para pedágios 
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    protected function codEquipamento($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O código do equipamento eletrônico do pedágio não pode ser vazia e deve ter até 100 caracteres.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter até 100 caracteres. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 100)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        return $campo;
    }

    /**
     * Set servico conforme Lista (código DMS). 
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function identificador($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O identificador do arquivo não pode ser vazia e deve ter até 80 caracteres.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter até 11 caracteres. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 80)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infIdentificador = $value;
    }

    /**
     * Set number of RPS
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function numero($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O numero do RPS deve ser um inteiro positivo apenas.";
        } else {
            $msg = "O item '$campo' deve ser um inteiro positivo apenas. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infNumero = $value;
    }

    /**
     * Set series of RPS
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function serie($value, $campo = null)
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
        $this->infSerie = $value;
    }

    /**
     * Set date of issue
     * @param DateTime $value
     */
    public function dataEmissao(DateTime $value)
    {
        $this->infDataEmissao = $value;
    }

    /**
     * Set date of issue
     * @param DateTime $value
     */
    public function dataFatoGerador(DateTime $value)
    {
        $this->infDataFatoGerador = $value;
    }

    /**
     * Set service amount
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorTotal($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorTotal = $this->getValorFormatado($value);
    }

    /**
     * Set service discont
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorDesconto($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorDesconto = $this->getValorFormatado($value);
    }

    /**
     * Set amount for IR tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorIr($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorIr = $this->getValorFormatado($value);
    }

    /**
     * Set amount for INSS tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorInss($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorInss = $this->getValorFormatado($value);
    }

    /**
     * Set amount for Contribuicao Social tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorContribuicaoSocial($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorContribuicaoSocial = $this->getValorFormatado($value);
    }

    /**
     * Set Retenções da Previdência Social
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorRps($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorRps = $this->getValorFormatado($value);
    }

    /**
     * Set amount for PIS tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorPis($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorPis = $this->getValorFormatado($value);
    }

    /**
     * Set amount for COFINS tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorCofins($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorCofins = $this->getValorFormatado($value);
    }

    /**
     * Informações da NFS-e
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function observacao($value, $campo = null)
    {
        if (!$campo) {
            $msg = "As observações da NFS-e não pode ser vazia e deve ter até 1000 caracteres.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter até 1000 caracteres. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 1000)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infObservacao = $value;
    }
    
    private function getFone($value, $campo = "") {
        if($value == "") {
            return "";
        }
        if (!$campo) {
            $msg = "O ddd não pode ser vazia e deve possuir 3 caracteres numéricos.";
        } else {
            $msg = "O item '$campo' ser vazia e deve possuir 3 caracteres numéricos. Informado: '$value'";
        }

        $value = preg_replace("/\D/","", $value);
        if (!Validator::length(8, 9)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        return $value;
    } 

    private function getDddFone($value, $campo = "") {
        if($value == "") {
            return "";
        }

        if (!$campo) {
            $msg = "O ddd não pode ser vazia e deve possuir 3 caracteres numéricos.";
        } else {
            $msg = "O item '$campo' ser vazia e deve possuir 3 caracteres numéricos. Informado: '$value'";
        }

        $value = preg_replace("/\D+/","", $value);
        if (!Validator::length(3, 3)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        return $value;
    } 

    private function getValorFormatado($value)
    {
        return \number_format(round($value, 2), 2, ',', '');
    }
}
