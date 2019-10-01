<?php

namespace NFePHP\NFSe\Models\Abrasf;

/**
 * Classe para a renderização dos RPS em XML
 * conforme o modelo Abrasf
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abrasf\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;

abstract class RenderRps
{
    /**
     * @var DOMImproved
     */
    protected static $dom;
    /**
     * @var Certificate
     */
    protected static $certificate;
    /**
     * @var int
     */
    protected static $algorithm;
    /**
     * @var \DateTimeZone
     */
    protected static $timezone;

    /**
     * @param $data
     * @param \DateTimeZone $timezone
     * @param Certificate $certificate
     * @param int $algorithm
     */
    public static function toXml(
        $data,
        \DateTimeZone $timezone,
        Certificate $certificate,
        $algorithm = OPENSSL_ALGO_SHA1
    ) {
    }

    /**
     * @param $data
     * @param \DateTimeZone $timezone
     * @param Certificate $certificate
     * @param int $algorithm
     * @param $dom
     * @param $element
     */
    public static function appendRps(
        $data,
        \DateTimeZone $timezone,
        Certificate $certificate,
        $algorithm = OPENSSL_ALGO_SHA1,
        &$dom,
        &$element
    ) {
    }

    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @param $dom
     * @param $parent
     */
    protected static function render(Rps $rps, &$dom, &$parent)
    {
    }
}
