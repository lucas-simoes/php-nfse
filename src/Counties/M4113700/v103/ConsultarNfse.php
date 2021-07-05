<?php

namespace NFePHP\NFSe\Counties\M4113700\v103;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\SIGISS\Factories\Factory;

class ConsultarNfse extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $rpss
     * @return string
     */
    public function render(
        $versao,
        $rps        
    ) {
        $method = 'ConsultarNfseServicoPrestado';
        $xsd = "nfse-londrina-schema-v1_03";

        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseServicoPrestadoEnvio');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $dom->addChild(
            $root,
            'ccm',
            $rps->infPrestador['ccm'],
            true,
            "CMC do prestador de serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'cnpj',
            $rps->infPrestador['cnpjcpf'],
            true,
            "CNPJ do prestador de serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'cpf',
            $rps->infPrestador['cpf_usuario'],
            true,
            "CPF do usuário cadastrado",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'senha',
            $rps->infPrestador['senha_usuario'],
            true,
            "Senha do prestador do serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'numero_nfse',
            $rps->infNumero,
            true,
            "Número da NFS-e procurada",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $body = $this->clear($body);
        #$this->validar($versao, $body, "SIGISS/Londrina", $xsd, '');
        return $body;
    }
}
