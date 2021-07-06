<?php

namespace NFePHP\NFSe\Models\IPM;

use Exception;
use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Exception\SoapException;

/**
 * Description of SoapCurl SIGISS
 *
 * @author Tiago Franco
 */
class SoapCurl extends SoapBase 
{
    /**
     * @var array
     */
    protected $prefixes = [1 => 'soapenv', 2 => 'x'];
    
    /**
     * Send soap message to url
     * @param string $url
     * @param string $operation
     * @param string $action
     * @param int $soapver
     * @param array $parameters
     * @param array $namespaces
     * @param string $request
     * @param \SoapHeader $soapheader
     * @return string
     * @throws \NFePHP\Common\Exception\SoapException
     */
    public function send(
        $url,
        $operation = '',
        $action = '',
        $soapver = SOAP_1_2,
        $parameters = [],
        $namespaces = [],
        $request = '',
        $soapheader = null
    ) {
        //check or create key files
        //before send request
        $response = '';
        
        try {
            $oCurl = curl_init();
            curl_setopt($oCurl, CURLOPT_URL, $url."?eletron=1");
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($oCurl);
            $this->soaperror = curl_error($oCurl);
            $ainfo = curl_getinfo($oCurl);
            if (is_array($ainfo)) {
                $this->soapinfo = $ainfo;
            }
            $httpcode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
            curl_close($oCurl);
            
            $this->saveDebugFiles(
                $operation,
                json_encode($parameters),
                $response
            );
        } catch (\Exception $e) {
            throw $e;
        }
        if ($this->soaperror != '') {
            throw new \Exception($this->soaperror . " [$url]", 500);
        }
        if ($httpcode != 200) {
            throw new \Exception(" [$url]" . json_encode($parameters), 500);
        }

        return $response;
    }
    
    /**
     * Set proxy into cURL parameters
     * @param \CurlHandle $oCurl
     */
    private function setCurlProxy(&$oCurl)
    {
        if ($this->proxyIP != '') {
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($oCurl, CURLOPT_PROXY, $this->proxyIP . ':' . $this->proxyPort);
            if ($this->proxyUser != '') {
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->proxyUser . ':' . $this->proxyPass);
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            }
        }
    }
    
    /**
     * Mount soap envelope
     * @param string $request
     * @param array $namespaces
     * @param int $soapVer
     * @param \SoapHeader $header
     * @return string
     */
    protected function makeEnvelopeSoap(
        $request,
        $namespaces,
        $soapVer = SOAP_1_2,
        $header = null
    ) {
        $prefix = $this->prefixes[$soapVer];
        $envelopeAttributes = $this->getStringAttributesModify($namespaces);
        return $this->mountEnvelopString(
            $prefix,
            $envelopeAttributes,
            $header,
            $request
        );
    }
    
    /**
     * Get attributes
     * @param array $namespaces
     * @return string
     */
    private function getStringAttributesModify($namespaces = [])
    {
        $envelopeAttributes = '';
        foreach ($namespaces as $key => $value) {
            $envelopeAttributes .= $key . '="' . $value . '" ';
        }
        return $envelopeAttributes;
    }
    
    /**
     * Create a envelop string
     * @param string $envelopPrefix
     * @param string $envelopAttributes
     * @param string $header
     * @param string $bodyContent
     * @return string
     */
    private function mountEnvelopString(
        $envelopPrefix,
        $envelopAttributes = '',
        $header = '',
        $bodyContent = ''
    ) {
        return sprintf(
            '<%s:Envelope %s>' . $header . '<%s:Body>%s</%s:Body></%s:Envelope>',
            $envelopPrefix,
            $envelopAttributes,
            $envelopPrefix,
            $bodyContent,
            $envelopPrefix,
            $envelopPrefix
        );
    }
}
