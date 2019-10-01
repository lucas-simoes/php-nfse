<?php

namespace NFePHP\NFSe\Models\Prodam\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Cancelamento de NFSe dos webservices da
 * Cidade de São Paulo conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Factories\CancelamentoNFSe
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class CancelamentoNFSe extends Factory
{
    /**
     * Renderiza o xml do Pedido de Cancelamento de NFSe
     * e faz a validação com o xsd
     * @param string $versao
     * @param int $remetenteTipoDoc
     * @param string $remetenteCNPJCPF
     * @param string $transacao '', 'true' ou 'false' como string
     * @param string $prestadorIM
     * @param string $numeroNFSe
     * @return string
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $transacao,
        $prestadorIM,
        $numeroNFSe
    ) {
        $method = "PedidoCancelamentoNFe";
        $content = $this->requestFirstPart($method);
        $content .= Header::render($versao, $remetenteTipoDoc, $remetenteCNPJCPF, $transacao);
        if (is_array($numeroNFSe)) {
            if (count($numeroNFSe) > 50) {
                throw InvalidArgumentException("No máximo pode ser solicitado o cancelamento de 50 NFSe por vez.");
            }
            foreach ($numeroNFSe as $num) {
                $content .= $this->detalhe($prestadorIM, $num);
            }
        } else {
            $content .= $this->detalhe($prestadorIM, $numeroNFSe);
        }
        $content .= "</$method>";
        $content = $this->signer($content, $method, '', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Prodam', $method);
        return $body;
    }

    private function detalhe($prestadorIM, $numeroNFSe)
    {
        $signString = str_pad($prestadorIM, 8, '0', STR_PAD_LEFT)
            . str_pad($numeroNFSe, 12, '0', STR_PAD_LEFT);
        $detalhe = "<Detalhe xmlns=\"\">";
        $detalhe .= "<ChaveNFe>";
        $detalhe .= "<InscricaoPrestador>$prestadorIM</InscricaoPrestador>";
        $detalhe .= "<NumeroNFe>$numeroNFSe</NumeroNFe>";
        $detalhe .= "</ChaveNFe>";
        $detalhe .= "<AssinaturaCancelamento>";
        $detalhe .= base64_encode($this->certificate->sign($signString, $this->algorithm));
        $detalhe .= "</AssinaturaCancelamento>";
        $detalhe .= "</Detalhe>";
        return $detalhe;
    }
}
