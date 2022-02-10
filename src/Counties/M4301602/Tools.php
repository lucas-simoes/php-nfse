<?php

namespace NFePHP\NFSe\Counties\M4301602;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Bage RS
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
        1 => 'https://bagers.webiss.com.br/ws/nfse.asmx',
        2 => 'https://homologacao.webiss.com.br/ws/nfse.asmx'
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
    protected $codcidade = 3945;
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
    protected $namespaces = [
        1 => [
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
            'xmlns:soap'=> "http://schemas.xmlsoap.org/soap/envelope/"
        ],
        2  => [
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
            'xmlns:soap'=> "http://schemas.xmlsoap.org/soap/envelope/"
        ],
    ];

    protected $params = [];

    private $soapAction = 'http://nfse.abrasf.org.br/';

    /**
     * Monta o request da mensagem SOAP
     * @param string $url
     * @param string $message
     * @return string
     */
    protected function sendRequest($url, $message)
    {
        $this->xmlRequest = $message;
        
        //Abrasf possui apenas uma URL
        if (!$url) {
            $url = $this->url[$this->config->tpAmb];
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

        $messageText = $message;
        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }
        $request = $this->makeRequest($messageText);
        if (!count($this->params)) {
            $this->params = [
                //"POST /ws/nfse.asmx HTTP/1.1",
               // "Host: homologacao.webiss.com.br",
                "Content-Type: text/xml; charset=utf-8",
                "SOAPAction: \"{$this->soapAction}{$this->method}\"",
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
                $request = "<{$this->method}Request xmlns=\"http://www.e-governeapps2.com.br/\">"
                    . $message
                    . "</{$this->method}Request>";
                break;
            case 201:
                $versao = '2.01';
                // no break
            case 202:
                $request =
                    "<{$this->method}Request xmlns=\"http://nfse.abrasf.org.br\">"
                    . '<nfseCabecMsg xmlns="">'
                    . "<![CDATA["
                    . "<cabecalho xmlns=\"{$this->xmlns}\" versao=\"{$versao}\">"
                    . "<versaoDados>{$versao}</versaoDados>"
                    . "</cabecalho>"
                    . "]]>"
                    . "</nfseCabecMsg>"
                    . '<nfseDadosMsg xmlns="">'
                    . "<![CDATA["
                    . $message
                    . "]]>"
                    . "</nfseDadosMsg>"
                    . "</{$this->method}Request>";
                break;
            default:
                throw new \LogicException('Versão não suportada');
        }
        return $request;
    }
}
