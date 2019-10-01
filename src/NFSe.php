<?php

namespace NFePHP\NFSe;

/**
 * Classe para a instanciação das classes especificas de cada municipio
 * atendido pela API
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

use NFePHP\Common\Certificate;

class NFSe
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
    public function __construct($config, Certificate $certificate)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }
        $configClass = json_decode($config);
        $this->convert = NFSeStatic::convert($configClass);
        $this->rps = NFSeStatic::rps($configClass);
        $this->tools = NFSeStatic::tools($configClass, $certificate);
        $this->response = NFSeStatic::response($configClass);
    }
}
