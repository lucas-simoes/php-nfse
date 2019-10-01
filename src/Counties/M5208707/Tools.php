<?php

namespace NFePHP\NFSe\Counties\M5208707;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Goiania GO
 * conforme o modelo Goiania
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M5208707\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Goiania\Tools as ToolsModel;

class Tools extends ToolsModel
{
    /**
     * Webservices URL
     * @var array
     */

    //O ambiente de produção e homologação são o mesmo, onde deve-se solicitar alteração na prefeitura de Goiânia para entrar/sair do modo TESTE
    protected $url = [
        1 => 'https://nfse.goiania.go.gov.br/ws/nfse.asmx',
        2 => 'https://nfse.goiania.go.gov.br/ws/nfse.asmx'
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = 'http://nfse.goiania.go.gov.br/xsd/nfse_gyn_v02.xsd';

    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_2;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 9373;
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
    protected $versao = '02';
    /**
     * namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        1 => [
            'xmlns:soap' => "http://schemas.xmlsoap.org/soap/envelope/",
            'xmlns:ws' => "http://nfse.goiania.go.gov.br/ws/"
        ],
        2 => [
            'xmlns:soap' => "http://www.w3.org/2003/05/soap-envelope",
            'xmlns:ws' => "http://nfse.goiania.go.gov.br/ws/"
        ]
    ];
}
