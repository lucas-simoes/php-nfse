<?php

namespace NFePHP\NFSe\Models\Publica;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo ABRASF Publica
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Publica\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Tools as ToolsBase;
use NFePHP\NFSe\Models\Publica\Factories;


class Tools extends ToolsBase
{
    protected $xmlns = 'http://www.publica.inf.br';
    protected $schemeFolder = 'Publica';
    protected $params = [];

    public function cancelarNfse($nfseNumero, $codCancelamento)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\CancelarNfse";
        $fact = new $class($this->certificate);

        $this->method = 'CancelarNfse';
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $this->config->cmun, $nfseNumero, $codCancelamento);
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);

        $this->method = 'ConsultarLoteRps';
        $fact->setXmlns($this->xmlns);
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $protocolo);
        return $this->sendRequest('', $message);
    }

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarSituacaoLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\ConsultarSituacaoLoteRps";
        $fact = new $class($this->certificate);

        $this->method = 'ConsultarSituacaoLoteRps';
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $protocolo);
        return $this->sendRequest('', $message);
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
        
        //Publica possui apenas uma URL
        if (!$url) {
            $url = $this->url[$this->config->tpAmb];
        }

        if (!is_object($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }

        $message = '<?xml version="1.0" encoding="UTF-8"?>' . $message;
        $request  = "<ns2:" . $this->method . ">";
        $request .= sprintf("<XML><![CDATA[%s]]></XML>", $message);
        $request .= "</ns2:" . $this->method . ">";

        $action = "";
        //Realiza o request SOAP
        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $this->params,
            $this->namespaces,
            $request
        );
    }

    public function consultarNfsePorFaixa($numeroNfseInicial, $numeroNfseFinal)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\ConsultarNfseFaixa";
        $fact = new $class($this->certificate);

        $this->method = 'ConsultarNfseFaixa';
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $numeroNfseInicial, $numeroNfseFinal);
        return $this->sendRequest('', $message);
    }

    public function ConsultarNfsePorRps($numero, $serie, $tipo)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\ConsultarNfsePorRps";
        $fact = new $class($this->certificate);

        $this->method = 'ConsultarNfsePorRps';
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $numero, $serie, $tipo);
        return $this->sendRequest('', $message);
    }

    public function consultarNfseServicoPrestado()
    {
    }

    public function ConsultarNfseRecebida($tipoTomador, $cpfCnpj, $dataNfse)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\ConsultarNfseRecebida";
        $fact = new $class($this->certificate);

        $this->method = 'ConsultarNfseRecebida';
        $message = $fact->render($this->versao, $tipoTomador, $cpfCnpj, $dataNfse);
        return $this->sendRequest('', $message);
    }

    /**
     * @param $rps
     * @return string
     */
    public function gerarNfse($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\GerarNfse";
        $fact = new $class($this->certificate);

        $this->method = 'GerarNfse';
        $fact->setXmlns($this->xmlns);
        $fact->setSchemeFolder($this->schemeFolder);
        $fact->setCodMun($this->config->cmun);
        $fact->setSignAlgorithm($this->algorithm);
        $fact->setTimezone($this->timezone);

        $message = $fact->render(
            $this->versao,
            $rps
        );

        return $this->sendRequest('', $message);
    }

    /**
     * @param $lote
     * @param $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
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

    /**
     * @param $lote
     * @param $rpss
     * @return string
     */
    public function cartaCorrecaoNfseEnvio($nfseNumero, $rps, $retificaValor = false)
    {
        $class = "NFePHP\\NFSe\\Models\\Publica\\Factories\\v{$this->versao}\\CartaCorrecaoNfseEnvio";
        $fact = new $class($this->certificate);

        $this->method = 'CartaCorrecaoNfseEnvio';
        $fact->setXmlns($this->xmlns);
        $fact->setSchemeFolder($this->schemeFolder);
        $fact->setCodMun($this->config->cmun);
        $fact->setSignAlgorithm($this->algorithm);
        $fact->setTimezone($this->timezone);
        $message = $fact->render(
            $this->versao,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $nfseNumero,
            $rps,
            $retificaValor
        );

        // @header ("Content-Disposition: attachment; filename=\"NFSe_Lote.xml\"" );
        // echo $message;
        // exit;
        return $this->sendRequest('', $message);
    }
}
