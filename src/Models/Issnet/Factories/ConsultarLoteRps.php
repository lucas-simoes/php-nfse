<?php

namespace NFePHP\NFSe\Models\Issnet\Factories;

class ConsultarLoteRps extends Factory
{
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $protocolo
    ) {
        $method = "ConsultarLoteRpsEnvio";
        $xsd = 'servico_consultar_lote_rps_envio';
        $content = $this->requestFirstPart($method, $xsd);
        $content .= Header::render($remetenteTipoDoc, $remetenteCNPJCPF, $inscricaoMunicipal);
        $content .= "<Protocolo>$protocolo</Protocolo>";
        $content .= "</$method>";
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Issnet', $xsd, '');
        return $body;
    }
}
