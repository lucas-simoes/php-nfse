<?php

namespace NFePHP\NFSe\Counties\M3157203\v203;


use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Counties\M3157203\RenderRps;
use NFePHP\NFSe\Models\Abrasf\Factories\v203\RecepcionarLoteRps as RecepcionarLoteRps203;
use NFePHP\NFSe\Counties\M3157203\SignerRps as Signer;
use NFePHP\NFSe\Counties\M3157203\Tools\prefixos;

class RecepcionarLoteRps extends RecepcionarLoteRps203
{
    private static $canonical = [true, false, null, null];  
        
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $lote,
        $rpss
    ) {

        $xsd = "nfse_v{$versao}";
        $qtdRps = count($rpss);


        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;

        $root = $dom->createElement('nfs:EnviarLoteRpsEnvio');
        $root->setAttribute('xmlns:sis', prefixos['xmlns:sis']);        
        $root->setAttribute('xmlns:nfs', prefixos['xmlns:nfs']);
        $root->setAttribute('xmlns:dsi', prefixos['xmlns:dsi']);

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $loteRps = $dom->createElement('nfs:LoteRps');        
        
        $loteRps->setAttribute('Id', "lote{$lote}");
        $loteRps->setAttribute('versao', '2.03');

        $dom->appChild($root, $loteRps, 'Adicionando tag LoteRps a EnviarLoteRpsEnvio');

        $dom->addChild(
            $loteRps,
            'nfs:NumeroLote',
            $lote,
            true,
            "Numero do lote RPS",
            true
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
            "nfs:Cpf / Cnpj",
            true
        );
        $dom->appChild($loteRps, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');

        /* Inscrição Municipal */
        $dom->addChild(
            $loteRps,
            'nfs:InscricaoMunicipal',
            $inscricaoMunicipal,
            false,
            "Inscricao Municipal",
            false
        );

        /* Quantidade de RPSs */
        $dom->addChild(
            $loteRps,
            'nfs:QuantidadeRps',
            $qtdRps,
            true,
            "Quantidade de Rps",
            true
        );

        /* Lista de RPS */
        $listaRps = $dom->createElement('nfs:ListaRps');
        $dom->appChild($loteRps, $listaRps, 'Adicionando tag ListaRps a LoteRps');

        foreach ($rpss as $rps) {
            RenderRps::appendRps($rps, $this->timezone, $this->certificate, $this->algorithm, $dom, $listaRps);
        }

        $xml = $dom->saveXML();

        $body = Signer::signPack(
            $this->certificate,
            $xml,
            'LoteRps',
            'Id',
            $this->algorithm,
            [false, false, null, null],
            '',
            true
        );

        $body = '<?xml version="1.0" encoding="utf-8"?>' . $body;        

        $body = $this->clear($body);
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '');   
        
        $body = str_replace('nfs:EnviarLoteRpsEnvio', 'sis:EnviarLoteRpsEnvio', $body);

        return $body;
    }
}
