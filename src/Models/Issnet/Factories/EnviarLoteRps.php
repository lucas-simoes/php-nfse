<?php

namespace NFePHP\NFSe\Models\Issnet\Factories;

use NFePHP\NFSe\Models\Issnet\RenderRPS;

class EnviarLoteRps extends Factory
{
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $lote,
        $rpss
    ) {
        $method = 'EnviarLoteRpsEnvio';
        $xsd = 'servico_enviar_lote_rps_envio';
        $qtdRps = count($rpss);
        $content = "<EnviarLoteRpsEnvio "
            . "xmlns=\"http://www.issnetonline.com.br/webserviceabrasf/vsd/$xsd.xsd\" "
            . "xmlns:tc=\"http://www.issnetonline.com.br/webserviceabrasf/vsd/tipos_complexos.xsd\">";
        $content .= "<LoteRps>";
        $content .= "<tc:NumeroLote>$lote</tc:NumeroLote>";
        $content .= "<tc:CpfCnpj>";
        if ($remetenteTipoDoc == '2') {
            $content .= "<tc:Cnpj>$remetenteCNPJCPF</tc:Cnpj>";
        } else {
            $content .= "<tc:Cpf>$remetenteCNPJCPF</tc:Cpf>";
        }
        $content .= "</tc:CpfCnpj>";
        $content .= "<tc:InscricaoMunicipal>$inscricaoMunicipal</tc:InscricaoMunicipal>";
        $content .= "<tc:QuantidadeRps>$qtdRps</tc:QuantidadeRps>";
        $content .= "<tc:ListaRps>";
        foreach ($rpss as $rps) {
            $content .= RenderRPS::toXml($rps, $this->timezone, $this->algorithm);
        }
        $content .= "</tc:ListaRps>";
        $content .= "</LoteRps>";
        $content .= "</EnviarLoteRpsEnvio>";

        $body = Signer::sign(
            $this->certificate,
            $content,
            'LoteRps',
            '',
            $this->algorithm,
            [false, false, null, null]
        );
        $body = $this->clear($body);
        $this->validar($versao, $body, 'Issnet', $xsd, '');
        return $body;
    }
}
