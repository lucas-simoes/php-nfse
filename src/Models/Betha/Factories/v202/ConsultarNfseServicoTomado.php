<?php

namespace NFePHP\NFSe\Models\Betha\Factories\v202;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Abrasf\NfseServicoTomado;
use NFePHP\NFSe\Models\Abrasf\Factories\ConsultarNfseServicoTomado as Base;

class ConsultarNfseServicoTomado extends Base
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @return mixed
     */
    public function render(
        $versao,
        NfseServicoTomado $nsTomado
    ) { {
            $xsd = "nfse_v{$versao}";
            $dom = new Dom('1.0', 'utf-8');
            //Cria o elemento pai
            $root = $dom->createElement('ConsultarNfseServicoTomadoEnvio');
            $root->setAttribute('xmlns', $this->xmlns);

            //Adiciona as tags ao DOM
            $dom->appendChild($root);

            //Cria os dados do Consulente
            $consulente = $dom->createElement('Consulente');

            /* CPF CNPJ */
            $cpfCnpj = $dom->createElement('CpfCnpj');

            if ($nsTomado->infConsulente['tipo'] == '2') {
                $tag = 'Cnpj';
            } else {
                $tag = 'Cpf';
            }
            //Adiciona o Cpf/Cnpj na tag CpfCnpj
            $dom->addChild(
                $cpfCnpj,
                $tag,
                $nsTomado->infConsulente['cnpjcpf'],
                true,
                "Cpf / Cnpj",
                true
            );
            $dom->appChild($consulente, $cpfCnpj, 'Adicionando tag CpfCnpj ao Consulente');
            // //Adiciona a Inscrição Municipal na tag Consulente
            $dom->addChild(
                $consulente,
                'InscricaoMunicipal',
                $nsTomado->infConsulente['im'],
                true,
                "InscricaoMunicipal",
                true
            );

            //Adiciona a tag Consulente a consulta
            $dom->appChild($root, $consulente, 'Adicionando tag Consulente');

            //Adiciona a tag NumeroNfseInicial na Faixa
            $dom->addChild(
                $root,
                'NumeroNfse',
                $nsTomado->infNumeroNfse,
                true,
                "NumeroNfse",
                true
            );

            if (!empty($nsTomado->infDataEmissaoInicial)) {


                //Cria os dados do PeriodoEmissao
                $periodoEmissao = $dom->createElement('PeriodoEmissao');

                //Adiciona a tag NumeroNfseInicial na Faixa
                $dom->addChild(
                    $periodoEmissao,
                    'DataInicial',
                    $nsTomado->infDataEmissaoInicial->format('Y-m-d'),
                    true,
                    "DataInicial",
                    true
                );

                $dom->addChild(
                    $periodoEmissao,
                    'DataFinal',
                    $nsTomado->infDataEmissaoFinal->format('Y-m-d'),
                    true,
                    "DataFinal",
                    true
                );

                //Adiciona a tag PeriodoEmissao a consulta
                $dom->appChild($root, $periodoEmissao, 'Adicionando tag PeriodoEmissao');
            }

            if (!empty($nsTomado->infDataCompetenciaInicial)) {
                //Cria os dados do PeriodoCompetencia
                $periodoCompetencia = $dom->createElement('PeriodoCompetencia');

                //Adiciona a tag NumeroNfseInicial na Faixa
                $dom->addChild(
                    $periodoCompetencia,
                    'DataInicial',
                    $nsTomado->infDataCompetenciaInicial->format('Y-m-d'),
                    true,
                    "DataInicial",
                    true
                );

                $dom->addChild(
                    $periodoCompetencia,
                    'DataFinal',
                    $nsTomado->infDataCompetenciaFinal->format('Y-m-d'),
                    true,
                    "DataFinal",
                    true
                );

                //Adiciona a tag PeriodoCompetencia a consulta
                $dom->appChild($root, $periodoCompetencia, 'Adicionando tag PeriodoCompetencia');
            }

            //Cria os dados do prestador
            $prestador = $dom->createElement('Prestador');

            /* CPF CNPJ */
            $cpfCnpj = $dom->createElement('CpfCnpj');

            if ($nsTomado->infPrestador['tipo'] == '2') {
                $tag = 'Cnpj';
            } else {
                $tag = 'Cpf';
            }
            //Adiciona o Cpf/Cnpj na tag CpfCnpj
            $dom->addChild(
                $cpfCnpj,
                $tag,
                $nsTomado->infPrestador['cnpjcpf'],
                true,
                "Cpf / Cnpj",
                true
            );
            $dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Prestador');
            // //Adiciona a Inscrição Municipal na tag Prestador
            $dom->addChild(
                $prestador,
                'InscricaoMunicipal',
                $nsTomado->infPrestador['im'],
                true,
                "InscricaoMunicipal",
                true
            );

            //Adiciona a tag Prestador a consulta
            $dom->appChild($root, $prestador, 'Adicionando tag Prestador');

            //Cria os dados do tomador
            $tomador = $dom->createElement('Tomador');

            /* CPF CNPJ */
            $cpfCnpj = $dom->createElement('CpfCnpj');

            if ($nsTomado->infTomador['tipo'] == '2') {
                $tag = 'Cnpj';
            } else {
                $tag = 'Cpf';
            }
            //Adiciona o Cpf/Cnpj na tag CpfCnpj
            $dom->addChild(
                $cpfCnpj,
                $tag,
                $nsTomado->infTomador['cnpjcpf'],
                true,
                "Cpf / Cnpj",
                true
            );
            $dom->appChild($tomador, $cpfCnpj, 'Adicionando tag CpfCnpj ao Tomador');
            // //Adiciona a Inscrição Municipal na tag Tomador
            $dom->addChild(
                $tomador,
                'InscricaoMunicipal',
                $nsTomado->infTomador['im'],
                true,
                "InscricaoMunicipal",
                true
            );

            //Adiciona a tag Tomador a consulta
            $dom->appChild($root, $tomador, 'Adicionando tag Tomador');

            /** Intermediario **/
            if (!empty($nsTomado->infIntermediario['tipo'])) {
                $intermediario = $dom->createElement('Intermediario');
                $cpfCnpj = $dom->createElement('CpfCnpj');
                if ($nsTomado->infIntermediario['tipo'] == 2) {
                    $dom->addChild(
                        $cpfCnpj,
                        'Cnpj',
                        $nsTomado->infIntermediario['cnpjcpf'],
                        true,
                        'CNPJ Intermediario',
                        false
                    );
                } elseif ($nsTomado->infIntermediario['tipo'] == 1) {
                    $dom->addChild(
                        $cpfCnpj,
                        'Cpf',
                        $nsTomado->infIntermediario['cnpjcpf'],
                        true,
                        'CPF Intermediario',
                        false
                    );
                }
                $dom->appChild($intermediario, $cpfCnpj, 'Adicionando tag CpfCnpj em Intermediario');
                $dom->addChild(
                    $intermediario,
                    'InscricaoMunicipal',
                    $nsTomado->infIntermediario['im'],
                    false,
                    'IM Intermediario',
                    false
                );
                $dom->appChild($root, $intermediario, 'Adicionando tag Intermediario em infnsPrestado');
            }

            $dom->addChild(
                $root,
                'Pagina',
                $nsTomado->infPagina,
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
