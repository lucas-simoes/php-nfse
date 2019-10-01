<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories\v100;

/**
 * Classe para extração dos dados retornados pelos webservices
 * conforme o modelo ABRASF 2.02
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abrasf\Response
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Response as ResponseBase;

class Response extends ResponseBase
{
    /**
     * Le a resposta do Webservice
     * @param $response
     * @return array|mixed|string
     */
    public function read($response)
    {
        $data = parent::read($response);
        $data = reset($data);
        $data = reset($data);
        $data = reset($data);

        return $data;
    }
}