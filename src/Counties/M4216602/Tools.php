<?php

namespace NFePHP\NFSe\Counties\M4216602;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de São José - SC
 * conforme o modelo BETHA
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4216602\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Betha\Tools as ToolsBetha;

class Tools extends ToolsBetha
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'http://e-gov.betha.com.br/e-nota-contribuinte-ws/nfseWS?wsdl',
        2 => 'http://e-gov.betha.com.br/e-nota-contribuinte-test-ws/nfseWS?wsdl'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = "http://www.betha.com.br/e-nota-contribuinte-ws";
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_1;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = '';
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
     * namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        1 => [
            'xmlns:soapenv' => "http://schemas.xmlsoap.org/soap/envelope/",
            'xmlns:e' => "http://www.betha.com.br/e-nota-contribuinte-ws",
        ],
        2 => [
            'xmlns:soapenv' => "http://schemas.xmlsoap.org/soap/envelope/",
            'xmlns:e' => "http://www.betha.com.br/e-nota-contribuinte-ws",
        ]
    ];
}
