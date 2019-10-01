<?php

namespace NFePHP\NFSe\Models\Issnet;

/**
 * Classe para a comunicação com os webservices da
 * conforme o modelo ISSNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Issnet\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Tools as ToolsBase;
use NFePHP\NFSe\Models\Issnet\Factories;

class Tools extends ToolsBase
{
    public function cancelarNfse($numero, $codigoCancelamento)
    {
        $this->method = 'CancelarNfse';
        $fact = new Factories\CancelarNfse($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $cmun = $this->config->cmun;
        if ($this->config->tpAmb == 2) {
            $cmun = '999';
        }
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $cmun,
            $numero,
            $codigoCancelamento
        );
        return $this->sendRequest('', $message);
    }

    protected function sendRequest($url, $message)
    {
        //no caso do ISSNET o URL é unico para todas as ações
        if (!$url) {
            $url = $this->url[$this->config->tpAmb];
        }

        if (!is_object($this->soap)) {
            $this->soap = new \NFePHP\NFSe\Common\SoapCurl($this->certificate);
        }
        //formata o xml da mensagem para o padão esperado pelo webservice
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($message);
        $message = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $dom->saveXML());

        $messageText = $message;
        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }
        $request = "<" . $this->method . " xmlns=\"" . $this->xmlns . "\">"
            . "<xml>$messageText</xml>"
            . "</" . $this->method . ">";
        $params = [
            'xml' => $message
        ];

        $action = "\"" . $this->xmlns . "/" . $this->method . "\"";
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

    public function consultarUrlVisualizacaoNfse($numero, $codigoTributacao)
    {
        $this->method = 'ConsultarUrlVisualizacaoNfse';
        $fact = new Factories\ConsultarUrlVisualizacaoNfse($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $numero,
            $codigoTributacao
        );
        return $this->sendRequest('', $message);
    }

    public function consultarUrlVisualizacaoNfseSerie($numero, $codigoTributacao, $serie)
    {
        $this->method = 'ConsultarUrlVisualizacaoNfseSerie';
        $fact = new Factories\ConsultarUrlVisualizacaoNfse($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $numero,
            $codigoTributacao,
            $serie
        );
        return $this->sendRequest('', $message);
    }

    public function recepcionarLoteRps($lote, $rpss)
    {
        $this->method = 'RecepcionarLoteRps';
        $fact = new Factories\EnviarLoteRps($this->certificate);
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
        return $this->sendRequest('', $message);
    }

    public function consultarNfse(
        $numeroNFSe = '',
        $dtInicio = '',
        $dtFim = '',
        $tomador = [],
        $intermediario = []
    ) {
        $this->method = 'ConsultarNfse';
        $fact = new Factories\ConsultarNfse($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $numeroNFSe,
            $dtInicio,
            $dtFim,
            $tomador,
            $intermediario
        );
        return $this->sendRequest('', $message);
    }

    public function consultarNfseRps($numero, $serie, $tipo)
    {
        $this->method = 'ConsultarNfseRps';
        $fact = new Factories\ConsultarNfseRps($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $numero,
            $serie,
            $tipo
        );
        return $this->sendRequest('', $message);
    }

    public function consultarLoteRps($protocolo)
    {
        $this->method = 'ConsultarLoteRps';
        $fact = new Factories\ConsultarLoteRps($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $protocolo
        );
        return $this->sendRequest('', $message);
    }

    public function consultarSituacaoLoteRps($protocolo)
    {
        $this->method = 'ConsultarSituacaoLoteRPS';
        $fact = new Factories\ConsultarSituacaoLoteRps($this->certificate);
        $fact->setSignAlgorithm($this->algorithm);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $protocolo
        );
        return $this->sendRequest('', $message);
    }
}
