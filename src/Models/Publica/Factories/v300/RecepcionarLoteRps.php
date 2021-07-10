<?php

namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\RecepcionarLoteRps as RecepcionarLoteRpsBase;
use NFePHP\NFSe\Models\Publica\Factories\Signer;

class RecepcionarLoteRps extends RecepcionarLoteRpsBase
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteTipoDoc
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     * @param $lote
     * @param $rpss
     * @return string
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $lote,
        $rpss
    ) {
        $method = 'EnviarLoteRpsEnvio';
        $xsd = "schema_nfse_v300";
        $qtdRps = count($rpss);


        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('EnviarLoteRpsEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.publica.inf.br schema_nfse_v03.xsd');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $loteRps = $dom->createElement('LoteRps');        
        $loteRps->setAttribute('versao', '1.00');

        $dom->appChild($root, $loteRps, 'Adicionando tag LoteRps a EnviarLoteRpsEnvio');


        $dom->addChild(
            $loteRps,
            'NumeroLote',
            $lote,
            true,
            "Numero do lote RPS",
            true
        );

        //Adiciona o Cpf/Cnpj na tag CpfCnpj
        $dom->addChild(
            $loteRps,
            'Cnpj',
            $remetenteCNPJCPF,
            true,
            "Cpf / Cnpj",
            true
        );
        

        /* Inscrição Municipal */
        $dom->addChild(
            $loteRps,
            'InscricaoMunicipal',
            $inscricaoMunicipal,
            false,
            "Inscricao Municipal",
            false
        );

        /* Quantidade de RPSs */
        $dom->addChild(
            $loteRps,
            'QuantidadeRps',
            $qtdRps,
            true,
            "Quantidade de Rps",
            true
        );

        /* Lista de RPS */
        $listaRps = $dom->createElement('ListaRps');
        $dom->appChild($loteRps, $listaRps, 'Adicionando tag ListaRps a LoteRps');

        foreach ($rpss as $rps) {
            RenderRps::appendRps($rps, $this->timezone, $this->certificate, $this->algorithm, $dom, $listaRps);
        }


        //Parse para XML
        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '');

        return $body;
    }
}
