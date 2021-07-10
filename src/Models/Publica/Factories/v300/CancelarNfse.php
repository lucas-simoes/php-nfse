<?php

namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\Factory;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;

class CancelarNfse extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     * @param $codMunicipio
     * @param $nfseNumero
     * @param $codCancelamento
     * @return string
     */
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $codMunicipio,
        $nfseNumero,
        $codCancelamento
    ) {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('CancelarNfseEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $pedido = $dom->createElement('Pedido');

        $dom->appChild($root, $pedido, 'Adicionando tag Pedido');

        $InfPedidoCancelamento = $dom->createElement('InfPedidoCancelamento');
        $InfPedidoCancelamento->setAttribute('id', 'assinar');

        $dom->appChild($pedido, $InfPedidoCancelamento, "Inf Pedido Cancelamento");

        $identificacaoNfse = $dom->createElement('IdentificacaoNfse');

        $dom->appChild(
            $InfPedidoCancelamento,
            $identificacaoNfse,
            'Identificação da Nfse'
        );

        /* Inscrição Municipal */
        $dom->addChild(
            $identificacaoNfse,
            'Numero',
            $nfseNumero,
            false,
            "Numero NFse",
            false
        );

        //Adiciona o Cpf/Cnpj na tag CpfCnpj
        $dom->addChild(
            $identificacaoNfse,
            'Cnpj',
            $remetenteCNPJCPF,
            true,
            "Cpf / Cnpj",
            true
        );
 
        /* Inscrição Municipal */
        $dom->addChild(
            $identificacaoNfse,
            'InscricaoMunicipal',
            $inscricaoMunicipal,
            false,
            "Inscricao Municipal",
            false
        );

        /* Código do Municipio */
        $dom->addChild(
            $identificacaoNfse,
            'CodigoMunicipio',
            $codMunicipio,
            false,
            "Código Municipio",
            false
        );

        //Adiciona a tag Prestador a consulta
        $dom->appChild($InfPedidoCancelamento, $identificacaoNfse, 'Adicionando tag InfPedidoCancelamento');

        /* Código do Cancelamento */
        $dom->addChild(
            $InfPedidoCancelamento,
            'CodigoCancelamento',
            $codCancelamento,
            false,
            "Codigo Cancelamento",
            false
        );

        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $body = SignerRps::sign(
            $this->certificate,
            'InfPedidoCancelamento',
            'id',
            $this->algorithm,
            [true, false, null, null],
            $dom,
            $pedido
        );

        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, 'Publica', 'schema_nfse_v300', '');

        #echo '<pre>' . print_r($body) . '</pre>';die;

        return $body;
    }
}
