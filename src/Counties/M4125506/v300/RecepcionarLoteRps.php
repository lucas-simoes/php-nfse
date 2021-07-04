<?php

namespace NFePHP\NFSe\Counties\M4125506\v300;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\RecepcionarLoteRps as RecepcionarLoteRpsBase;
use NFePHP\NFSe\Models\Abrasf\Factories\Signer;

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
        $xsd = "servico_enviar_lote_rps_envio_v03";
        $qtdRps = count($rpss);


        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('EnviarLoteRpsEnvio');
        $root->setAttribute('xmlns', "http://nfe.sjp.pr.gov.br/$xsd.xsd");

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $loteRps = $dom->createElement('LoteRps');
        $loteRps->setAttribute('Id', "lote{$lote}");

        $dom->appChild($root, $loteRps, 'Adicionando tag LoteRps a EnviarLoteRpsEnvio');


        $dom->addChild(
            $loteRps,
            'NumeroLote',
            $lote,
            true,
            "Numero do lote RPS",
            true,
            [['attr' => 'xmlns', 'value' => 'http://nfe.sjp.pr.gov.br/tipos_v03.xsd']]
        );

        $dom->addChild(
            $loteRps,
            'Cnpj',
            $remetenteCNPJCPF,
            true,
            "Cnpj",
            true,
            [['attr' => 'xmlns', 'value' => 'http://nfe.sjp.pr.gov.br/tipos_v03.xsd']]
        );

        /* Inscrição Municipal */
        $dom->addChild(
            $loteRps,
            'InscricaoMunicipal',
            $inscricaoMunicipal,
            false,
            "Inscricao Municipal",
            false,
            [['attr' => 'xmlns', 'value' => 'http://nfe.sjp.pr.gov.br/tipos_v03.xsd']]
        );

        /* Quantidade de RPSs */
        $dom->addChild(
            $loteRps,
            'QuantidadeRps',
            $qtdRps,
            true,
            "Quantidade de Rps",
            true,
            [['attr' => 'xmlns', 'value' => 'http://nfe.sjp.pr.gov.br/tipos_v03.xsd']]
        );

        /* Lista de RPS */
        $listaRps = $dom->createElement('ListaRps');
        $listaRps->setAttribute('xmlns', 'http://nfe.sjp.pr.gov.br/tipos_v03.xsd');
        $dom->appChild($loteRps, $listaRps, 'Adicionando tag ListaRps a LoteRps');
 
        foreach ($rpss as $rps) {
            RenderRps::appendRps($rps, $this->timezone, $this->certificate, $this->algorithm, $dom, $listaRps);
        }


        //Parse para XML
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

        $body = Signer::sign(
            $this->certificate,
            $xml,
            'LoteRps',
            'Id',
            $this->algorithm,
            [false, false, null, null],
            '',
            true
        );
        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        $this->validar($versao, $body, $this->schemeFolder . "/SJP", $xsd, '');
        return $body;
    }
}
