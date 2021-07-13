<?php

namespace NFePHP\NFSe\Counties\M3549102;

/**
 * Classe para a comunicação com os webservices da
 * Sao Joao da Boa Vista - MG
 * conforme o modelo Abrasf Simpliss 1.00
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M3549102\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Simpliss\Tools as ToolsBase;

class Tools extends ToolsBase
{
    /**
     * Webservices URL
     * @var array
     */
    protected $url = [
        1 => 'http://wssaojoao.simplissweb.com.br/nfseservice.svc?singleWsdl',
        2 => 'http://wshomologacao.simplissweb.com.br/nfseservice.svc?singleWsdl'
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
     * Soap Action
     * @var string
     */
    protected $soapAction = "http://www.sistema.com.br/Sistema.Ws.Nfse/INfseService/";

    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 7083;

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
     * Namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [
        'xmlns:soap12' => "http://schemas.xmlsoap.org/soap/envelope/",         'xmlns:sis' => 'http://www.sistema.com.br/Sistema.Ws.Nfse',
        'xmlns:nfse'    => "http://www.sistema.com.br/Nfse/arquivos/nfse_3.xsd", 'xmlns:xd' => 'http://www.w3.org/2000/09/xmldsig#',
        'xmlns:sis1'    => 'xmlns:sis1="">'
    ];
}
