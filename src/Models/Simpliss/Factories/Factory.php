<?php

namespace NFePHP\NFSe\Models\Simpliss\Factories;

use NFePHP\Common\Certificate;
use NFePHP\NFSe\Common\Factory as FactoryBase;

class Factory extends FactoryBase
{
    protected $xmlns;
    protected $schemeFolder;
    protected $cmun;

    /**
     * Construtor recebe a classe de certificados
     *
     * @param \NFePHP\Common\Certificate|null $certificate
     * @param int $algorithm
     */
    public function __construct(Certificate $certificate = null, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->certificate = $certificate;
        $this->algorithm = $algorithm;
        $this->pathSchemes = __DIR__ . '/../../../../schemes';
    }

    /**
     * @param $xmlns
     */
    public function setXmlns($xmlns)
    {
        $this->xmlns = $xmlns;
    }

    /**
     * @param $schemeFolder
     */
    public function setSchemeFolder($schemeFolder)
    {
        $this->schemeFolder = $schemeFolder;
    }

    /**
     * @param $cmun
     */
    public function setCodMun($cmun)
    {
        $this->cmun = $cmun;
    }
}
