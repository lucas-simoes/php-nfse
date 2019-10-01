<?php

namespace NFePHP\NFSe\Models\Abrasf;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abrasf\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Tools as ToolsBase;
use NFePHP\NFSe\Models\Abrasf\Factories;


class Tools extends ToolsBase
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';
    protected $schemeFolder = 'Abrasf';
    protected $params = [];

    public function cancelarNfse()
    {
    }

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Abrasf\\Factories\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);
        return $this->consultarLoteRpsCommon($fact, $protocolo);
    }

    /**
     * @param $fact
     * @param $protocolo
     * @param string $url
     * @return string
     */
    protected function consultarLoteRpsCommon($fact, $protocolo, $url = '')
    {
        $this->method = 'ConsultarLoteRps';
        $fact->setXmlns($this->xmlns);
        $message = $fact->render($this->remetenteCNPJCPF, $this->remetenteIM, $protocolo);
        return $this->sendRequest($url, $message);

    }

    /**
     * Monta o request da mensagem SOAP
     * @param string $url
     * @param string $message
     * @return string
     */
    protected function sendRequest($url, $message)
    {
        //Abrasf possui apenas uma URL
        if (!$url) {
            $url = $this->url[$this->config->tpAmb];
        }

        if (!is_object($this->soap)) {
            $this->soap = new \NFePHP\NFSe\Common\SoapCurl($this->certificate);
        }
        //formata o xml da mensagem para o padão esperado pelo webservice
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        $messageText = $message;
        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }
        $request = $this->makeRequest($messageText);
        if (!count($this->params)) {
            $this->params = [
                "Content-Type: text/xml;charset=utf-8;",
                "SOAPAction: \"http://www.e-governeapps2.com.br/{$this->method}\""
            ];
        }

        $action = '';

        //Realiza o request SOAP
        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $this->params,
            $this->namespaces[$this->soapversion],
            $request
        );
    }

    /**
     * @param $message
     * @return string
     */
    protected function makeRequest($message)
    {
        $versao = '2.02';
        switch ($this->versao) {
            case 100:
                $request = "<{$this->method} xmlns=\"http://www.e-governeapps2.com.br/\">"
                    . $message
                    . "</{$this->method}>";
                break;
            case 201:
                $versao = '2.01';
            case 202:
                $request =
                    "<e:{$this->method}>"
                    . "<nfseCabecMsg>"
                    . "<![CDATA["
                    . "<cabecalho xmlns=\"{$this->xmlns}\" versao=\"{$versao}\"><versaoDados>{$versao}</versaoDados></cabecalho>"
                    . "]]>"
                    . "</nfseCabecMsg>"
                    . "<nfseDadosMsg>"
                    . "<![CDATA["
                    . $message
                    . "]]>"
                    . "</nfseDadosMsg>"
                    . "</e:{$this->method}>";
                break;
            default:
                throw new \LogicException('Versão não suportada');
        }
        return $request;
    }

    public function consultarNfsePorFaixa()
    {
    }

    public function consultarNfsePorRps()
    {
    }

    public function consultarNfseServicoPrestado()
    {
    }

    public function consultarNfseServicoTomado()
    {
    }

    /**
     * @param $rps
     * @return string
     */
    public function gerarNfse($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\Abrasf\\Factories\\v{$this->versao}\\GerarNfse";
        $fact = new $class($this->certificate);
        return $this->gerarNfseCommon($fact, $rps);
    }

    /**
     * @param Factories\GerarNfse $fact
     * @param $rps
     * @param string $url
     * @return string
     */
    protected function gerarNfseCommon(Factories\GerarNfse $fact, $rps, $url = '')
    {
        $this->method = 'GerarNfse';
        $fact->setXmlns($this->xmlns);
        $fact->setSchemeFolder($this->schemeFolder);
        $fact->setCodMun($this->config->cmun);
        $fact->setSignAlgorithm($this->algorithm);
        $fact->setTimezone($this->timezone);

        //O webservice de Goiania quando em modo de produção, você pode testar as NFSe
        //Enviando a série TESTE, logo, está enviando para todos os provedores a Série TESTE por Default
        // https://docs.google.com/document/d/1B6L11ZGv2iXMfxCtIJxgzLaDCyeF-tCJ82ELysnJaTs/edit?pli=1
        if ($this->config->tpAmb == 2) {
            $rps->infSerie = 'TESTE';
        }

        $message = $fact->render(
            $this->versao,
            $rps
        );

        return $this->sendRequest($url, $message);
    }

    /**
     * @param $lote
     * @param $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Abrasf\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);

        return $this->recepcionarLoteRpsCommon($fact, $lote, $rpss);
    }

    /**
     * @param Factories\RecepcionarLoteRps $fact
     * @param $lote
     * @param $rpss
     * @param string $url
     * @return string
     */
    protected function recepcionarLoteRpsCommon(Factories\RecepcionarLoteRps $fact, $lote, $rpss, $url = '')
    {
        $this->method = 'RecepcionarLoteRps';
        $fact->setXmlns($this->xmlns);
        $fact->setSchemeFolder($this->schemeFolder);
        $fact->setCodMun($this->config->cmun);
        $fact->setSignAlgorithm($this->algorithm);
        $fact->setTimezone($this->timezone);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $lote,
            $rpss
        );

        // @header ("Content-Disposition: attachment; filename=\"NFSe_Lote.xml\"" );
        // echo $message;
        // exit;
        return $this->sendRequest($url, $message);

    }

    public function recepcionarLoteRpsSincrono()
    {
    }

    public function substituirNfse()
    {
    }
}
