<?php

namespace NFePHP\NFSe\Counties\M3550308;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de São Paulo SP
 * conforme o modelo PRODAM
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M3550308\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Prodam\Tools as ToolsProdam;

class Tools extends ToolsProdam
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        '2' => 'https://testenfe.prefeitura.sp.gov.br/ws/lotenfe.asmx',
        '1' => 'https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = "http://www.prefeitura.sp.gov.br/nfe";
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_2;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 7107;
    /**
     * Indicates when use CDATA string on message
     * @var boolean
     */
    protected $withcdata = true;
    /**
     * Encription signature algorithm
     * @var string
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * Version of schemas
     * @var int
     */
    protected $versao = 1;
    /**
     * namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        1 => [
            'xmlns:soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
            'xmlns' => 'http://www.prefeitura.sp.gov.br/nfe'
        ],
        2 => [
            'xmlns:soap' => 'http://www.w3.org/2003/05/soap-envelope',
            'xmlns' => 'http://www.prefeitura.sp.gov.br/nfe'
        ]
    ];
}
