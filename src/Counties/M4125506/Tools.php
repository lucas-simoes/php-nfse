<?php

namespace NFePHP\NFSe\Counties\M4125506;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Sao Jose dos Pinhais PR
 * conforme o modelo ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4125506\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Lucas B. Simões <lucas_development at outlook dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Factories;
use NFePHP\NFSe\Models\Abrasf\Tools as ToolsAbrasf;

class Tools extends ToolsAbrasf
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'https://nfe.sjp.pr.gov.br/servicos/issOnline2/ws/index.php?wsdl',
        2 => 'https://nfe.sjp.pr.gov.br/servicos/issOnline2/homologacao/ws/index.php?wsdl'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = 'http://www.e-governeapps2.com.br';
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_1;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 7885;
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
    protected $versao = 300;

    /**
     * Namespaces for soap envelope
     * @var array
     */
    protected $namespaces = ['xmlns:soapenv' => "http://schemas.xmlsoap.org/soap/envelope/", 'xmlns:nfe' => "http://nfe.sjp.pr.gov.br"];

    protected $params = [];

    /**
     * @param $lote
     * @param $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\\RecepcionarLoteRps";
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
        $this->method = 'RecepcionarLoteRpsV3';
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

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\\ConsultarLoteRps";
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
        $this->method = 'ConsultarLoteRpsV3';
        $message = $fact->render($this->versao,$this->remetenteCNPJCPF, $this->remetenteIM, $protocolo);
        return $this->sendRequest($url, $message);
    }

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarSituacaoLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\cConsultarSituacaoLoteRps";
        $fact = new $class($this->certificate);
        return $this->consultarSituacaoLoteRpsCommon($fact, $protocolo);
    }

    /**
     * @param $fact
     * @param $protocolo
     * @param string $url
     * @return string
     */
    protected function consultarSituacaoLoteRpsCommon($fact, $protocolo, $url = '')
    {
        $this->method = 'ConsultarSituacaoLoteRpsV3';
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $protocolo);
        return $this->sendRequest($url, $message);
    }

    /**
     * Consulta Lote
     * @param string $numeroNfse
     * @param string $dataInicial
     * @param string $dataFinal
     * @return string
     */
    public function consultarNfse($numeroNfse,$dataInicial,$dataFinal)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\\ConsultarNfse";
        $fact = new $class($this->certificate);
        return $this->consultarNfseCommon($fact, $numeroNfse,$dataInicial,$dataFinal);
    }

    /**
     * @param $fact
     * @param string $numeroNfse
     * @param string $dataInicial
     * @param string $dataFinal
     * @param string $url
     * @return string
     */
    protected function consultarNfseCommon($fact, $numeroNfse,$dataInicial,$dataFinal, $url = '')
    {
        $this->method = 'ConsultarNfseV3';
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $numeroNfse,$dataInicial,$dataFinal);
        return $this->sendRequest($url, $message);
    }


    /**
     * Consulta Lote
     * @param string $numero
     * @param string $serie
     * @param string $tipo
     * @return string
     */
    public function consultarNfsePorRpsV3($numero, $serie,$tipo)
    {
        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\\ConsultarNfsePorRps";
        $fact = new $class($this->certificate);
        return $this->consultarNfsePorRpsCommon($fact, $numero, $serie,$tipo);
    }

    /**
     * @param $fact
     * @param $protocolo
     * @param string $url
     * @return string
     */
    protected function consultarNfsePorRpsCommon($fact, $numero, $serie,$tipo, $url = '')
    {
        $this->method = 'ConsultarNfsePorRpsV3';
        $fact->setXmlns($this->xmlns);
        $message = $fact->render($this->versao, $this->remetenteCNPJCPF, $this->remetenteIM, $numero, $serie,$tipo);
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
            $this->soap = new \NFePHP\NFSe\Counties\M4125506\SoapCurl($this->certificate);
        }

        //formata o xml da mensagem para o padão esperado pelo webservice
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        //O atributo xmlns precisa ser removido da tag <EnviarLoteRpsEnvio> pois
        //o web service de Itabira não o reconhece
        $messageText = str_replace('xmlns="http://www.abrasf.org.br/nfse.xsd"', '', $message);

        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }

        $request = '<nfe:' . $this->method . '>
                         <arg0><![CDATA[<ns2:cabecalho versao="3" xmlns:ns2="http://nfe.sjp.pr.gov.br/cabecalho_v03.xsd"><versaoDados>3</versaoDados></ns2:cabecalho>]]></arg0>
             <arg1><![CDATA[' . $messageText . ']]></arg1>
           </nfe:' . $this->method . '>';

        $this->params = array(
            "soapaction: '$url#" . $this->method . "'",
            'Host: nfe.sjp.pr.gov.br',
            'Content-Type: text/xml; charset=ISO-8859-1'
        );

        $action = 'RecepcionarLoteRpsV3';
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
        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\\RecepcionarLoteRps";
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

        //O atributo xmlns precisa ser removido da tag <EnviarLoteRpsEnvio> pois
        //o web service de Itabira não o reconhece
        $messageText = str_replace('<EnviarLoteRpsEnvio xmlns="http://nfe.sjp.pr.gov.br/servico_enviar_lote_rps_envio_v03.xsd">', '<EnviarLoteRpsEnvio>', $message);

        if ($this->withcdata) {
            $messageText = $this->stringTransform($message);
        }

        return $messageText;
    }
    
    public function cancelarNfse($nfseNumero)
    {
        return '<?xml version="1.0" encoding="utf-8"?><ConsultarLoteRpsResponse xmlns="http://www.e-governeapps2.com.br/">
                <ListaMensagemRetorno>
                    <MensagemRetorno>
                    <Codigo>E999</Codigo>
                    <Mensagem>ESTA NOTA NAO PODERA SER CANCELADA CONFORME DECRETO No. 1.852, DE 28 DE AGOSTO 2014</Mensagem>
                    <Correcao></Correcao>
                    </MensagemRetorno>
                </ListaMensagemRetorno>
                </ConsultarLoteRpsResponse>';

        /*$this->soapAction = $this->url[$this->config->tpAmb];

        $class = "NFePHP\\NFSe\\Counties\\M4125506\\v{$this->versao}\\CancelarNfse";
        $fact = new $class($this->certificate);

        $this->method = 'CancelarNfseV3';
        $fact->xmlns = $this->xmlns;
        $fact->schemeFolder = $this->schemeFolder;
        $fact->codMun = $this->config->cmun;
        $fact->algorithm = $this->algorithm;
        //$fact->setTimezone($this->timezone);
        $message = $fact->render(
            $this->versao,
            $this->remetenteTipoDoc,
            $this->remetenteCNPJCPF,
            $this->remetenteIM,
            $nfseNumero
        );

        // @header ("Content-Disposition: attachment; filename=\"NFSe_Lote.xml\"" );
        // echo $message;
        // exit;
        return $this->sendRequest('', $message);*/
    }
}
