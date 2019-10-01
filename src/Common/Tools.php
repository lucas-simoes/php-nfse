<?php

namespace NFePHP\NFSe\Common;

/**
 * Basic Abstract Class, for all derived classes from NFSe models
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Common\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapInterface;
use Psr\Log\LoggerInterface;
use stdClass;

abstract class Tools
{
    /**
     * configuration values
     * @var \stdClass
     */
    protected $config;
    /**
     * Certificate::class
     * @var \NFePHP\Common\Certificate
     */
    protected $certificate;
    /**
     * Soap::class
     * @var \NFePHP\Common\Soap\SoapInterface
     */
    protected $soap;
    /**
     * Method from webservice
     * @var string
     */
    protected $method = '';
    /**
     * Logger::class
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * Version of XSD
     * @var int
     */
    protected $versao;
    /**
     * Type of Federal registration
     * @var int
     */
    protected $remetenteTipoDoc;
    /**
     * Federal registration number
     * @var string
     */
    protected $remetenteCNPJCPF;
    /**
     * Company Name
     * @var string
     */
    protected $remetenteRazao;
    /**
     * Municipal registration number
     * @var string
     */
    protected $remetenteIM;
    /**
     * Webservices URL's
     * @var array
     */
    protected $url = [
        1 => '',
        2 => ''
    ];
    /**
     * County Namespace
     * @var string
     */
    protected $xmlns = '';
    /**
     * Soap Version
     * @var int
     */
    protected $soapversion = SOAP_1_2;
    /**
     * SIAFI County Cod
     * @var int
     */
    protected $codcidade = 0;
    /**
     * Indicates when use CDATA string on message
     * @var boolean
     */
    protected $withcdata = false;
    /**
     * Encription signature algorithm
     * @var int
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * @var \DateTimeZone
     */
    protected $timezone;
    /**
     * Namespaces for soap envelope
     * @var array
     */
    protected $namespaces = [];
    /**
     * @var bool
     */
    protected $debugsoap = false;

    /**
     * Constructor
     * @param stdClass $config
     * @param \NFePHP\Common\Certificate $certificate
     */
    public function __construct(stdClass $config, Certificate $certificate)
    {
        $this->config = $config;

        //Se o model já possuia  versão não tem necessidade de pegar da configuração
        if (empty($this->versao)) {
            $this->versao = $config->versao;
        }

        $this->remetenteCNPJCPF = $config->cpf;
        $this->remetenteRazao = $config->razaosocial;
        $this->remetenteIM = $config->im;
        $this->remetenteTipoDoc = 1;
        if ($config->cnpj != '') {
            $this->remetenteCNPJCPF = $config->cnpj;
            $this->remetenteTipoDoc = 2;
        }
        $this->certificate = $certificate;
        $this->timezone = DateTime::tzdBR($config->siglaUF);


        if (empty($this->versao)) {
            throw new \LogicException('Informe a versão do modelo.');
        }

    }

    /**
     * Set to true if CData is used in XML message
     * @param boolean $flag
     */
    public function setUseCdata($flag)
    {
        $this->withcdata = $flag;
    }

    /**
     * Set debug Soap Mode
     * @param bool $value
     */
    public function setDebugSoapMode($value = false)
    {
        $this->debugsoap = $value;
        if (is_object($this->soap)) {
            $this->soap->setDebugMode($this->debugsoap);
        }
    }

    /**
     * Load the chosen soap class
     * @param \NFePHP\Common\Soap\SoapInterface $soap
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
        $this->soap->loadCertificate($this->certificate);
        $this->soap->setDebugMode($this->debugsoap);
    }

    /**
     * Load the cohsen logger class
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLoggerClass(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Send request to webservice
     * @param string $message
     * @return string
     */
    abstract protected function sendRequest($url, $message);

    /**
     * Convert string xml message to cdata string
     * @param string $message
     * @return string
     */
    protected function stringTransform($message)
    {
        return EntitiesCharacters::unconvert(htmlentities($message, ENT_NOQUOTES));
    }
}
