<?php

namespace NFePHP\NFSe\Counties\M3304557;

/**
 * Classe para a comunicação com os webservices da
 * Cidade do Rio de Janeiro RJ
 * conforme o modelo ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M3304557\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
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
        1 => [
            'EnvioLoteRPS' => "https://notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaSituacaoLoteRPS' => "https://notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaLoteRPS' => "https://notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaNfseRPS' => "https://notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaNFse' => "https://notacarioca.rio.gov.br/WSNacional/nfse.asmx"
        ],
        2 => [
            'EnvioLoteRPS' => "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaSituacaoLoteRPS' => "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaLoteRPS' => "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaNfseRPS' => "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx",
            'ConsultaNFse' => "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx"
        ]
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = "http://tempuri.org/";
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_2;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 6001;
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
}
