<?php

namespace NFePHP\NFSe;

use NFePHP\Common\Certificate;

/**
 * Classe para a instanciação das classes especificas de cada municipio
 * atendido pela API que nao trabalham com certificados e assinaturas de documentos
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\NFSe
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class NFSeSemCertif
{
    public $rps;
    public $convert;
    public $tools;
    public $response;

    /**
     * Construtor da classe
     * @param string $config Path to file or string Json
     * @param NFePHP\Common\Certificate $certificate
     */
    public function __construct($config, Certificate $certificate = null)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }
        $configClass = json_decode($config);
        $this->convert = NFSeStaticSemCertif::convert($configClass);
        $this->rps = NFSeStaticSemCertif::rps($configClass);
        $this->tools = NFSeStaticSemCertif::tools($configClass, $certificate);
        $this->response = NFSeStaticSemCertif::response($configClass);
    }
}
