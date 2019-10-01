<?php

namespace NFePHP\NFSe\Models\Goiania;

/**
 * Classe para extração dos dados retornados pelos webservices
 * conforme o modelo Goiania
 * NOTA: Goiania extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Goiania\Response
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Response as ResponseAbrasf;

class Response extends ResponseAbrasf
{
    /**
     * Le a resposta do Webservice
     * @param $response
     * @return array|mixed|string
     */
    public function read($response)
    {
        $response = str_replace('<?xml version="1.0"?>','',$response);
        $data = parent::read($response);
        $data = reset($data);
        $data = reset($data);
        $data = reset($data);
        $data = reset($data);

        return $data;
    }
}
