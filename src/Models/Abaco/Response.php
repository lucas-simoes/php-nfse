<?php

namespace NFePHP\NFSe\Models\Abaco;

/**
 * Classe para extração dos dados retornados pelos webservices
 * conforme o modelo Abaco
 * NOTA: Abaco extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abaco\Response
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Factories\v100\Response as ResponseAbrasf;

class Response extends ResponseAbrasf
{
    public function read($response)
    {
        $data = parent::read($response);
        $data = reset($data);

        return $data;
    }
}
