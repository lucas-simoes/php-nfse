<?php

namespace NFePHP\NFSe\Models\Issnet\Factories;

class ConsultarUrlVisualizacaoNfse extends Factory
{
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $numero,
        $codigoTributacao
    ) {
        $method = "ConsultarUrlVisualizacaoNfseEnvio";
        $xsd = 'servico_consultar_url_visualizacao_nfse_envio';
        $content = $this->requestFirstPart($method, $xsd);
        $content .= Header::render($remetenteTipoDoc, $remetenteCNPJCPF, $inscricaoMunicipal);
        $content .= "<Numero>$numero</Numero>";
        $content .= "<CodigoTributacaoMunicipio>$codigoTributacao</CodigoTributacaoMunicipio>";
        $content .= "</$method>";
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Issnet', $xsd, '');
        return $body;
    }
}
