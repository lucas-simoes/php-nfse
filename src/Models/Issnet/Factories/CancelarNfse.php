<?php

namespace NFePHP\NFSe\Models\Issnet\Factories;

class CancelarNfse extends Factory
{
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $remetenteIM,
        $codigoMunicipio,
        $numero,
        $codigoCancelamento
    ) {
        $method = "CancelarNfseEnvio";
        $xsd = 'servico_cancelar_nfse_envio';

        $content = "<p1:" . $method . " "
            . "xmlns:p1=\"http://www.issnetonline.com.br/webserviceabrasf/vsd/$xsd.xsd\" "
            . "xmlns:tc=\"http://www.issnetonline.com.br/webserviceabrasf/vsd/tipos_complexos.xsd\" "
            . "xmlns:ts=\"http://www.issnetonline.com.br/webserviceabrasf/vsd/tipos_simples.xsd\""
            . ">";
        $content .= "<Pedido>";
        $content .= "<tc:InfPedidoCancelamento>";
        $content .= "<tc:IdentificacaoNfse>";
        $content .= "<tc:Numero>$numero</tc:Numero>";
        $content .= "<tc:Cnpj>$remetenteCNPJCPF</tc:Cnpj>";
        $content .= "<tc:InscricaoMunicipal>$remetenteIM</tc:InscricaoMunicipal>";
        $content .= "<tc:CodigoMunicipio>$codigoMunicipio</tc:CodigoMunicipio>";
        $content .= "</tc:IdentificacaoNfse>";
        $content .= "<tc:CodigoCancelamento>$codigoCancelamento</tc:CodigoCancelamento>";
        $content .= "</tc:InfPedidoCancelamento>";
        $content .= "</Pedido>";
        $content .= "</p1:CancelarNfseEnvio>";

        $body = Signer::sign(
            $this->certificate,
            $content,
            'InfPedidoCancelamento',
            'http://www.w3.org/TR/2000/REC-xhtml1-20000126/',
            $this->algorithm,
            [false, false, null, null],
            'Pedido'
        );
        $body = $this->clear($body);
        $this->validar($versao, $body, 'Issnet', $xsd, '');
        return $body;
    }
}
