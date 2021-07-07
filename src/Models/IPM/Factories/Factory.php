<?php

namespace NFePHP\NFSe\Models\IPM\Factories;

use NFePHP\Common\Certificate;
use NFePHP\NFSe\Common\Factory as FactoryBase;

class Factory extends FactoryBase
{
    /**
     * Construtor recebe a classe de certificados
     *
     * @param \NFePHP\Common\Certificate $certificate
     * @param int $algorithm
     */
    public function __construct(Certificate $certificate = null, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->certificate = $certificate;
        $this->algorithm = $algorithm;
        $this->pathSchemes = __DIR__ . '/../../schemes';
    }
}
