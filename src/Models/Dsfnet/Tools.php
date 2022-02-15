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
        $this->xmlRequest = $message;

        //Dsfnet so possui producao
        if (!$url) {
            $url = $this->url[1];
        }

        if (!is_object($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        //formata o xml da mensagem para o padão esperado pelo webservice
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        $messageText = '<'.$this->method.' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">';
        $messageText.= '<mensagemXml xsi:type="xsd:string"><![CDATA['.$message.']]></mensagemXml>';
        $messageText.= '</'.$this->method.'>';
        
        $this->params = [
            'POST /WsNFe2/LoteRps.jws HTTP/1.1',
            'Host: www.issdigitalsod.com.br',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "https://www.issdigitalsod.com.br/WsNFe2/LoteRps.jws/LoteRps/'.$this->method.'"'
        ];
    
        $action = '';

        //Realiza o request SOAP
        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $this->params,
            $this->namespaces[$this->soapversion],
            $messageText
        );
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
     * @param string $transacao
     * @return string
     */
    public function enviar($rpss, $numeroLote, $transacao = true)
    {
        $this->method = 'enviar';
        $fact = new Factories\Enviar($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $xml = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->remetenteRazao,
            $transacao,
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
