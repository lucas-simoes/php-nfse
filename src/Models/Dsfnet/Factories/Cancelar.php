<?php

namespace NFePHP\NFSe\Models\Dsfnet\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Cancelamento de NFSe dos webservices da
 * conforme o modelo DSFNET
 *
 * NOTA: Este processo está limitado a apneas uma NFSe por vez!!
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Factories\Cancelar
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Signer;

class Cancelar extends Factory
{
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $transacao,
        $codcidade,
        $prestadorIM,
        $tokenEnvio,
        $lote,
        $numero,
        $codigoverificacao,
        $motivocancelamento
    ) {
        $method = "ReqCancelamentoNFSe";
        $content = $this->requestFirstPart($method);
        $content .= Header::render(
            $versao,
            $remetenteCNPJCPF,
            null,
            $transacao,
            $codcidade,
            null,
            $tokenEnvio
        );
        $content .= "<Lote Id=\"lote:$lote\">";
        $content .= "<Nota Id=\"nota:$numero\">";
        $content .= "<InscricaoMunicipalPrestador>$prestadorIM</InscricaoMunicipalPrestador>";
        $content .= "<NumeroNota>$numero</NumeroNota>";
        $content .= "<CodigoVerificacao>$codigoverificacao</CodigoVerificacao>";
        $content .= "<MotivoCancelamento>$motivocancelamento</MotivoCancelamento>";
        $content .= "</Nota>";
        $content .= "</Lote>";
        $content .= "</ns1:$method>";
        $content = $this->signer($content, 'Lote', 'Id', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Dsfnet', $method, '');
        return $body;
    }
}
