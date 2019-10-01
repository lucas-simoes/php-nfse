<?php

namespace NFePHP\NFSe\Models\Infisc;

/**
 * Classe para extração dos dados retornados pelos webservices
 * conforme o modelo Infisc
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Infisc\Response
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Response as ResponseBase;

class Response extends ResponseBase
{
    public function readReturn($tag, $response)
    {
        return parent::readReturn($tag, $response);
    }
}
