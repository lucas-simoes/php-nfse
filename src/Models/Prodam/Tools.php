<?php

namespace NFePHP\NFSe\Models\Prodam;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo PRODAM
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFSe\Common\Tools as ToolsBase;
use NFePHP\NFSe\Models\Prodam\Factories;

class Tools extends ToolsBase
{
    /**
     * Envio de apenas um RPS
     * @param \NFePHP\NFSe\Models\Prodam\RPS $rps
     * @return string
     */
    public function envioRPS(RPS $rps)
    {
        $this->method = 'EnvioRPS';
        $fact = new Factories\EnvioRPS($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            $rps
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Send request to webservice
     * @param string $message
     * @return string
     */
    protected function sendRequest($url, $message)
    {
        //no caso da Prodam o URL é unico para todas as ações
        $url = $this->url[$this->config->tpAmb];
        //o ambiente de testes da Prodam não FUNCIONA!!
        if ($this->config->tpAmb == 2) {
            $this->soapversion = SOAP_1_1;
        }
        if (!is_object($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        //para usar o cURL quando está estabelecido o uso do CData na estrutura
        //do xml, terá de haver uma transformação, porém no caso do SoapNative isso
        //não é necessário, pois o próprio SoapClient faz essas transformações,
        //baseado no WSDL.
        $messageText = $message;
        if ($this->withcdata) {
            $messageText = $this->stringTransform("<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . $message);
        }
        $request = "<" . $this->method . "Request xmlns=\"" . $this->xmlns . "\">"
            . "<VersaoSchema>$this->versao</VersaoSchema>"
            . "<MensagemXML>$messageText</MensagemXML>"
            . "</" . $this->method . "Request>";
        $params = [
            'VersaoSchema' => $this->versao,
            'MensagemXML' => $message
        ];
        $action = "\"http://www.prefeitura.sp.gov.br/nfe/ws/" . lcfirst($this->method) . "\"";
        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $params,
            $this->namespaces[$this->soapversion],
            $request
        );
    }

    /**
     * Envio de lote de RPS
     * @param array $rpss
     */
    public function envioLoteRPS(array $rpss)
    {
        $this->method = 'EnvioLoteRPS';
        $fact = new Factories\EnvioRPS($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            'true',
            $rpss
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Pedido de teste de envio de lote
     * @param array $rpss
     */
    public function testeEnvioLoteRPS(array $rpss)
    {
        $this->method = 'TesteEnvioLoteRPS';
        $fact = new Factories\TesteEnvioLoteRPS($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            'true',
            $rpss
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta as NFSe e/ou RPS
     * @param array $chavesNFSe array(array('prestadorIM'=>'', 'numeroNFSe'=>''))
     * @param array $chavesRPS array(array('prestadorIM'=>'', 'serieRPS'=>'', 'numeroRPS'=>''))
     */
    public function consultaNFSe(array $chavesNFSe = [], array $chavesRPS = [])
    {
        $this->method = 'ConsultaNFe';
        $fact = new Factories\ConsultaNFSe($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            $chavesNFSe,
            $chavesRPS
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta as NFSe Recebidas pelo Tomador no periodo
     * @param string $cnpjTomador
     * @param string $cpfTomador
     * @param string $imTomador
     * @param string $dtInicio
     * @param string $dtFim
     * @param string $pagina
     */
    public function consultaNFSeRecebidas(
        $cnpjTomador,
        $cpfTomador,
        $imTomador,
        $dtInicio,
        $dtFim,
        $pagina
    ) {
        $this->method = 'ConsultaNFeRecebidas';
        $fact = new Factories\ConsultaNFSePeriodo($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            $cnpjTomador,
            $cpfTomador,
            $imTomador,
            $dtInicio,
            $dtFim,
            $pagina
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta das NFSe emitidas pelo prestador no período
     * @param string $cnpjPrestador
     * @param string $cpfPrestador
     * @param string $imPrestador
     * @param string $dtInicio
     * @param string $dtFim
     * @param string $pagina
     */
    public function consultaNFSeEmitidas(
        $cnpjPrestador,
        $cpfPrestador,
        $imPrestador,
        $dtInicio,
        $dtFim,
        $pagina
    ) {
        $this->method = 'ConsultaNFeEmitidas';
        $fact = new Factories\ConsultaNFSePeriodo($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            $cnpjPrestador,
            $cpfPrestador,
            $imPrestador,
            $dtInicio,
            $dtFim,
            $pagina
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta Lote
     * @param string $numeroLote
     */
    public function consultaLote($numeroLote)
    {
        $this->method = 'ConsultaLote';
        $fact = new Factories\ConsultaLote($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            $numeroLote
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Pedido de informações de Lote
     * @param string $prestadorIM
     * @param string $numeroLote
     */
    public function consultaInformacoesLote($prestadorIM, $numeroLote)
    {
        $this->method = 'ConsultaInformacoesLote';
        $fact = new Factories\ConsultaInformacoesLote($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            $prestadorIM,
            $numeroLote
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Solicita cancelamento da NFSe
     * @param string $prestadorIM
     * @param string $numeroNFSe
     */
    public function cancelamentoNFSe($prestadorIM, $numeroNFSe)
    {
        $this->method = 'CancelamentoNFe';
        $fact = new Factories\CancelamentoNFSe($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            'true',
            $prestadorIM,
            $numeroNFSe
        );
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta CNPJ de contribuinte do ISS
     * @param string $cnpjContribuinte
     * @return string
     */
    public function consultaCNPJ($cnpjContribuinte)
    {
        $this->method = 'ConsultaCNPJ';
        $fact = new Factories\ConsultaCNPJ($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            null,
            str_pad($cnpjContribuinte, 14, '0', STR_PAD_LEFT)
        );
        return $this->sendRequest('', $message);
    }
}
