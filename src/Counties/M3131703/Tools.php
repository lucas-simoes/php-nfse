<?php

namespace NFePHP\NFSe\Counties\M3131703;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Itabira MG
 * conforme o modelo ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M3131703\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Lucas B. Simões <lucas_development at outlook dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Tools as ToolsAbrasf;

class Tools extends ToolsAbrasf
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'http://servicosweb.itabira.mg.gov.br:90/nfse.portal.integracao/services.svc',
        2 => 'http://servicosweb.itabira.mg.gov.br:90/nfse.portal.integracao.teste/services.svc'
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
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 4633;
    /**
     * Indicates when use CDATA string on message
     * @var boolean
     */
    protected $withcdata = false;
    /**
     * Encription signature algorithm
     * @var string
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * Version of schemas
     * @var int
     */
    protected $versao = 202;
    
    /**
     * Namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [2];
    
    protected $params = [
                    "Content-Type: text/xml; charset=utf-8"
                ];
    
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
            $this->namespaces,
            $request
        );
    }
    
    /**
     * Método que converto o objeto RPS em XML;
     * @param \NFePHP\NFSe\Models\Abrasf\Rps $rps
     * @return string Retorna o xml serializado
     */
    public function makeXml($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\Abrasf\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
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
            1,
            [$rps]
        );
        
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
        
        return $messageText;
    }
}
