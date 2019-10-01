<?php

namespace NFePHP\NFSe\Models\BHISS;

/**
 * Classe para extração dos dados retornados pelos webservices
 * conforme o modelo BHISS
 * NOTA: BHISS extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\BHISS\Response
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Response as ResponseBase;

class Response extends ResponseBase
{
    /**
     * Le o retorno do Webservice
     * @param $response
     * @return array|string
     */
    public function read($response)
    {
        $out = [];
        preg_match_all("/(<outputXML>)(.*)(<\/outputXML)/s", $response, $out);
        $response = $out[2][0];
        $data = parent::read($response);
        if (!empty($data['ListaMensagemRetornoLote'])) {
            $data['ListaMensagemRetorno'] = $data['ListaMensagemRetornoLote'];
            unset($data['ListaMensagemRetornoLote']);
        }

        return $data;
    }
}
