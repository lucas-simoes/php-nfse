<?php

namespace NFePHP\NFSe\Counties\M3526902;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Limeira (SP)
 * conforme o modelo ETransparencia
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M3526902\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\ETransparencia\Tools as ToolsModel;

class Tools extends ToolsModel
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        '2' => 'https://nfehomologacao.etransparencia.com.br/sp.limeira/webservice/aws_nfe.aspx',
        '1' => 'https://nfe.etransparencia.com.br/sp.limeira/webservice/aws_nfe.aspx'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = "NFe";
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_1;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 6639;
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
    protected $versao = 1;
    /**
     * namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        1 => [
            'xmlns:soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
            'xmlns' => 'NFe'
        ],
        2 => []
    ];
}
