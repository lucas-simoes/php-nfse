<?php

namespace NFePHP\NFSe\Models\Dsfnet\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Consulta de Lote de NFSe dos webservices da
 * conforme o modelo DSFNET
 *
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Factories\ConsultarLote
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class ConsultarLote extends Factory
{
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $codcidade,
        $numeroLote
    ) {
        $method = "ReqConsultaLote";
        $content = $this->requestFirstPart($method);
        $content .= Header::render(
            $versao,
            $remetenteCNPJCPF,
            null, //remetenteRazao
            null, //$transacao
            $codcidade,
            null, //$codcid
            null, //$token
            null, //$prestadorIM
            null, //$seriePrestacao
            $numeroLote //$numeroLote
        );
        $content .= "</ns1:$method>";
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Dsfnet', $method, '');
        return $body;
    }
}
