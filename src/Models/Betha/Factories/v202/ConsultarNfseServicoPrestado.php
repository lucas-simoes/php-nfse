<?php

namespace NFePHP\NFSe\Models\Betha\Factories\v202;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\NfseServicoPrestado;
use NFePHP\NFSe\Models\Abrasf\Factories\ConsultarNfseServicoPrestado as Base;

class ConsultarNfseServicoPrestado extends Base
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @return mixed
     */
    public function render(
        $versao,
        NfseServicoPrestado $nsPrestado
    ) { {
            $xsd = "nfse_v{$versao}";
            $dom = new Dom('1.0', 'utf-8');
            //Cria o elemento pai
            $root = $dom->createElement('ConsultarNfseServicoPrestadoEnvio');
            $root->setAttribute('xmlns', $this->xmlns);

            //Adiciona as tags ao DOM
            $dom->appendChild($root);

            //Cria os dados do prestador
            $prestador = $dom->createElement('Prestador');

            /* CPF CNPJ */
            $cpfCnpj = $dom->createElement('CpfCnpj');

            if ($nsPrestado->infPrestador['tipo'] == '2') {
                $tag = 'Cnpj';
            } else {
                $tag = 'Cpf';
            }
            //Adiciona o Cpf/Cnpj na tag CpfCnpj
            $dom->addChild(
                $cpfCnpj,
                $tag,
                $nsPrestado->infPrestador['cnpjcpf'],
                true,
                "Cpf / Cnpj",
                true
            );
            $dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');
            // //Adiciona a Inscrição Municipal na tag Prestador
            $dom->addChild(
                $prestador,
                'InscricaoMunicipal',
                $nsPrestado->infPrestador['im'],
                true,
                "InscricaoMunicipal",
                true
            );


            //Adiciona a tag Prestador a consulta
            $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

            //Adiciona a tag NumeroNfseInicial na Faixa
            $dom->addChild(
                $root,
                'NumeroNfse',
                $nsPrestado->infNumeroNfse,
                true,
                "NumeroNfse",
                true
            );

            if (!empty($nsPrestado->infDataEmissaoInicial)) {


                //Cria os dados do PeriodoEmissao
                $periodoEmissao = $dom->createElement('PeriodoEmissao');

                //Adiciona a tag NumeroNfseInicial na Faixa
                $dom->addChild(
                    $periodoEmissao,
                    'DataInicial',
                    $nsPrestado->infDataEmissaoInicial->format('Y-m-d'),
                    true,
                    "DataInicial",
                    true
                );

                $dom->addChild(
                    $periodoEmissao,
                    'DataFinal',
                    $nsPrestado->infDataEmissaoFinal->format('Y-m-d'),
                    true,
                    "DataFinal",
                    true
                );

                //Adiciona a tag PeriodoEmissao a consulta
                $dom->appChild($root, $periodoEmissao, 'Adicionando tag PeriodoEmissao');
            }

            if (!empty($nsPrestado->infDataCompetenciaInicial)) {
                //Cria os dados do PeriodoCompetencia
                $periodoCompetencia = $dom->createElement('PeriodoCompetencia');

                //Adiciona a tag NumeroNfseInicial na Faixa
                $dom->addChild(
                    $periodoCompetencia,
                    'DataInicial',
                    $nsPrestado->infDataCompetenciaInicial->format('Y-m-d'),
                    true,
                    "DataInicial",
                    true
                );

                $dom->addChild(
                    $periodoCompetencia,
                    'DataFinal',
                    $nsPrestado->infDataCompetenciaFinal->format('Y-m-d'),
                    true,
                    "DataFinal",
                    true
                );

                //Adiciona a tag PeriodoCompetencia a consulta
                $dom->appChild($root, $periodoCompetencia, 'Adicionando tag PeriodoCompetencia');
            }
            //Cria os dados do tomador
            $tomador = $dom->createElement('Tomador');

            /* CPF CNPJ */
            $cpfCnpj = $dom->createElement('CpfCnpj');

            if ($nsPrestado->infTomador['tipo'] == '2') {
                $tag = 'Cnpj';
            } else {
                $tag = 'Cpf';
            }
            //Adiciona o Cpf/Cnpj na tag CpfCnpj
            $dom->addChild(
                $cpfCnpj,
                $tag,
                $nsPrestado->infTomador['cnpjcpf'],
                true,
                "Cpf / Cnpj",
                true
            );
            $dom->appChild($tomador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Tomador');
            // //Adiciona a Inscrição Municipal na tag Tomador
            $dom->addChild(
                $tomador,
                'InscricaoMunicipal',
                $nsPrestado->infTomador['im'],
                true,
                "InscricaoMunicipal",
                true
            );

            //Adiciona a tag Tomador a consulta
            $dom->appChild($root, $tomador, 'Adicionando tag Tomador');

            /** Intermediario **/
            if (!empty($nsPrestado->infIntermediario['tipo'])) {
                $intermediario = $dom->createElement('Intermediario');
                $cpfCnpj = $dom->createElement('CpfCnpj');
                if ($nsPrestado->infIntermediario['tipo'] == 2) {
                    $dom->addChild(
                        $cpfCnpj,
                        'Cnpj',
                        $nsPrestado->infIntermediario['cnpjcpf'],
                        true,
                        'CNPJ Intermediario',
                        false
                    );
                } elseif ($nsPrestado->infIntermediario['tipo'] == 1) {
                    $dom->addChild(
                        $cpfCnpj,
                        'Cpf',
                        $nsPrestado->infIntermediario['cnpjcpf'],
                        true,
                        'CPF Intermediario',
                        false
                    );
                }
                $dom->appChild($intermediario, $cpfCnpj, 'Adicionando tag CpfCnpj em Intermediario');
                $dom->addChild(
                    $intermediario,
                    'InscricaoMunicipal',
                    $nsPrestado->infIntermediario['im'],
                    false,
                    'IM Intermediario',
                    false
                );
                $dom->appChild($root, $intermediario, 'Adicionando tag Intermediario em infnsPrestado');
            }

            $dom->addChild(
                $root,
                'Pagina',
                $nsPrestado->infPagina,
                true,
                "Pagina",
                true
            );

            $body = $dom->saveXML();
            $body = $this->clear($body);
            $this->validar($versao, $body, $this->schemeFolder, $xsd, '');
            #echo '<pre>' . print_r($body) . '</pre>';die;

            return $body;
        }
    }
}
