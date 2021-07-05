<?php

namespace NFePHP\NFSe\Counties\M4113700;

use DateTime;
use Respect\Validation\Validator;
use NFePHP\NFSe\Models\SIGISS\Rps as RpsSIGISS;

/**
 * Classe a construção do xml da NFSe para a
 * Cidade de Londrina PR
 * conforme o modelo SIGISS
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4113700\Rps
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Lucas B. Simões <lucas_development at outlook dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class Rps extends RpsSIGISS
{
    const TIPO_RPS   = 1;
    const TIPO_MISTO = 2;
    const TIPO_CUPOM = 3;
    
    const TOMADORPFNI = 1;
    const TOMADORPF= 2;
    const TOMADORPJMUNICIPIO = 3;
    const TOMADORPJFORA = 4;
    const TOMADORPJFORADOPAIS = 5;
    
    const CPF  = 1;
    const CNPJ = 2;

    const SIT_TRIBUTADA_PRESTADOR = 'tp'; //Tributada no Prestador
    const SIT_TRIBUTADA_TOMADOR   = 'tt';  //Tributada no tomador
    const SIT_TRIBUTADO_FIXO      = 'tf'; //Tributado Fixo
    const SIT_ISENTO              = 'is'; //Isenta/Imune
    const SIT_OUTRO_MUNICIPIO     = 'nt'; //Outro Municipio
    const SIT_EXPORTACAO          = 'si'; //Exportacao
    const SIT_CANCELADA           = 'ca'; //Cancelada

    const SIM = 1;
    const NAO = 2;

    const CODCANCSNP = 2;
    const CODCANCDN  = 4;

    /**
     * @var array
     */
    public $infPrestador = ['ccm' => '', 'cnpjcpf' => '', 'cpf_usuario' => '', 'senha_usuario' => ''];
    /**
     * @var array
     */
    public $infTomador = ['tipo' => '', 'cnpjcpf' => '', 'im' => '','ie' => '', 'razao' => '', 'tel' => '', 'ramal' => '', 'fax' => '', 'email' => ''];
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
    public $infRpsSubstituido;
    /**
     * @var array
     */
    public $infIntermediario;
    /**
     * @var array
     */
    public $infConstrucaoCivil;

    /***
     * @var string
     */
    public $infServico;

    /**
     * @var int
     */
    public $infNumero;
    /**
     * @var string
     */
    public $infSerie;
    /**
     * @var int
     */
    public $infTipo;
    /**
     * @var DateTime
     */
    public $infDataEmissao;
    /**
     * @var string
     */
    public $infSituacao;
    /**
     * @var int
     */
    public $infIncentivoFiscal;

    /**
     * @var float
     */
    public $infValor;

    /**
     * @var float
     */
    public $infPis;
    /**
     * @var float
     */
    public $infCofins;
    /**
     * @var float
     */
    public $infInss;
    /**
     * @var float
     */
    public $infIr;
    /**
     * @var float
     */
    public $infCsll;

    /**
     * @var float
     */
    public $infBase;

    /**
     * @var float
     */
    public $infRetencaoIss;

    /**
     * @var float
     */
    public $infAliquota;

    /**
     * @var int
     */
    public $infCodigoCnae;

    /**
     * @var string
     */
    public $infDescricaoNF;
    /**
     * @var int
     */
    public $infMunicipioPrestacaoServico;

    /**
     * @var int
     */
    public $infMunicipioIncidencia;

    /**
     * @var int
     */
    public $infPaisPrestacaoServico;

    /**
     * Set informations of provider
     * @param string $ccm
     * @param string $cnpjcpf
     * @param string $cpf_usuario
     * @param string $senha_usuario
     */
    public function prestador($ccm, $cnpjcpf, $cpf_usuario, $senha_usuario)
    {
        $this->infPrestador = [
            "ccm" => $ccm,
            "cnpjcpf" => $cnpjcpf,
            "cpf_usuario" => $cpf_usuario,
            "senha_usuario" => $senha_usuario,
        ];
    }

    /**
     * Set informations of customer
     * @param string $tipo
     * @param string $cnpjcpf
     * @param string $im
     * @param string $ie
     * @param string $razao
     * @param string $tel
     * @param string $ramal
     * @param string $fax
     * @param string $email
     */
    public function tomador($tipo, $cnpjcpf, $im, $ie, $razao, $tel, $ramal, $fax, $email)
    {
        $this->infTomador = [
            'tipo' => $tipo,
            'cnpjcpf' => $cnpjcpf,
            'im' => $im,
            'ie' => $ie,
            'razao' => $razao,
            'tel' => $tel,
            'ramal' => $ramal,
            'fax' => $fax,
            'email' => $email
        ];
    }

    /**
     * Set address of customer
     * @param string $end
     * @param string $numero
     * @param string $complemento
     * @param string $bairro
     * @param int $cmun
     * @param string $uf
     * @param int $cep
     */
    public function tomadorEndereco($end, $numero, $complemento, $bairro, $cmun, $uf, $cep)
    {
        $this->infTomadorEndereco = [
            'end' => $end,
            'numero' => $numero,
            'complemento' => $complemento,
            'bairro' => $bairro,
            'cmun' => $cmun,
            'uf' => $uf,
            'cep' => $cep
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

    /**
     * Set servico conforme Lista (código DMS). 
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function servico($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O serviço não pode ser vazia e deve ter até 11 caracteres.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter até 11 caracteres. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 11)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infServico = $value;
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
            $msg = "A série não pode ser vazia e deve ter até 5 caracteres.";
        } else {
            $msg = "O item '$campo' não pode ser vazio e deve ter até 5 caracteres. Informado: '$value'";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 5)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infSerie = $value;
    }

    /**
     * Set type of RPS
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function tipo($value = self::TIPO_RPS, $campo = null)
    {
        if (!$campo) {
            $msg = "O tipo deve estar entre 1 e 3.";
        } else {
            $msg = "O item '$campo' deve ser um valor inteiro entre 1 e 3. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->between(1, 3)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infTipo = $value;
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
     * Set replaced RPS
     * @param int $nfse
     * @param string $rps
     * @throws InvalidArgumentException
     */
    public function rpsSubstituido($nfse, $rps)
    {
        $this->infRpsSubstituido = [
            'nfse' => $nfse,
            'rps'  => $rps,
        ];
    }

    /**
     * Set type of kind tax operation
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function situacao($value = self::SIT_TRIBUTADA_PRESTADOR, $campo = null)
    {
        if (!$campo) {
            $msg = "A situacao deve ser tp,tt,tf,is,nt,si ou ca.";
        } else {
            $msg = "O item '$campo' deve ser tp,tt,tf,is,nt,si ou ca. Informado: '$value'";
        }

        if (!in_array($value, ['tp', 'tt', 'tf', 'is', 'nt', 'si', 'ca'])) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infSituacao = $value;
    }

    /**
     * Set opting for Simple National tax regime
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function incentivoFiscal($value = self::SIM, $campo = null)
    {
        if (!$campo) {
            $msg = "Optante pelo Simples deve ser 1 ou 2.";
        } else {
            $msg = "O item '$campo' deve ser 1 ou 2. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->between(1, 2)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infIncentivoFiscal = $value;
    }

    /**
     * Set service amount
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valor($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValor = round($value, 2);
    }

    /**
     * Set amount for PIS tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function pis($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infPis = round($value, 2);
    }

    /**
     * Set amount for COFINS tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function cofins($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infCofins = round($value, 2);
    }

    /**
     * Set amount for INSS tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function inss($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infInss = round($value, 2);
    }

    /**
     * Set amount for IR tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function ir($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infIr = round($value, 2);
    }

    /**
     * Set amount for CSLL tax
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function csll($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infCsll = round($value, 2);
    }

    /**
     * Set calculation base value
     * (Valor dos serviços - Valor das deduções - descontos incondicionados)
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function base($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infBase = round($value, 2);
    }

    /**
     * Set calculation base value
     * (Valor dos serviços - Valor das deduções - descontos incondicionados)
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function retencaoIss($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infRetencaoIss = round($value, 2);
    }
    
    /**
     * Set ISS tax aliquot in percent
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function aliquota($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infAliquota = round($value, 4);
    }

    /**
     * Set CNAE code
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function codigoCnae($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O código CNAE é obrigatorio.";
        } else {
            $msg = "O item '$campo' é obrigatório e precisa ser um valor inteiro. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infCodigoCnae = $value;
    }

    /**
     * Set discrimination of service
     * @param string $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function descricaoNF($value, $campo = null)
    {
        if (!$campo) {
            $msg = "A descricaoNF é obrigatória e deve ter no máximo 1400 caracteres.";
        } else {
            $msg = "O item '$campo' é obrigatória e deve ter no máximo 1400 caracteres. Informado: " . strlen($value) . " caracteres";
        }

        $value = trim($value);
        if (!Validator::stringType()->length(1, 2000)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infDescricaoNF = $value;
    }

    /**
     * Set constructions information
     * @param string $codigoObra
     * @param string $art
     * @param string $obra_alvara_numero
     * @param string $obra_alvara_ano
     * @param string $obra_local_lote
     * @param string $obra_local_quadra
     * @param string $obra_local_bairro
     */
    public function construcaoCivil($codigoObra, $art, $obra_alvara_numero = '', 
    $obra_alvara_ano = '', $obra_local_lote = '', $obra_local_quadra = '', $obra_local_bairro = '')
    {
        $this->infConstrucaoCivil = [
            'obra' => $codigoObra,
            'art'  => $art,
            'obra_alvara_numero' => $obra_alvara_numero,
            'obra_alvara_ano'    => $obra_alvara_ano,
            'obra_local_lote'    => $obra_local_lote,
            'obra_local_quadra'  => $obra_local_quadra,
            'obra_local_bairro'  => $obra_local_bairro,
        ];
    }

    /**
     * Set IBGE county code where service was realized
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function municipioPrestacaoServico($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Deve ser passado o código do IBGE.";
        } else {
            $msg = "O item '$campo' deve ser inteiro, referente ao código IBGE. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infMunicipioPrestacaoServico = $value;
    }

    /**
     * Set IBGE county code where service was realized
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function municipioIncidencia($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Deve ser passado o código do IBGE.";
        } else {
            $msg = "O item '$campo' deve ser inteiro, referente ao código IBGE. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infMunicipioIncidencia = $value;
    }

    /**
     * Set BACEN Código do país em que o serviço foi prestado
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function paisPrestacaoServico($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Deve ser passado o código do BACEN do país.";
        } else {
            $msg = "O item '$campo' deve ser inteiro, referente ao código BACEN do país. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infPaisPrestacaoServico = $value;
    }
}
