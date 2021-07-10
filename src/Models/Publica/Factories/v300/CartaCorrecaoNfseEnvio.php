<?php


namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\NFSe\Models\Publica\Factories\CartaCorrecaoNfseEnvio as CartaCorrecaoNfseEnvioBase;
use NFePHP\Common\DOMImproved as Dom;

class CartaCorrecaoNfseEnvio extends CartaCorrecaoNfseEnvioBase
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $rps
     * @return bool|string
     */
    public function render(
        $versao,
        $rps
    ) {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('CartaCorrecaoNfseEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        //Cria a tag Pedido
        $pedido = $dom->createElement('Pedido');
        //Cria a tag InfPedidoCartaCorrecao
        $infPedidoCartaCorrecao = $dom->createElement('InfPedidoCartaCorrecao');
        RenderRps::appendRps($rps, $this->timezone, $this->certificate, $this->algorithm, $dom, $root);

        //Parse para XML
        $xml = $dom->saveXML();
        $xml = $this->clear($xml);
        $this->validar($versao, $xml, $this->schemeFolder, 'schema_nfse_v300', '');
        #echo '<pre>'.print_r($xml).'</pre>';die;
        return $xml;
    }
}
