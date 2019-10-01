<?php

namespace NFePHP\NFSe\Models\Prodam\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Consulta do CNPJ do contribuinte para
 * os webservices da Cidade de São Paulo conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Factories\ConsultaCNPJ
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class ConsultaCNPJ extends Factory
{
    /**
     * Renderiza o Pedido de Consulta do CNPJ do contribuinte
     * e faz a validação com o xsd
     * @param int $versao
     * @param string $remetenteTipoDoc
     * @param string $remetenteCNPJCPF
     * @param string $transacao
     * @param string $cnpjContribuinte
     * @return string
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $transacao,
        $cnpjContribuinte
    ) {
        $method = 'PedidoConsultaCNPJ';
        $content = $this->requestFirstPart("$method");
        $content .= Header::render($versao, $remetenteTipoDoc, $remetenteCNPJCPF, $transacao);
        $content .= "<CNPJContribuinte xmlns=\"\">";
        $content .= "<CNPJ>$cnpjContribuinte</CNPJ>";
        $content .= "</CNPJContribuinte>";
        $content .= "</$method>";
        $content = $this->signer($content, $method, '', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Prodam', $method);
        return $body;
    }
}
