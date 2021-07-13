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
     * Comunica com os servidores IPM via REST
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
            curl_setopt($oCurl, CURLOPT_URL, $url);
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
}
