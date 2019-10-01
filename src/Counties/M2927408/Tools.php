<?php

namespace NFePHP\NFSe\Counties\M2927408;

/**
 * Classe para a comunicação com os webservices da
 * Cidade de Salvador BA
 * conforme o modelo ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Counties\M2927408\Tools
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
            'EnvioLoteRPS' => "https://nfse.salvador.ba.gov.br/rps/ENVIOLOTERPS/EnvioLoteRPS.svc",
            'ConsultaSituacaoLoteRPS' =>
                "https://nfse.salvador.ba.gov.br/rps/CONSULTASITUACAOLOTERPS/ConsultaSituacaoLoteRPS.svc",
            'ConsultaLoteRPS' => "https://nfse.salvador.ba.gov.br/rps/CONSULTALOTERPS/ConsultaLoteRPS.svc",
            'ConsultaNfseRPS' => "https://nfse.salvador.ba.gov.br/rps/CONSULTANFSERPS/ConsultaNfseRPS.svc",
            'ConsultaNFse' => "https://nfse.salvador.ba.gov.br/rps/CONSULTANFSE/ConsultaNfse.svc"
        ],
        2 => [
            'EnvioLoteRPS' => "https://notahml.salvador.ba.gov.br/rps/ENVIOLOTERPS/EnvioLoteRPS.svc",
            'ConsultaSituacaoLoteRPS' =>
                "https://notahml.salvador.ba.gov.br/rps/CONSULTASITUACAOLOTERPS/ConsultaSituacaoLoteRPS.svc",
            'ConsultaLoteRPS' => "https://notahml.salvador.ba.gov.br/rps/CONSULTALOTERPS/ConsultaLoteRPS.svc",
            'ConsultaNfseRPS' => "https://notahml.salvador.ba.gov.br/rps/CONSULTANFSERPS/ConsultaNfseRPS.svc",
            'ConsultaNFse' => "https://notahml.salvador.ba.gov.br/rps/CONSULTANFSE/ConsultaNfse.svc"
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
    protected $codcidade = 3849;
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
