<?php

namespace NFePHP\NFSe\Models\Dsfnet;

/**
 * Classe a construção do xml da NFSe
 * conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Rps
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use InvalidArgumentException;
use NFePHP\Common\Strings;
use NFePHP\NFSe\Common\Rps as RpsBase;

class Rps extends RpsBase
{
    public $versaoRPS = '1';
    public $tipoRPS = 'RPS'; //Padrão "RPS"
    public $serieRPS = 'NF';//Padrão "NF"
    public $numeroRPS = '';
    public $dataEmissaoRPS = '';//Y-m-dTH:i:s
    public $situacaoRPS = 'N'; //Situação da RPS "N"-Normal "C"-Cancelada
    public $seriePrestacao = '99'; //preencha o campo com o valor '99'

    public $inscricaoMunicipalPrestador = '';
    public $razaoSocialPrestador = '';
    public $dDDPrestador = '';
    public $telefonePrestador = '';

    public $serieRPSSubstituido = '';
    public $numeroRPSSubstituido = '';
    public $numeroNFSeSubstituida = '';
    public $dataEmissaoNFSeSubstituida = '1900-01-01'; //Preencher com "1900-01-01"

    public $inscricaoMunicipalTomador = '';
    public $cPFCNPJTomador = '';
    public $razaoSocialTomador = '';
    public $docTomadorEstrangeiro = '';
    public $dDDTomador = '';
    public $telefoneTomador = '';

    public $tipoLogradouroTomador = '';
    public $logradouroTomador = '';
    public $numeroEnderecoTomador = '';
    public $complementoTomador = '';
    public $tipoBairroTomador = '';
    public $bairroTomador = '';
    public $cidadeTomador = ''; //Código da Cidade do Tomador - Padrão SIAF
    public $cidadeTomadorDescricao = '';
    public $cEPTomador = '';
    public $emailTomador = '';

    public $itens = [];

    public $codigoAtividade = '';
    public $aliquotaAtividade = '';
    public $tipoRecolhimento = '';
    public $municipioPrestacao = '';
    public $municipioPrestacaoDescricao = '';
    public $operacao = '';

    public $tributacao = '';
    public $valorPIS = 0;
    public $valorCOFINS = 0;
    public $valorINSS = 0;
    public $valorIR = 0;
    public $valorCSLL = 0;
    public $aliquotaPIS = 0;
    public $aliquotaCOFINS = 0;
    public $aliquotaINSS = 0;
    public $aliquotaIR = 0;
    public $aliquotaCSLL = 0;
    public $descricaoRPS = '';

    public $motCancelamento = '';

    public $cpfCnpjIntermediario = '';

    public $deducoes = [];

    protected $aTributacao = [
        'C' => 'Isenta de ISS',
        'E' => 'Não Incidência no Município',
        'F' => 'Imune',
        'K' => 'Exigibilidade Susp.Dec.J/Proc.A',
        'N' => 'Não Tributável',
        'T' => 'Tributável',
        'G' => 'Tributável Fixo',
        'H' => 'Tributável S.N.',
        'M' => 'Micro Empreendedor Individual (MEI).'
    ];

    protected $aOperacao = [
        'A' => 'Sem Dedução',
        'B' => 'Com Dedução/Materiais',
        'C' => 'Imune/Isenta de ISSQN',
        'D' => 'Devolução / Simples Remessa',
        'J' => 'Intermediação*'
    ];

    protected $aTipoRecolhimento = [
        "A" => 'A Recolher',
        "R" => 'Retido na Fonte'
    ];

    /**
     * Versão do layout usado 1
     * @param int $versao
     */
    public function versaoRPS($versao)
    {
        $versao = preg_replace('/[^0-9]/', '', $versao);
        $this->versaoRPS = $versao;
    }

    /**
     * Tipo do RPS
     * RPS – Recibo Provisório de Serviços
     * @param string $tipo
     */
    public function tipoRPS($tipo)
    {
        $this->tipoRPS = $tipo;
    }

    /**
     * Série do RPS
     * @param string $serie
     */
    public function serie($serie)
    {
        $this->serieRPS = $serie;
    }

    /**
     * Numero do RPS
     * @param int $numero
     * @throws InvalidArgumentException
     */
    public function numero($numero)
    {
        if (!is_numeric($numero) || $numero <= 0) {
            $msg = 'O numero do RPS deve ser maior ou igual a 1';
            throw new InvalidArgumentException($msg);
        }
        $this->numeroRPS = $numero;
    }

    /**
     * Data do RPS
     * Formato YYYY-mm-ddTHH:ii:ss
     * @param datetime $data
     */
    public function data($data)
    {
        $dt = new \DateTime($data);
        $dtf = $dt->format('Y-m-d\TH:i:s');
        $this->dataEmissaoRPS = $dtf;
    }

    /**
     * Status do RPS Normal ou Cancelado
     * @param string $status
     * @throws InvalidArgumentException
     */
    public function situacao($status)
    {
        if (!$this->validData(['N' => 0, 'C' => 1], $status)) {
            $msg = 'O status pode ser apenas N-normal ou C-cancelado.';
            throw new InvalidArgumentException($msg);
        }
        $this->situacaoRPS = $status;
    }

    public function prestador($im, $razao, $ddd, $telefone)
    {
        $this->inscricaoMunicipalPrestador = $im;
        $this->razaoSocialPrestador = Strings::replaceSpecialsChars($razao);
        $this->dDDPrestador = $ddd;
        $this->telefonePrestador = $telefone;
    }

    public function substituido($serieRPS, $numeroRPS, $numeroNFSe, $dataNFSe)
    {
        $this->serieRPSSubstituido = $serieRPS;
        $this->numeroRPSSubstituido = $numeroRPS;
        $this->numeroNFSeSubstituida = $numeroNFSe;
        $dt = new \DateTime($dataNFSe);
        $this->dataEmissaoNFSeSubstituida = $dt->format('Y-m-d');
    }

    public function seriePrestacao($serie)
    {
        $this->seriePrestacao = $serie;
    }

    public function tomador(
        $im,
        $cpfcnpj,
        $razao,
        $docEstrangeiro,
        $ddd,
        $telefone
    ) {
        $this->inscricaoMunicipalTomador = $im;
        $this->cPFCNPJTomador = $cpfcnpj;
        $this->razaoSocialTomador = Strings::replaceSpecialsChars($razao);
        $this->docTomadorEstrangeiro = $docEstrangeiro;
        $this->dDDTomador = $ddd;
        $this->telefoneTomador = $telefone;
    }

    public function tomadorEndereco(
        $tipoLogradouro,
        $logradouro,
        $numero,
        $complemento,
        $tipoBairro,
        $bairro,
        $codigoSIAF,
        $cidade,
        $cep,
        $email
    ) {
        $this->tipoLogradouroTomador = Strings::replaceSpecialsChars($tipoLogradouro);
        $this->logradouroTomador = Strings::replaceSpecialsChars($logradouro);
        $this->numeroEnderecoTomador = $numero;
        $this->complementoTomador = Strings::replaceSpecialsChars($complemento);
        $this->tipoBairroTomador = Strings::replaceSpecialsChars($tipoBairro);
        $this->bairroTomador = Strings::replaceSpecialsChars($bairro);
        $this->cidadeTomador = str_pad($codigoSIAF, 7, '0', STR_PAD_LEFT);
        $this->cidadeTomadorDescricao = Strings::replaceSpecialsChars($cidade);
        $this->cEPTomador = $cep;
        $this->emailTomador = strtolower($email);
    }

    public function itemServico(
        $discriminacao,
        $quantidade,
        $valorUnitario,
        $valorTotal,
        $tributavel
    ) {
        $this->itens[] = [
            'DiscriminacaoServico' => Strings::replaceSpecialsChars($discriminacao),
            'Quantidade' => $quantidade,
            'ValorUnitario' => $valorUnitario,
            'ValorTotal' => $valorTotal,
            'Tributavel' => $tributavel
        ];
    }

    public function operacaoRPS($operacao)
    {
        if (!$this->validData($this->aOperacao, $operacao)) {
            $msg = "[$operacao] não é válido, pode ser apenas " . implode(',', array_keys($this->aOperacao)) . ".";
            throw new InvalidArgumentException($msg);
        }
        $this->operacao = $operacao;
    }

    public function descricao($descricao)
    {
        $this->descricaoRPS = Strings::replaceSpecialsChars($descricao);
    }

    public function codigoAtividadeRPS($codigo, $aliquota)
    {
        $this->codigoAtividade = $codigo;
        $this->aliquotaAtividade = $aliquota;
    }

    public function recolhimento($tipo)
    {
        $this->tipoRecolhimento = $tipo;
    }

    public function localPrestacao($codmunicipio, $municipio)
    {
        $this->municipioPrestacao = $codmunicipio;
        $this->municipioPrestacaoDescricao = Strings::replaceSpecialsChars($municipio);
    }

    public function tributacaoServico(
        $tributacao,
        $valorPIS,
        $valorCOFINS,
        $valorINSS,
        $valorIR,
        $valorCSLL,
        $aliquotaPIS,
        $aliquotaCOFINS,
        $aliquotaINSS,
        $aliquotaIR,
        $aliquotaCSLL
    ) {
        $this->tributacao = $tributacao;
        $this->valorPIS = $valorPIS;
        $this->valorCOFINS = $valorCOFINS;
        $this->valorINSS = $valorINSS;
        $this->valorIR = $valorIR;
        $this->valorCSLL = $valorCSLL;
        $this->aliquotaPIS = $aliquotaPIS;
        $this->aliquotaCOFINS = $aliquotaCOFINS;
        $this->aliquotaINSS = $aliquotaINSS;
        $this->aliquotaIR = $aliquotaIR;
        $this->aliquotaCSLL = $aliquotaCSLL;
    }

    public function cancelamento($motivo)
    {
        $this->motCancelamento = Strings::replaceSpecialsChars($motivo);
    }

    public function intermediario($cpfcnpj)
    {
        $this->cpfCnpjIntermediario = $cpfcnpj;
    }

    public function deducao(
        $deducaoPor,
        $tipoDeducao,
        $cpfcnpjReferencia,
        $numeroNFReferencia,
        $valorTotalReferencia,
        $percentualDeduzir,
        $valorDeduzir
    ) {
        $this->deducoes[] = [
            'DeducaoPor' => $deducaoPor,
            'TipoDeducao' => $tipoDeducao,
            'CPFCNPJReferencia' => $cpfcnpjReferencia,
            'NumeroNFReferencia' => $numeroNFReferencia,
            'ValorTotalReferencia' => $valorTotalReferencia,
            'PercentualDeduzir' => $percentualDeduzir,
            'ValorDeduzir' => $valorDeduzir
        ];
    }
}
