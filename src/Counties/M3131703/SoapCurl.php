<?php

namespace NFePHP\NFSe\Counties\M3131703;

use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Exception\SoapException;

/**
 * Description of SoapCurlItabira
 *
 * @author lucas
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
        $this->saveTemporarilyKeyFiles();
        $response = '';
        $envelope = $this->makeEnvelopeSoap(
            $request,
            $namespaces,
            $soapver,
            $soapheader
        );
        $msgSize = strlen($envelope);
        
        $this->httpver = CURL_HTTP_VERSION_1_0;
        
        $parameters[] = "Content-length: $msgSize";
     
        if (!empty($action)) {
            $parameters[0] .= "action=$action";
        }
        $this->requestHead = implode("\n", $parameters);
        $this->requestBody = $envelope;
        
        try {
            $oCurl = curl_init();
            $this->setCurlProxy($oCurl);
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 20);
            curl_setopt($oCurl, CURLOPT_HEADER, 1);
            curl_setopt($oCurl, CURLOPT_HTTP_VERSION, $this->httpver);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
            if (!$this->disablesec) {
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
                if (is_file($this->casefaz)) {
                    curl_setopt($oCurl, CURLOPT_CAINFO, $this->casefaz);
                }
            }
            curl_setopt($oCurl, CURLOPT_SSLVERSION, $this->soapprotocol);
            curl_setopt($oCurl, CURLOPT_SSLCERT, $this->tempdir . $this->certfile);
            curl_setopt($oCurl, CURLOPT_SSLKEY, $this->tempdir . $this->prifile);
            if (!empty($this->temppass)) {
                curl_setopt($oCurl, CURLOPT_KEYPASSWD, $this->temppass);
            }
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            if (!empty($envelope)) {
                curl_setopt($oCurl, CURLOPT_POST, 1);
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, $envelope);
                curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parameters);
            }
            $response = curl_exec($oCurl);
            $this->soaperror = curl_error($oCurl);
            $ainfo = curl_getinfo($oCurl);
            if (is_array($ainfo)) {
                $this->soapinfo = $ainfo;
            }
            $headsize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
            $httpcode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
            curl_close($oCurl);
            $this->responseHead = trim(substr($response, 0, $headsize));
            $this->responseBody = trim(substr($response, $headsize));
            $this->saveDebugFiles(
                $operation,
                $this->requestHead . "\n" . $this->requestBody,
                $this->responseHead . "\n" . $this->responseBody
            );
        } catch (\Exception $e) {
            throw SoapException::unableToLoadCurl($e->getMessage());
        }
        if ($this->soaperror != '') {
            throw SoapException::soapFault($this->soaperror . " [$url]", 500);
        }
        if ($httpcode != 200) {
            throw SoapException::soapFault(" [$url]" . $this->responseHead, 500);
        }
        return $this->responseBody;
    }
    
    /**
     * Set proxy into cURL parameters
     * @param resource $oCurl
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
