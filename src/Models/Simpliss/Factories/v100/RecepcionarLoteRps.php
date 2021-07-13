<?php

namespace NFePHP\NFSe\Models\Simpliss\Factories\v100;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\RenderRps;
use NFePHP\NFSe\Models\Simpliss\Factories\Signer;
use NFePHP\NFSe\Models\Simpliss\Factories\Factory;

class RecepcionarLoteRps extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteTipoDoc
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     * @param $lote
     * @param $rpss
     * @return mixed
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $lote,
        $rpss
    ) {

        $xsd = "servico_enviar_lote_rps_envio";
        $qtdRps = count($rpss);


        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;

        $root = $dom->createElement('EnviarLoteRpsEnvio');

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
            true
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
        $dom->appChild($loteRps, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');

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

        if ($this->certificate) {
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
        }

        //Parse para XML
        $body = $dom->saveXML();
        $body = $this->clear($body);
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '', $this->cmun);
        #echo '<pre>'.print_r($body).'</pre>';die;
        return '<?xml version="1.0" encoding="utf-8"?>' . $body;
    }
}
