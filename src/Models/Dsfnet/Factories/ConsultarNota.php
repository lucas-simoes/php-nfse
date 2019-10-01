<?php

namespace NFePHP\NFSe\Models\Dsfnet\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Consulta de Notas dos webservices
 * conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Factories\ConsultarNota
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Signer;

class ConsultarNota extends Factory
{
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $codcidade,
        $prestadorIM,
        $dtInicio,
        $dtFim,
        $notaInicial
    ) {
        $method = "ReqConsultaNotas";
        $content = $this->requestFirstPart($method);
        $content .= Header::render(
            $versao,
            $remetenteCNPJCPF,
            null,
            null,
            $codcidade,
            null,
            null,
            $prestadorIM,
            null,
            null,
            $dtInicio,
            $dtFim,
            $notaInicial
        );
        $content .= "</ns1:$method>";
        $content = $this->signer($content, $method, 'Consulta:notas', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Dsfnet', $method, '');
        return $body;
    }
}
