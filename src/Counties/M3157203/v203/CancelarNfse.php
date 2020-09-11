<?php

namespace NFePHP\NFSe\Counties\M3157203\v203;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Counties\M3157203\SignerRps as Signer;
use NFePHP\NFSe\Models\Abrasf\Factories\Factory;

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
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $nfseNumero
    ) 
    {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('sis:CancelarNfseEnvio');
        $root->setAttribute('xmlns:sis', prefixos['xmlns:sis']);        
        $root->setAttribute('xmlns:nfs', prefixos['xmlns:nfs']);

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $loteRps = $dom->createElement('nfs:Pedido');

        $dom->appChild($root, $loteRps, 'Adicionando tag Pedido');
        
        $InfPedidoCancelamento = $dom->createElement('nfs:InfPedidoCancelamento');

        $dom->appChild(
            $loteRps,
            $InfPedidoCancelamento,
            "Inf Pedido Cancelamento"
        );
        
        $identificacaoNfse = $dom->createElement('nfs:IdentificacaoNfse');
        
        $dom->appChild(
            $InfPedidoCancelamento,
            $identificacaoNfse,
            'Identificação da Nfse'
        );
        
        /* Inscrição Municipal */
        $dom->addChild(
            $identificacaoNfse,
            'nfs:Numero',
            $nfseNumero,
            false,
            "Numero NFse",
            false
        );

        /* CPF CNPJ */
        $cpfCnpj = $dom->createElement('nfs:CpfCnpj');

        if ($remetenteTipoDoc == '2') {
            $tag = 'nfs:Cnpj';
        } else {
            $tag = 'nfs:Cpf';
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
            'nfs:InscricaoMunicipal',
            $inscricaoMunicipal,
            false,
            "Inscricao Municipal",
            false
        );
        
        /* Código do Municipio */
        $dom->addChild(
            $identificacaoNfse,
            'nfs:CodigoMunicipio',
            $this->codMun,
            false,
            "Código Municipio",
            false
        );
        
        /* Código do Cancelamento */
        $dom->addChild(
            $InfPedidoCancelamento,
            'nfs:CodigoCancelamento',
            2,
            false,
            "Código Municipio",
            false
        );

        //Parse para XML
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

        $body = Signer::signDoc(
            $this->certificate,
            'nfs:Pedido',
            'Id',
            $this->algorithm,
            [false, false, null, null],
            $dom,
            $root
        );

        $body = $dom->saveXML();

        $body = $this->clear($body);        

        return $body;
    }
}
