<?php

namespace NFePHP\NFSe\Models\Abrasf;

/**
 * Classe a construção do xml dos RPS para o modelo ABRASF 2.02
 * ATENÇÃO:
 *  - O modelo ABRASF tem multiplas versões em uso, por vários municipos
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abrasf\Rps
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use DateTime;
use InvalidArgumentException;
use NFePHP\NFSe\Common\Rps as RpsBase;
use Respect\Validation\Validator;


class Rps extends RpsBase
{
    const TIPO_RPS = 1;
    const TIPO_MISTO = 2;
    const TIPO_CUPOM = 3;

    const CPF = 1;
    const CNPJ = 2;

    const STATUS_NORMAL = 1;
    const STATUS_CANCELADO = 2;

    const REGIME_MICROEMPRESA = 1;
    const REGIME_ESTIMATIVA = 2;
    const REGIME_SOCIEDADE = 3;
    const REGIME_COOPERATIVA = 4;

    const NATUREZA_INTERNA = 1; //Tributação no município
    const NATUREZA_EXTERNA = 2;  //Tributação fora do município
    const NATUREZA_ISENTA = 3; //Isenção
    const NATUREZA_IMUNE = 4; //Imune
    const NATUREZA_SUSPENSA_JUS = 5; //Exigibilidade suspensa por decisão judicial
    const NATUREZA_SUSPENSA_ADMIN = 6; //Exigibilidade suspensa por procedimento administrativo

    const SIM = 1;
    const NAO = 2;

    /**
     * @var array
     */
    public $infPrestador = ['tipo' => '', 'cnpjcpf' => '', 'im' => ''];
    /**
     * @var array
     */
    public $infTomador = ['tipo' => '', 'cnpjcpf' => '', 'im' => '', 'razao' => '', 'tel' => '', 'email' => ''];
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
    public $infRpsSubstituido = ['numero' => '', 'serie' => '', 'tipo' => ''];
    /**
     * @var array
     */
    public $infIntermediario = ['tipo' => '', 'cnpjcpf' => '', 'im' => '', 'razao' => ''];
    /**
     * @var array
     */
    public $infConstrucaoCivil = ['obra' => '', 'art' => ''];
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
     * @var int
     */
    public $infNaturezaOperacao;
    /**
     * @var int
     */
    public $infOptanteSimplesNacional;
    /**
     * @var int
     */
    public $infIncentivadorCultural;
    /**
     * @var int
     */
    public $infStatus;
    /**
     * @var int
     */
    public $infRegimeEspecialTributacao;
    /**
     * @var float
     */
    public $infValorServicos;
    /**
     * @var float
     */
    public $infValorDeducoes;
    /**
     * @var float
     */
    public $infOutrasRetencoes;
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
    public $infValorInss;
    /**
     * @var float
     */
    public $infValorIr;
    /**
     * @var float
     */
    public $infValorCsll;
    /**
     * @var int
     */
    public $infIssRetido;
    /**
     * @var float
     */
    public $infValorIss;
    /**
     * @var float
     */
    public $infValorIssRetido;
    /**
     * @var float
     */
    public $infBaseCalculo;
    /**
     * @var float
     */
    public $infAliquota;
    /**
     * @var float
     */
    public $infValorLiquidoNfse;
    /**
     * @var float
     */
    public $infDescontoIncondicionado;
    /**
     * @var float
     */
    public $infDescontoCondicionado;
    /**
     * @var string
     */
    public $infItemListaServico;
    /**
     * @var int
     */
    public $infCodigoCnae;
    /**
     * @var string
     */
    public $infCodigoTributacaoMunicipio;
    /**
     * @var string
     */
    public $infDiscriminacao;
    /**
     * @var int
     */
    public $infMunicipioPrestacaoServico;

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
     * Set informations of customer
     * @param int $tipo
     * @param string $cnpjcpf
     * @param string $im
     * @param string $razao
     * @param string $telefone
     * @param string $email
     */
    public function tomador($tipo, $cnpjcpf, $im, $razao, $telefone, $email)
    {
        $this->infTomador = [
            'tipo' => $tipo,
            'cnpjcpf' => $cnpjcpf,
            'im' => $im,
            'razao' => $razao,
            'tel' => $telefone,
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
     * Set number of RPS
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function numero($value)
    {
        if (!Validator::numeric()->intVal()->positive()->validate($value)) {
            throw new \InvalidArgumentException('O numero do RPS deve ser um inteiro positivo apenas.');
        }
        $this->infNumero = $value;
    }

    /**
     * Set series of RPS
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function serie($value)
    {
        $value = trim($value);
        if (!Validator::stringType()->length(1, 5)->validate($value)) {
            throw new \InvalidArgumentException('A série não pode ser vazia e deve ter até 5 caracteres.');
        }
        $this->infSerie = $value;
    }

    /**
     * Set type of RPS
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function tipo($value = self::TIPO_RPS)
    {
        if (!Validator::numeric()->intVal()->between(1, 3)->validate($value)) {
            throw new \InvalidArgumentException('O tipo deve estar entre 1 e 3.');
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
     * @param int $numero
     * @param string $serie
     * @param int $tipo
     * @throws InvalidArgumentException
     */
    public function rpsSubstituido($numero, $serie, $tipo)
    {
        $this->infRpsSubstituido = [
            'numero' => $numero,
            'serie' => $serie,
            'tipo' => $tipo
        ];
    }

    /**
     * Set type of kind tax operation
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function naturezaOperacao($value = self::NATUREZA_INTERNA)
    {
        if (!Validator::numeric()->intVal()->between(1, 6)->validate($value)) {
            throw new \InvalidArgumentException('A natureza da operação deve estar entre 1 e 6.');
        }
        $this->infNaturezaOperacao = $value;
    }

    /**
     * Set opting for Simple National tax regime
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function optanteSimplesNacional($value = self::SIM)
    {
        if (!Validator::numeric()->intVal()->between(1, 2)->validate($value)) {
            throw new \InvalidArgumentException('Optante pelo Simples deve ser 1 ou 2.');
        }
        $this->infOptanteSimplesNacional = $value;
    }

    /**
     * Set encouraging cultural flag
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function incentivadorCultural($value = self::NAO)
    {
        if (!Validator::numeric()->intVal()->between(1, 2)->validate($value)) {
            throw new \InvalidArgumentException('Incentivador cultural deve ser 1 ou 2.');
        }
        $this->infIncentivadorCultural = $value;
    }

    /**
     * Set RPS status
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function status($value = self::STATUS_NORMAL)
    {
        if (!Validator::numeric()->intVal()->between(1, 2)->validate($value)) {
            throw new \InvalidArgumentException('O status do RPS deve ser 1 ou 2.');
        }
        $this->infStatus = $value;
    }

    /**
     * Set special tax regime
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function regimeEspecialTributacao($value = self::REGIME_MICROEMPRESA)
    {
        if (!Validator::numeric()->intVal()->between(1, 4)->validate($value)) {
            throw new \InvalidArgumentException('O regime de tributação deve estar entre 1 e 4.');
        }
        $this->infRegimeEspecialTributacao = $value;
    }

    /**
     * Set service amount
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorServicos($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorServicos = round($value, 2);
    }

    /**
     * Set other withholdings amount
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function outrasRetencoes($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infOutrasRetencoes = round($value, 2);
    }

    /**
     * Set amount for PIS tax
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorPis($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorPis = round($value, 2);
    }

    /**
     * Set amount for COFINS tax
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorCofins($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorCofins = round($value, 2);
    }

    /**
     * Set amount for INSS tax
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorInss($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorInss = round($value, 2);
    }

    /**
     * Set amount for IR tax
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorIr($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorIr = round($value, 2);
    }

    /**
     * Set amount for CSLL tax
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorCsll($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorCsll = round($value, 2);
    }

    /**
     * Set ISS taxes retention flag
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function issRetido($value = self::NAO)
    {
        if (!Validator::numeric()->intVal()->between(1, 2)->validate($value)) {
            throw new \InvalidArgumentException('IssRetido deve ser 1 ou 2.');
        }
        $this->infIssRetido = $value;
    }

    /**
     * Set amount withheld of ISS
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorIssRetido($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorIssRetido = round($value, 2);
    }

    /**
     * Set amount of ISS
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorIss($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorIss = round($value, 2);
    }

    /**
     * Set calculation base value
     * (Valor dos serviços - Valor das deduções - descontos incondicionados)
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function baseCalculo($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infBaseCalculo = round($value, 2);
    }

    /**
     * Set ISS tax aliquot in percent
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function aliquota($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infAliquota = round($value, 4);
    }

    /**
     * Set deductions amount
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function valorDeducoes($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorDeducoes = round($value, 2);
    }

    /**
     * Set net amount
     * (ValorServicos - ValorPIS - ValorCOFINS - ValorINSS
     * - ValorIR - ValorCSLL - OutrasRetençoes - ValorISSRetido
     * - DescontoIncondicionado - DescontoCondicionado)
     * @param type $value
     * @throws InvalidArgumentException
     */
    public function valorLiquidoNfse($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infValorLiquidoNfse = round($value, 2);
    }

    /**
     * Set inconditioning off amount
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function descontoIncondicionado($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infDescontoIncondicionado = round($value, 2);
    }

    /**
     * Set conditioning off amount
     * @param float $value
     * @throws InvalidArgumentException
     */
    public function descontoCondicionado($value = 0.00)
    {
        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException('Os valores deve ser numericos tipo float.');
        }
        $this->infDescontoCondicionado = round($value, 2);
    }

    /**
     * Set Services List item
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function itemListaServico($value)
    {
        $value = trim($value);
        if (!Validator::stringType()->length(1, 5)->validate($value)) {
            throw new \InvalidArgumentException('O item da lista é obrigatório e'
                . ' deve ter no máximo 5 caracteres.');
        }
        $this->infItemListaServico = $value;
    }

    /**
     * Set CNAE code
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function codigoCnae($value)
    {
        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException('O código CNAE é obrigatorio.');
        }
        $this->infCodigoCnae = $value;
    }

    /**
     * Set tax code from county
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function codigoTributacaoMunicipio($value)
    {
        $value = trim($value);
        if (!Validator::stringType()->length(1, 20)->validate($value)) {
            throw new \InvalidArgumentException('O codigo de tributação é obrigatório e deve ter '
                . 'no máximo 20 caracteres.');
        }
        $this->infCodigoTributacaoMunicipio = $value;
    }

    /**
     * Set discrimination of service
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function discriminacao($value)
    {
        $value = trim($value);
        if (!Validator::stringType()->length(1, 2000)->validate($value)) {
            throw new \InvalidArgumentException('A discriminação é obrigatória e deve ter '
                . 'no máximo 2000 caracteres.');
        }
        $this->infDiscriminacao = $value;
    }

    /**
     * Set constructions information
     * @param string $codigoObra
     * @param string $art
     */
    public function construcaoCivil($codigoObra, $art)
    {
        $this->infConstrucaoCivil = ['obra' => $codigoObra, 'art' => $art];
    }

    /**
     * Set IBGE county code where service was realized
     * @param int $value
     * @throws InvalidArgumentException
     */
    public function municipioPrestacaoServico($value)
    {
        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException('Deve ser passado o código do IBGE.');
        }
        $this->infMunicipioPrestacaoServico = $value;
    }
}
