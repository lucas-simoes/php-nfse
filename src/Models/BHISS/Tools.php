<?php

namespace NFePHP\NFSe\Models\BHISS;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo BHISS
 * NOTA: BHISS extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\BHISS\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Tools as ToolsAbrasft;

class Tools extends ToolsAbrasft
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';
    protected $schemeFolder = 'BHISS';

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\BHISS\\Factories\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);
        return $this->consultarLoteRpsCommon($fact, $protocolo);
    }

    /**
     * Recepciona lote
     * @param string $lote
     * @param array $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\BHISS\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);
        return $this->recepcionarLoteRpsCommon($fact, $lote, $rpss);
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
        $params = [
            "Content-Type: text/xml;charset=utf-8;",
        ];

        $action = '';

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
     * @param $message
     * @return string
     */
    protected function makeRequest($message)
    {
        /*
                    . "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
        */
        $request =
            "<ws:{$this->method}Request>"
            . "<nfseCabecMsg><![CDATA["
            . "<cabecalho xmlns=\"http://www.abrasf.org.br/nfse.xsd\" versao=\"1.00\">"
            . "<versaoDados>1.00</versaoDados>"
            . "</cabecalho>]]>"
            . "</nfseCabecMsg>"
            . "<nfseDadosMsg><![CDATA["
            . $message
            . "]]></nfseDadosMsg>"
            . "</ws:{$this->method}Request>";
        return $request;
    }
}
