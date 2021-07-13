<?php

namespace NFePHP\NFSe\Counties\M4113700;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Londrina PR
 * conforme o modelo SIGISS
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4113700\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Lucas B. Simões <lucas_development at outlook dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\SIGISS\SoapCurl;
use NFePHP\NFSe\Models\SIGISS\Tools as ToolsSIGISS;

class Tools extends ToolsSIGISS
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'https://iss.londrina.pr.gov.br/ws/v1_03/sigiss_ws.php?wsdl',
        2 => 'http://testeiss.londrina.pr.gov.br/ws/v1_03/sigiss_ws.php?wsdl'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = '';
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_1;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 7667;
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
    protected $versao = 103;

    /**
     * Namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        'xmlns:SOAP-ENV' => "http://schemas.xmlsoap.org/soap/envelope/",
        'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchemainstance", 
        'xmlns:SOAP-ENC' => 'http://schemas.xmlsoap.org/soap/encoding/', 
        'xmlns:tns' => 'http://iss.londrina.pr.gov.br/ws/v1_03'
    ];

    protected $params = [];

    /**
     * @param $lote
     * @param $rps
     * @return string
     */
    public function gerarNota($rps)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4113700\\v{$this->versao}\\GerarNota";
        $fact = new $class($this->certificate);

        return $this->gerarNotaCommon($fact, $rps);
    }

    /**
     * @param $fact
     * @param $lote
     * @param $rps
     * @param string $url
     * @return string
     */
    protected function gerarNotaCommon($fact, $rps, $url = '')
    {
        $this->method = 'GerarNota';
        $message = $fact->render(
            $this->versao,
            $rps
        );

        // @header ("Content-Disposition: attachment; filename=\"NFSe_Lote.xml\"" );
        // echo $message;
        // exit;
        return $this->sendRequest($url, $message);
    }

    /**
     * Consulta pelo Numero do RPS
     * @param string $rps
     * @return string
     */
    public function consultarRps($rps)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4113700\\v{$this->versao}\\ConsultarRps";
        $fact = new $class($this->certificate);
        return $this->consultarRpsCommon($fact, $rps);
    }

    /**
     * @param $fact
     * @param $rps
     * @param string $url
     * @return string
     */
    protected function consultarRpsCommon($fact, $rps, $url = '')
    {
        $this->method = 'ConsultarRpsServicoPrestado';
        $message = $fact->render($this->versao, $rps);
        return $this->sendRequest($url, $message);
    }

    /**
     * Consulta Lote
     * @param string $rps
     * @return string
     */
    public function consultarNfse($rps)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4113700\\v{$this->versao}\\ConsultarNfse";
        $fact = new $class($this->certificate);
        return $this->consultarNfseCommon($fact, $rps);
    }

    /**
     * @param $fact
     * @param string $rps
     * @param string $url
     * @return string
     */
    protected function consultarNfseCommon($fact, $rps, $url = '')
    {
        $this->method = 'ConsultarNfseServicoPrestado';
        $message = $fact->render($this->versao, $rps);
        return $this->sendRequest($url, $message);
    }

    
    /**
     * Consulta Cadastro
     * @param string $rps
     * @param string $cpfCnpjContribuinte
     * @return string
     */
    public function ConsultarCadastro($rps, $cpfCnpjContribuinte)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4113700\\v{$this->versao}\\ConsultarCadastro";
        $fact = new $class($this->certificate);
        return $this->ConsultarCadastroCommon($fact, $rps, $cpfCnpjContribuinte);
    }

    /**
     * @param $fact
     * @param string $rps
     * @param string $cpfCnpjContribuinte
     * @param string $url
     * @return string
     */
    protected function ConsultarCadastroCommon($fact, $rps, $cpfCnpjContribuinte, $url = '')
    {
        $this->method = 'ConsultarCadastroContribuinte';
        $message = $fact->render($this->versao, $rps, $cpfCnpjContribuinte);
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
        $this->xmlRequest = $message;
        
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

        //O atributo xmlns precisa ser removido da tag <EnviarLoteRpsEnvio> pois
        //o web service de Itabira não o reconhece
        $messageText = str_replace('xmlns="http://www.SIGISS.org.br/nfse.xsd"', '', $message);

        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }

        $request = '<tns:' . $this->method . ' xmlns:tns="http://iss.londrina.pr.gov.br/ws/v1_03">' . trim($messageText) . '</tns:' . $this->method . '>';

        $this->params = array(
            "soapaction: '$url#" . $this->method . "'",
            'Host: testeiss.londrina.pr.gov.br',
            'Content-Type: text/xml; charset=ISO-8859-1'
        );

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
     * @param \NFePHP\NFSe\Models\SIGISS\Rps $rps
     * @return string Retorna o xml serializado
     */
    public function makeXml($rps)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4113700\\v{$this->versao}\\GerarNota";
        $fact = new $class($this->certificate);

        $message = $fact->render(
            $this->versao,
            $rps
        );

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        //O atributo xmlns precisa ser removido da tag <EnviarLoteRpsEnvio> pois
        //o web service de Itabira não o reconhece
        $messageText = str_replace('xmlns="http://www.SIGISS.org.br/nfse.xsd"', '', $message);

        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }

        return $messageText;
    }

    public function cancelarNfse($rps, $codCancelamento, $email = '')
    {
        $class = "NFePHP\\NFSe\\Counties\\M4113700\\v{$this->versao}\\CancelarNota";
        $fact = new $class($this->certificate);

        return $this->cancelarNotaCommon($fact, $rps, $codCancelamento, $email);
    }

    /**
     * @param $fact
     * @param $rps
     * @param $codCancelamento
     * @param $email
     * @param string $url
     * @return string
     */
    protected function cancelarNotaCommon($fact, $rps, $codCancelamento, $email = '', $url = '')
    {
        $this->method = 'CancelarNota';
        
        $message = $fact->render(
            $this->versao,
            $rps, 
            $codCancelamento,
            $email
        );
        

        // @header ("Content-Disposition: attachment; filename=\"NFSe_Lote.xml\"" );
        // echo $message;
        // exit;
        return $this->sendRequest($url, $message);
    }
}
