<?php


namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\Factory;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;

class ConsultarNfsePorRps extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $cnpj
     * @param $im
     * @param $numero
     * @param $serie
     * @param $tipo
     * @return mixed
     */
    public function render(
        $versao,
        $cnpj,
        $im,
        $numero,
        $serie,
        $tipo
    ) {
        
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseRpsEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);
        
        //Cria os dados da IdentificacaoRps
        $identificacaoRps = $dom->createElement('IdentificacaoRps');
        
        //Adiciona o Numero do rps na tag Numero
        $dom->addChild(
            $identificacaoRps,
            'Numero',
            $numero,
            true,
            "Numero",
            true
        );

        //Adiciona a serie do rps na tag Serie
        $dom->addChild(
            $identificacaoRps,
            'Serie',
            $serie,
            true,
            "Serie",
            true
        );

        //Adiciona o tipo do rps na tag Tipo
        $dom->addChild(
            $identificacaoRps,
            'Tipo',
            $tipo,
            true,
            "Tipo",
            true
        );
        
        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $identificacaoRps, 'Adicionando tag IdentificacaoRps');

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');
        $prestador->setAttribute('id','assinar');
        //Adiciona o Cnpj na tag Cnpj
        $dom->addChild(
            $prestador,
            'Cnpj',
            $cnpj,
            true,
            "Cnpj",
            true
        );

        //Adiciona a InscricaoMunicipal na tag InscricaoMunicipal
        $dom->addChild(
            $prestador,
            'InscricaoMunicipal',
            $im,
            true,
            "InscricaoMunicipal",
            true
        );
        
        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        
        $body = SignerRps::sign(
            $this->certificate,            
            'Prestador',
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
