<?php

namespace NFePHP\NFSe\Counties\M4106902;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Curitiba - PR
 * conforme o modelo Abrasf
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4106902\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
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
        1 => 'https://isscuritiba.curitiba.pr.gov.br/Iss.NfseWebService/nfsews.asmx',
        2 => 'https://pilotoisscuritiba.curitiba.pr.gov.br/nfse_ws/NfseWs.asmx'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = "http://isscuritiba.curitiba.pr.gov.br/iss/nfse.xsd";
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_2;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 7535;
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
    protected $versao = 100;
    /**
     * namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        1 => [
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
            'xmlns:soap' => "http://schemas.xmlsoap.org/soap/envelope/"
        ],
        2 => [
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
            'xmlns:soap' => "http://www.w3.org/2003/05/soap-envelope"
        ]
    ];
}
