<?php

namespace NFePHP\NFSe\Models\Dsfnet;

/**
 * Classe para a comunicação com os webservices da
 * conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Tools as ToolsBase;
use NFePHP\NFSe\Models\Dsfnet\Factories;

class Tools extends ToolsBase
{
    /**
     * Solicita o cancelamento
     * @param string $prestadorIM
     * @param string $numeroLote
     * @param int $numeroNota
     * @param string $codigoVerificacao
     * @param string $motivo
     * @param string $tokenEnvio
     * @return string
     */
    public function cancelar($prestadorIM, $numeroLote, $numeroNota, $codigoVerificacao, $motivo, $tokenEnvio = null)
    {
        $this->method = 'cancelar';
        $fact = new Factories\Cancelar($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            'true',
            $this->codcidade,
            $prestadorIM,
            $tokenEnvio,
            $numeroLote,
            $numeroNota,
            $codigoVerificacao,
            $motivo
        );
        return $this->sendRequest('', $xml);
    }

    /**
     * Monta o request da mensagem SOAP
     * @param string $url
     * @param string $message
     * @return string
     */
    protected function sendRequest($url, $message)
    {
        return $message;
        /*
        $url = $this->url[$this->config->tpAmb];
        if (!is_object($this->soap)) {
            $this->soap = new \NFePHP\NFSe\Common\SoapCurl($this->certificate);
        }
        //para usar o cURL quando está estabelecido o uso do CData na estrutura
        //do xml, terá de haver uma transformação, porém no caso do SoapNative isso
        //não é necessário, pois o próprio SoapClient faz essas transformações,
        //baseado no WSDL.
        if (is_a($this->soap, 'NFePHP\Common\Soap\SoapCurl') && $this->withcdata) {
            $messageText = $this->stringTransform($message);
            $request = "<$this->method soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">"
                . "<mensagemXml xsi:type=\"xsd:string\">"
                . $messageText
                . "</mensagemXml>"
                . "</$this->method>";
        } else {
            $params = [
                'mensagemXml' => $message
            ];
        }
        $action = "\"$this->xmlns/LoteRps/". $this->method ."Request\"";
        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $params,
            $this->namespaces[$this->soapversion]
        );

        */

        /*
        $request = "<dsf:$this->method>";
        $request .= "<mensagemXML>$body</mensagemXML>";
        $request .= "</dsf:$this->method>";
        if ($this->withcdata === true) {
            $param = ['soapenv:encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/'];
            $request = $this->replaceNodeWithCdata($request, 'mensagemXML', $body, $param);
        }
        $envelope = "<?xml version=\"1.0\" encoding=\"utf-8\"?><soapenv:Envelope "
                . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
                . "xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" "
                . "xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" "
                . "xmlns:dsf=\"$this->xmlns\">"
                . "<soapenv:Body>"
                . $request
                . "</soapenv:Body>"
                . "</soapenv:Envelope>";

        $messageSize = strlen($envelope);
        $parametros = array(
            'Content-Type: application/soap+xml;charset=utf-8',
            'SOAPAction: "'.$this->method.'"',
            "Content-length: $messageSize");

        return $envelope;
         */
    }

    /**
     * Consulta Lote
     * @param string $numeroLote
     * @return string
     */
    public function consultarLote($numeroLote)
    {
        $this->method = 'consultarLote';
        $fact = new Factories\ConsultarLote($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->codcidade,
            $numeroLote
        );
        return $this->sendRequest('', $xml);
    }

    /**
     * Consulta Lote de NFSe e/ou RPS
     * @param type $prestadorIM
     * @param type $nfse [0 => ['numero', 'codigoVerificacao']]
     * @param type $rps [0 => ['numero', 'serie']]
     */
    public function consultarNFSeRps($prestadorIM, $lote, $nfse = [], $rps = [])
    {
        $this->method = 'consultarNFSeRps';
        $fact = new Factories\ConsultarNFSeRps($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->codcidade,
            'true', //transacao
            $prestadorIM,
            $lote,
            $nfse,
            $rps
        );
        return $this->sendRequest('', $xml);
    }

    /**
     * Consulta nota
     * @param string $prestadorIM
     * @param string $dtInicio
     * @param string $dtFim
     * @param int $notaInicial
     * @return string
     */
    public function consultarNota($prestadorIM, $dtInicio, $dtFim, $notaInicial)
    {
        $this->method = 'consultarNota';
        $fact = new Factories\ConsultarNota($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->codcidade,
            $prestadorIM,
            $dtInicio,
            $dtFim,
            $notaInicial
        );
        return $this->sendRequest('', $xml);
    }

    /**
     * Consulta numero sequencial
     * @param string $prestadorIM
     * @param string $serieRPS
     * @return string
     */
    public function consultarSequencialRps($prestadorIM, $serieRPS)
    {
        $this->method = 'consultarSequencialRps';
        $fact = new Factories\ConsultarSequencialRps($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->codcidade,
            $prestadorIM,
            $serieRPS
        );
        return $this->sendRequest('', $xml);
    }

    /**
     *
     * @param array $rpss
     * @param string $numeroLote
     * @return string
     */
    public function enviar($rpss, $numeroLote)
    {
        $this->method = 'enviar';
        $fact = new Factories\Enviar($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->remetenteRazao,
            null,
            $this->codcidade,
            $rpss,
            $numeroLote
        );
        return $this->sendRequest('', $xml);
    }

    /**
     *
     * @param array $rpss
     * @param string $numeroLote
     * @return string
     */
    public function enviarSincrono($rpss, $numeroLote)
    {
        $this->method = 'enviarSincrono';
        $fact = new Factories\Enviar($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->remetenteRazao,
            null,
            $this->codcidade,
            $rpss,
            $numeroLote
        );
        return $this->sendRequest('', $xml);
    }

    /**
     *
     * @param array $rpss
     * @param string $numeroLote
     * @return string
     */
    public function testeEnviar($rpss, $numeroLote)
    {
        $this->method = 'testeEnviar';
        $fact = new Factories\Enviar($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->remetenteRazao,
            null,
            $this->codcidade,
            $rpss,
            $numeroLote
        );
        return $this->sendRequest('', $xml);
    }
}
