<?php

namespace NFePHP\NFSe\Counties\M4314902;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Porto Alegre RS
 * conforme o modelo BHISS
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4314902\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\BHISS\Tools as ToolsBHISS;

class Tools extends ToolsBHISS
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'https://nfe.portoalegre.rs.gov.br/bhiss-ws/nfse',
        2 => 'https://nfse-hom.procempa.com.br/bhiss-ws/nfse'
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
    protected $soapversion = SOAP_1_1;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 8801;
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
            'xmlns:soapenv' => "http://schemas.xmlsoap.org/soap/envelope/",
            'xmlns:ws' => "http://ws.bhiss.pbh.gov.br",
        ],
        2 => [
            'xmlns:soapenv' => "http://schemas.xmlsoap.org/soap/envelope/",
            'xmlns:ws' => "http://ws.bhiss.pbh.gov.br",
        ]
    ];
}
