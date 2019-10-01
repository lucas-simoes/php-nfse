<?php

namespace NFePHP\NFSe\Models\Prodam\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Consulta de NFSe para os webservices da
 * Cidade de São Paulo conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Factories\ConsultaNFSe
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use InvalidArgumentException;

class ConsultaNFSe extends Factory
{
    /**
     * Renderiza o pedido em seu respectivo xml
     * @param int $versao
     * @param int $remetenteTipoDoc
     * @param string $remetenteCNPJCPF
     * @param string $transacao
     * @param array $chavesNFSe
     * @param array $chavesRPS
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $transacao = '',
        $chavesNFSe = [],
        $chavesRPS = []
    ) {
        $method = "PedidoConsultaNFe";
        $content = $this->requestFirstPart($method);
        $content .= Header::render($versao, $remetenteTipoDoc, $remetenteCNPJCPF, $transacao);
        //minimo 1 e maximo de 50 objetos podem ser consultados
        $total = count($chavesNFSe) + count($chavesRPS);
        if ($total == 0 || $total > 50) {
            $msg = "Na consulta deve haver pelo menos uma chave e no máximo 50. Fornecido: $total";
            throw new InvalidArgumentException($msg);
        }
        //para cada chave montar um detalhe
        foreach ($chavesNFSe as $chave) {
            $content .= "<Detalhe xmlns=\"\">";
            $content .= "<ChaveNFe>";
            $content .= "<InscricaoPrestador>" . $chave['prestadorIM'] . "</InscricaoPrestador>";
            $content .= "<NumeroNFe>" . $chave['numeroNFSe'] . "</NumeroNFe>";
            $content .= "</ChaveNFe>";
            $content .= "</Detalhe>";
        }
        foreach ($chavesRPS as $chave) {
            $content .= "<Detalhe xmlns=\"\">";
            $content .= "<ChaveRPS>";
            $content .= "<InscricaoPrestador>" . $chave['prestadorIM'] . "</InscricaoPrestador>";
            $content .= "<SerieRPS>" . $chave['serieRPS'] . "</SerieRPS>";
            $content .= "<NumeroRPS>" . $chave['numeroRPS'] . "</NumeroRPS>";
            $content .= "</ChaveRPS>";
            $content .= "</Detalhe>";
        }
        $content .= "</$method>";
        $content = $this->signer($content, $method, '', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Prodam', $method);
        return $body;
    }
}
