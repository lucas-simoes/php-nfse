<?php

namespace NFePHP\NFSe\Models\Prodam;

/**
 * Classe a montagem do RPS para a Cidade de São Paulo
 * conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Rps
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
    public $versaoRPS = '';
    public $prestadorIM = '';
    public $serieRPS = '';
    public $numeroRPS = '';
    public $dtEmiRPS = '';
    public $tipoRPS = '';
    public $statusRPS = '';
    public $tributacaoRPS = '';
    public $valorServicosRPS = '';
    public $valorDeducoesRPS = '';
    public $valorPISRPS = '';
    public $valorCOFINSRPS = '';
    public $valorINSSRPS = '';
    public $valorIRRPS = '';
    public $valorCSLLRPS = '';
    public $valorCargaTributariaRPS = '';
    public $percentualCargaTributariaRPS = '';
    public $fonteCargaTributariaRPS = '';
    public $codigoCEIRPS = '';
    public $matriculaObraRPS = '';
    public $municipioPrestacaoRPS = '';
    public $codigoServicoRPS = '';
    public $aliquotaServicosRPS = '';
    public $issRetidoRPS = false;
    public $discriminacaoRPS = '';
    public $tomadorTipoDoc = '2';
    public $tomadorCNPJCPF = '';
    public $tomadorIE = '';
    public $tomadorIM = '';
    public $tomadorRazao = '';
    public $tomadorTipoLogradouro = '';
    public $tomadorLogradouro = '';
    public $tomadorNumeroEndereco = '';
    public $tomadorComplementoEndereco = '';
    public $tomadorBairro = '';
    public $tomadorCodCidade = '';
    public $tomadorSiglaUF = '';
    public $tomadorCEP = '';
    public $tomadorEmail = '';
    public $intermediarioTipoDoc = '3';
    public $intermediarioCNPJCPF = '';
    public $intermediarioIM = '';
    public $intermediarioISSRetido = 'N';
    public $intermediarioEmail = '';

    private $aTp = [
        'RPS' => 'Recibo Provisório de Serviços',
        'RPS-M' => 'Recibo Provisório de Serviços proveniente de Nota Fiscal Conjugada (Mista)',
        'RPS-C' => 'Cupom'
    ];

    private $aTrib = [
        'T' => 'Tributado em São Paulo',
        'F' => 'Tributado Fora de São Paulo',
        'A' => 'Tributado em São Paulo, porém Isento',
        'B' => 'Tributado Fora de São Paulo, porém Isento',
        'M' => 'Tributado em São Paulo, porém Imune',
        'N' => 'Tributado Fora de São Paulo, porém Imune',
        'X' => 'Tributado em São Paulo, porém Exigibilidade Suspensa',
        'V' => 'Tributado Fora de São Paulo, porém Exigibilidade Suspensa',
        'P' => 'Exportação de Serviços'
    ];

    /**
     * Inscrição Municipal do Prestador do Serviço
     * @param string $im
     */
    public function prestador($im)
    {
        $this->prestadorIM = $im;
    }

    /**
     * Dados do Tomador do Serviço
     * @param string $razao
     * @param string $tipo
     * @param string $cnpjcpf
     * @param string $ie
     * @param string $im
     * @param string $email
     */
    public function tomador(
        $razao,
        $tipo,
        $cnpjcpf,
        $ie,
        $im,
        $email
    ) {
        $this->tomadorRazao = Strings::replaceSpecialsChars($razao);
        $this->tomadorTipoDoc = $tipo;
        if ($tipo == '2') {
            $cnpjcpf = str_pad($cnpjcpf, 14, '0', STR_PAD_LEFT);
        }
        $this->tomadorCNPJCPF = $cnpjcpf;
        $this->tomadorIE = $ie;
        $this->tomadorIM = $im;
        $this->tomadorEmail = $email;
    }

    /**
     * Endereço do Tomador do serviço
     * @param string $tipo
     * @param string $logradouro
     * @param string $numero
     * @param string $complemento
     * @param string $bairro
     * @param string $cmun
     * @param string $uf
     * @param string $cep
     */
    public function tomadorEndereco(
        $tipo,
        $logradouro,
        $numero,
        $complemento,
        $bairro,
        $cmun,
        $uf,
        $cep
    ) {
        $this->tomadorTipoLogradouro = $tipo;
        $this->tomadorLogradouro = Strings::replaceSpecialsChars($logradouro);
        $this->tomadorNumeroEndereco = $numero;
        $this->tomadorComplementoEndereco = Strings::replaceSpecialsChars($complemento);
        $this->tomadorBairro = Strings::replaceSpecialsChars($bairro);
        $this->tomadorCodCidade = $cmun;
        $this->tomadorSiglaUF = $uf;
        $this->tomadorCEP = $cep;
    }

    /**
     * Dados do intermediário
     * @param string $tipo
     * @param string $cnpj
     * @param string $im
     * @param string $email
     */
    public function intermediario(
        $tipo,
        $cnpj,
        $im,
        $email
    ) {
        $this->intermediarioTipoDoc = $tipo;
        $this->intermediarioCNPJCPF = $cnpj;
        $this->intermediarioIM = $im;
        $this->intermediarioEmail = strtolower($email);
    }

    /**
     * Versão do layout usado 1 ou 2
     * @param string $versao
     */
    public function versao($versao)
    {
        $versao = preg_replace('/[^0-9]/', '', $versao);
        $this->versaoRPS = $versao;
    }

    /**
     * Série do RPS
     * @param string $serie
     */
    public function serie($serie)
    {
        $serie = substr(trim($serie), 0, 5);
        $this->serieRPS = $serie;
    }

    /**
     * Numero do RPS
     * @param string $numero
     * @throws InvalidArgumentException
     */
    public function numero($numero)
    {
        if (!is_numeric($numero) || $numero <= 0) {
            $msg = "[$numero] não é aceito. O numero do RPS deve ser numerico maior ou igual a 1";
            throw new InvalidArgumentException($msg);
        }
        $this->numeroRPS = $numero;
    }

    /**
     * Data do RPS
     * @param string $data
     */
    public function data($data)
    {
        $this->dtEmiRPS = $data;
    }

    /**
     * Status do RPS Normal ou Cancelado
     * @param string $status
     * @throws InvalidArgumentException
     */
    public function status($status)
    {
        $status = strtoupper(trim($status));
        if (!$this->validData(['N' => 0, 'C' => 1], $status)) {
            $msg = 'O status pode ser apenas N-normal ou C-cancelado.';
            throw new InvalidArgumentException($msg);
        }
        $this->statusRPS = $status;
    }

    /**
     * Tipo do RPS
     * RPS – Recibo Provisório de Serviços
     * RPS-M – Recibo Provisório de Serviços proveniente de Nota Fiscal Conjugada (Mista);
     * RPS-C – Cupom
     *
     * @param string $tipo
     */
    public function tipo($tipo)
    {
        $tipo = strtoupper(trim($tipo));
        if (!$this->validData($this->aTp, $tipo)) {
            $msg = "[$tipo] não é um codigo valido entre " . implode(',', array_keys($this->aTp)) . ".";
            throw new InvalidArgumentException($msg);
        }
        $this->tipoRPS = $tipo;
    }

    /**
     * Tributação
     * T – Tributado em São Paulo
     * F – Tributado Fora de São Paulo
     * A – Tributado em São Paulo, porém Isento
     * B – Tributado Fora de São Paulo, porém Isento
     * M – Tributado em São Paulo, porém Imune
     * N – Tributado Fora de São Paulo, porém Imune
     * X – Tributado em São Paulo, porém Exigibilidade Suspensa
     * V – Tributado Fora de São Paulo, porém Exigibilidade Suspensa
     * P – Exportação de Serviços
     *
     * @param string $tributacao
     */
    public function tributacao($tributacao)
    {
        $tributacao = strtoupper(trim($tributacao));
        if (!$this->validData($this->aTrib, $tributacao)) {
            $msg = "[$tributacao] não é um código válido, ente" . implode(',', array_keys($this->aTrib));
            throw new InvalidArgumentException($msg);
        }
        $this->tributacaoRPS = $tributacao;
    }

    /**
     * Código do serviço prestado
     * @param string $cod
     */
    public function codigoServico($cod = '')
    {
        $this->codigoServicoRPS = str_pad($cod, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Valor dos Serviços prestados
     * @param float $valor
     */
    public function valorServicos($valor)
    {
        $this->valorServicosRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Valor das deduções aplicáveis ao serviço
     * @param float $valor
     */
    public function valorDeducoes($valor)
    {
        $this->valorDeducoesRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Aliquota do ISS do serviço
     * @param float $valor
     * @throws InvalidArgumentException
     */
    public function aliquotaServico($valor)
    {
        if ($valor > 1 || $valor < 0) {
            $msg = 'Voce deve indicar uma aliquota em fração ex. 0.12.';
            throw new InvalidArgumentException($msg);
        }
        $this->aliquotaServicosRPS = number_format($valor, 4, '.', '');
    }

    /**
     * Indicador de retenção de ISS
     * 1 - iss retido pelo tomador
     * 2 - sem retenção
     * 3 - iss retido pelo intermediário
     * @param integer $flag
     */
    public function issRetido($flag)
    {
        $this->issRetidoRPS = false;
        $this->intermediarioISSRetido = 'N';
        if ($flag == 1) {
            $this->issRetidoRPS = true;
        } elseif ($flag == 3) {
            $this->issRetidoRPS = true;
            $this->intermediarioISSRetido = 'S';
        }
    }

    /**
     * Discriminação do serviço prestado
     * @param string $desc
     */
    public function discriminacao($desc)
    {
        $this->discriminacaoRPS = Strings::replaceSpecialsChars(trim($desc));
    }

    /**
     * Carga tributária total estimada
     * Dados normalmente obtidos no IBPT
     * @param float $valor
     * @param float $percentual
     * @param string $fonte
     */
    public function cargaTributaria($valor, $percentual, $fonte)
    {
        $this->valorCargaTributariaRPS = number_format($valor, 2, '.', '');
        $this->percentualCargaTributariaRPS = number_format($percentual, 4, '.', '');
        $this->fonteCargaTributariaRPS = substr(Strings::replaceSpecialsChars($fonte), 0, 10);
    }

    /**
     * Valor referente ao recolhimento do PIS
     * @param float $valor
     */
    public function valorPIS($valor)
    {
        $this->valorPISRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Valor referente ao recolhimento da COFINS
     * @param float $valor
     */
    public function valorCOFINS($valor)
    {
        $this->valorCOFINSRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Valor referente ao recolhimento da contribuição ao INSS
     * @param float $valor
     */
    public function valorINSS($valor)
    {
        $this->valorINSSRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Valor refenrente ao IR (Imposto de Renda)
     * @param float $valor
     */
    public function valorIR($valor)
    {
        $this->valorIRRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Valor referente a CSLL (contribuição Sobre o Lucro Líquido)
     * @param float $valor
     */
    public function valorCSLL($valor)
    {
        $this->valorCSLLRPS = number_format($valor, 2, '.', '');
    }

    /**
     * Código Matricula no CEI (Cadastro Especifico do INSS)
     * @param string $cod
     */
    public function codigoCEI($cod)
    {
        $this->codigoCEIRPS = $cod;
    }

    /**
     * Identificaçao ou número de matricula da Obra Civil
     * @param string $matricula
     */
    public function matriculaObra($matricula)
    {
        $this->matriculaObraRPS = $matricula;
    }

    /**
     * Código IBGE para o municio onde o serviço
     * foi prestado
     * @param string $cmun
     */
    public function municipioPrestacao($cmun)
    {
        $this->municipioPrestacaoRPS = $cmun;
    }
}
