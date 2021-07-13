<?php

namespace NFePHP\NFSe\Counties\M3157203;

/**
 * Classe para a comunicação com os webservices da
 * Santa Barbara - MG
 * conforme o modelo Abrasf 2.03 Modificado.
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M3157203\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Tools as ToolsAbrasf;

define("prefixos", [
    'xmlns:x' => "http://schemas.xmlsoap.org/soap/envelope/",
    'xmlns:sis' => "http://www.sistema.com.br/Sistema.Ws.Nfse",
    'xmlns:nfs' => "http://www.abrasf.org.br/nfse.xsd",
    'xmlns:dsi' => "http://www.w3.org/2000/09/xmldsig#"
]);

class Tools extends ToolsAbrasf
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'http://wssantabarbara.simplissweb.com.br/nfseservice.svc',
        2 => 'http://wshomologacaoabrasf.simplissweb.com.br/nfseservice.svc'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_2;

    /**
     * Soap Action
     * @var string
     */
    protected $soapAction = "http://www.sistema.com.br/Sistema.Ws.Nfse/INfseService/";

    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 5143;

    /**
     * Encription signature algorithm
     * @var string
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * Version of schemas
     * @var int
     */
    protected $versao = 203;

    protected $municipioGerador;

    protected function makeRequest($message)
    {
        return  '<sis:' . $this->method . '>'
            . $message
            . '</sis:' . $this->method . '>';
    }

    public function makeXml($rps, $loteId)
    {
        $class = "NFePHP\\NFSe\\Counties\\M3157203\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);

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
            $loteId,
            [$rps]
        );

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        return $message;
    }


    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Counties\\M3157203\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);

        $url = $this->url[$this->config->tpAmb];

        return $this->recepcionarLoteRpsCommon($fact, $lote, $rpss, $url);
    }

    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Counties\\M3157203\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);
        return $this->consultarLoteRpsCommon($fact, $protocolo);
    }

    public function cancelarNfse($nfseNumero)
    {
        $class = "NFePHP\\NFSe\\Counties\\M3157203\\v{$this->versao}\\CancelarNfse";
        $fact = new $class($this->certificate);

        $this->method = 'CancelarNfse';
        $fact->xmlns = $this->xmlns;
        $fact->schemeFolder = $this->schemeFolder;
        $fact->codMun = $this->municipioGerador;
        $fact->algorithm = $this->algorithm;

        $message = $fact->render(
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $nfseNumero
        );

        $url = $this->url[$this->config->tpAmb];

        return $this->sendRequest($url, $message);
    }

    protected function sendRequest($url, $message)
    {
        $this->xmlRequest = $message;
        
        if (!$url) {
            $url = $this->url[$this->config->tpAmb];
        }

        if (!is_object($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        $message = self::deleteNamespaces(prefixos, $message);

        $request = $this->makeRequest($message);

        $this->params = [
            "Content-Type: text/xml; charset=utf-8;charset=utf-8;",
            "SOAPAction: \"{$this->soapAction}{$this->method}\""
        ];

        $action = '';

        return $this->soap->send(
            $url,
            $this->method,
            $action,
            $this->soapversion,
            $this->params,
            prefixos,
            $request
        );
    }


    public static function deleteNamespaces($namespaces, $request){
        // Remove xmlns indevidos.
        foreach ($namespaces as $key => $value) {
            $xmlns = $key . '="' . $value . '"';
            $request = str_replace($xmlns, '', $request);
        }

        // Remove excesso de espaços.
        $request = preg_replace('/\s+/', " ", $request);

        return $request;
    }

    public function setMunicipioGerador($value){
        $this->municipioGerador = $value;
    }
}
