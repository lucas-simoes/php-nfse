<?php

namespace NFePHP\NFSe\Common;

/**
 * Classe base para a construção dos cabaçalhos XML relativo aos serviços
 * dos webservices
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Header
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class Header
{
    protected static function check($tag, $info = null)
    {
        if (is_null($info)) {
            return '';
        }
        return "<$tag>$info</$tag>";
    }
}
