<?php

namespace NFePHP\NFSe\Counties\M4115200;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Maringa - PR
 * conforme o modelo Abrasf
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M4115200\Tools
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
        1 => 'https://isse.maringa.pr.gov.br/ws',
        2 => 'https://isseteste.maringa.pr.gov.br/ws'
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
    protected $codcidade = 7691;
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
    protected $versao = 201;
    /**
     * namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        1 => [
            'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xsi:schemaLocation' => "http://www.abrasf.org.br/nfse.xsd nfse_v2.01.xsd",
            'xmlns:soap' => "http://schemas.xmlsoap.org/soap/envelope/"
        ],
        2 => [
            'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xsi:schemaLocation' => "http://www.abrasf.org.br/nfse.xsd nfse_v2.01.xsd",
            'xmlns:soap12' => "http://www.w3.org/2003/05/soap-envelope"
        ]
    ];
}
