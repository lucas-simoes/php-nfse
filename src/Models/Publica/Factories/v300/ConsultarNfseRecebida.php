<?php

namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\Factory;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;

class ConsultarNfseRecebida extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $tipo
     * @param $cpfcnpj
     * @param $dataNfse
     * @return mixed
     */
    public function render(
        $versao,
        $tipo,
        $cpfcnpj,
        $dataNfse
    ) {

        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultaNfseRecebidaEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        //Cria os dados da ConsultaNfseRecebida
        $consultaNfseRecebida = $dom->createElement('ConsultaNfseRecebida');
        $consultaNfseRecebida->setAttribute('id', 'assinar');


        //Adiciona a tag ConsultaNfseRecebida a consulta
        $dom->appChild($root, $consultaNfseRecebida, 'Adicionando tag ConsultaNfseRecebida');

        //Cria os dados do IdentificacaoTomador
        $identificacaoTomador = $dom->createElement('IdentificacaoTomador');
        //Adiciona o Cnpj na tag Cnpj
        $cpfCnpjTomador = $dom->createElement('CpfCnpj');
        if ($tipo == 2) {
            $dom->addChild(
                $cpfCnpjTomador,
                'Cnpj',
                $cpfcnpj,
                true,
                'Tomador CNPJ',
                false
            );
        } else {
            $dom->addChild(
                $cpfCnpjTomador,
                'Cpf',
                $cpfcnpj,
                true,
                'Tomador CPF',
                false
            );
        }
        $dom->appChild(
            $identificacaoTomador,
            $cpfCnpjTomador,
            'Adicionando tag CpfCnpj em IdentificacaoTomador'
        );

        //Adiciona a tag IdentificacaoTomador a consulta
        $dom->appChild($consultaNfseRecebida, $identificacaoTomador, 'Adicionando tag Prestador');

        $dom->addChild(
            $consultaNfseRecebida,
            'DataNfse',
            $dataNfse->format('Y-m-d'),
            true,
            'Data da nfse',
            false
        );

        //Parse para XML
        $body = SignerRps::sign(
            $this->certificate,
            'ConsultaNfseRecebida',
            'id',
            $this->algorithm,
            [true, false, null, null],
            $dom,
            $root
        );

        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, 'Publica', 'schema_nfse_v300', '');
        return $body;
    }
}
