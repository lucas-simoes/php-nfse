<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories\v202;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\Factories\SignerRps;
use NFePHP\NFSe\Models\Abrasf\Factories\ConsultarNfsePorFaixa as ConsultarNfsePorFaixaBase;

class ConsultarNfsePorFaixa extends ConsultarNfsePorFaixaBase
{
    protected $xmlns = 'http://www.abrasf.org.br/nfse.xsd';
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteTipoDoc
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     * @param $numeroNfseInicial
     * @param $numeroNfseFinal
     * @param $pagina
     * @return mixed
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $numeroNfseInicial,
        $numeroNfseFinal,
        $pagina
    ) {
        $xsd = "nfse_v{$versao}";
        $dom = new Dom('1.0', 'utf-8');
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarNfseFaixaEnvio');
        $root->setAttribute('xmlns', $this->xmlns);

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        //Cria os dados do prestador
        $prestador = $dom->createElement('Prestador');

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
        $dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');
        
        // //Adiciona a Inscrição Municipal na tag Prestador
        $dom->addChild(
            $prestador,
            'InscricaoMunicipal',
            $inscricaoMunicipal,
            true,
            "InscricaoMunicipal",
            true
        );


        //Adiciona a tag Prestador a consulta
        $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

        //Cria o elemento Faixa
        $faixa = $dom->createElement('Faixa');
        //Adiciona a tag NumeroNfseInicial na Faixa
        $dom->addChild(
            $faixa,
            'NumeroNfseInicial',
            $numeroNfseInicial,
            true,
            "NumeroNfseInicial",
            true
        );
        //Adiciona a tag NumeroNfseInicial na Faixa
        $dom->addChild(
            $faixa,
            'NumeroNfseFinal',
            $numeroNfseFinal,
            true,
            "NumeroNfseFinal",
            true
        );

        //Adiciona a tag Faixa a consulta
        $dom->appChild($root, $faixa, 'Adicionando tag Faixa');

        $dom->addChild(
            $root,
            'Pagina',
            $pagina,
            true,
            "Pagina",
            true
        );

        $body = $dom->saveXML();
        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        $this->validar($versao, $body, $this->schemeFolder, $xsd, '');

        return $body;
    }
}
