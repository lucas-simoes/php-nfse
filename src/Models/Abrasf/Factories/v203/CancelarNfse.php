<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories\v203;

use NFePHP\NFSe\Models\Abrasf\Factories\Factory;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\Signer;

class CancelarNfse extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteTipoDoc
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     * @return string
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $nfseNumero
    ) 
    {
        $method = 'CancelarNfseEnvio';
        $xsd = "servico_cancelar_nfse_envio";


        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('CancelarNfseEnvio');
        //$root->setAttribute('xmlns', $this->xmlns);

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $loteRps = $dom->createElement('Pedido');

        $dom->appChild($root, $loteRps, 'Adicionando tag Pedido');
        
        $InfPedidoCancelamento = $dom->createElement('InfPedidoCancelamento');

        $dom->appChild(
            $loteRps,
            $InfPedidoCancelamento,
            "Inf Pedido Cancelamento"
        );
        
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

        /* CPF CNPJ */
        $cpfCnpj = $dom->createElement('CpfCnpj');

        if ($remetenteTipoDoc == '2') {
            $tag = 'Cnpj';
        } else {
            $tag = 'Cpf';
        }
        //Adiciona o Cpf/Cnpj na tag CpfCnpj
        $dom->addChild(
            $cpfCnpj,
            $tag,
            $remetenteCNPJCPF,
            true,
            "Cpf / Cnpj",
            true
        );
        $dom->appChild($identificacaoNfse, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');

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
            $this->codMun,
            false,
            "Código Municipio",
            false
        );
        
        /* Código do Cancelamento */
        $dom->addChild(
            $InfPedidoCancelamento,
            'CodigoCancelamento',
            2,
            false,
            "Código Municipio",
            false
        );

        //Parse para XML
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

        $body = Signer::sign(
            $this->certificate,
            $xml,
            'InfPedidoCancelamento',
            'Id',
            $this->algorithm,
            [false, false, null, null],
            'Pedido',
            true
        );
        $body = $this->clear($body);
        
        //$this->validar($versao, $body, $this->schemeFolder, $xsd, '');

        return $body;
    }
}
