<?php

namespace NFePHP\NFSe\Common;

/**
 * Classe base para a construção dos XMLs relativos ao serviços
 * dos webservices
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Factory
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;
use NFePHP\Common\Validator;

class Factory
{
    /**
     * @var Certificate
     */
    public $certificate;
    /**
     * @var string
     */
    public $pathSchemes = '';
    /**
     * @var string
     */
    public $xml = '';
    /**
     * @var int
     */
    public $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * @var \DateTimeZone
     */
    public $timezone;

    /**
     * Construtor recebe a classe de certificados
     *
     * @param \NFePHP\Common\Certificate $certificate
     * @param int $algorithm
     */
    public function __construct(Certificate $certificate, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->certificate = $certificate;
        $this->algorithm = $algorithm;
        $this->pathSchemes = __DIR__ . '/../../schemes';
    }

    /**
     * Set time Zone as class DateTimeZone based in UF alias
     * @param \DateTimeZone $timezone
     */
    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Set OPENSSL Algorithm using OPENSSL constants
     * @param int $algorithm
     */
    public function setSignAlgorithm($algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * Remove os marcadores de XML
     * @param string $body
     * @return string
     */
    public function clear($body)
    {
        $body = str_replace('<?xml version="1.0"?>', '', $body);
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $body);
        $body = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $body);
        return $body;
    }

    /**
     * Executa a validação da mensagem XML com base no XSD
     * @param string $versao versão dos schemas
     * @param string $body corpo do XML a ser validado
     * @param string $model modelo de RPS
     * @param string $method Denominação do método
     * @param string $suffix Alguns xsd possuem sulfixos
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function validar($versao, $body, $model, $method = '', $suffix = 'v', $cmun = null)
    {
        $ver = str_pad($versao, 2, '0', STR_PAD_LEFT);
        $cmunPath = $this->pathSchemes . DIRECTORY_SEPARATOR . 'Counties' . DIRECTORY_SEPARATOR . "M{$cmun}" . DIRECTORY_SEPARATOR;

        if ($cmun && is_dir($cmunPath)) {
            $path = $cmunPath;
        } else {
            $path = $this->pathSchemes . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $model . DIRECTORY_SEPARATOR;
        }

        $schema = $path . "v$ver" . DIRECTORY_SEPARATOR . $method . ".xsd";
        if ($suffix) {
            $schema = $path . "{$suffix}{$ver}" . DIRECTORY_SEPARATOR . $method . "_{$suffix}{$ver}.xsd";
        }

        if (!is_file($schema)) {
            throw new \InvalidArgumentException("XSD file not found. [$schema]");
        }
        return Validator::isValid(
            $body,
            $schema
        );
    }

    /**
     * Bild signature tag
     * @param string $content
     * @param string $method
     * @param string $mark
     * @param array $canonical
     * @return string
     */
    public function signer($content, $method, $mark = '', array $canonical = [], $rootname = '')
    {
        $content = str_replace("\n", "", $content);
        if (empty($canonical)) {
            $canonical = [false, false, null, null];
        }
        return Signer::sign($this->certificate, $content, $method, $mark, $this->algorithm, $canonical, $rootname);
    }
}
