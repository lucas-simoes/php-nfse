<?php

namespace NFePHP\NFSe\Models\Prodam;

/**
 * Classe para a renderização dos RPS em XML para a Cidade de São Paulo
 * conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;

class RenderRPS
{
    protected static $dom;
    protected static $certificate;
    protected static $algorithm;

    public static function toXml($data, Certificate $certificate, $algorithm = OPENSSL_ALGO_SHA1)
    {
        self::$certificate = $certificate;
        self::$algorithm = $algorithm;
        $xml = '';
        if (is_object($data)) {
            return self::render($data);
        } elseif (is_array($data)) {
            foreach ($data as $rps) {
                $xml .= self::render($rps);
            }
        }
        return $xml;
    }

    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    private static function render(Rps $rps)
    {
        self::$dom = new Dom('1.0', 'utf-8');
        $root = self::$dom->createElement('RPS');
        $xmlnsAttribute = self::$dom->createAttribute('xmlns');
        $xmlnsAttribute->value = '';
        $root->appendChild($xmlnsAttribute);
        //tag Assinatura
        self::$dom->addChild(
            $root,
            'Assinatura',
            self::signstr($rps),
            true,
            'Tag assinatura do RPS vazia',
            true
        );
        //tag ChaveRPS
        $chaveRps = self::$dom->createElement('ChaveRPS');
        self::$dom->addChild(
            $chaveRps,
            'InscricaoPrestador',
            $rps->prestadorIM,
            true,
            "IM do prestador",
            true
        );
        self::$dom->addChild(
            $chaveRps,
            'SerieRPS',
            $rps->serieRPS,
            true,
            'Serie do RPS',
            false
        );
        self::$dom->addChild(
            $chaveRps,
            'NumeroRPS',
            $rps->numeroRPS,
            true,
            "Numero do RPS",
            true
        );
        self::$dom->appChild($root, $chaveRps, 'Adicionando tag ChaveRPS');
        //outras tags
        self::$dom->addChild(
            $root,
            'TipoRPS',
            $rps->tipoRPS,
            true,
            'Tipo de RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'DataEmissao',
            $rps->dtEmiRPS,
            true,
            'Data de emissão',
            false
        );
        self::$dom->addChild(
            $root,
            'StatusRPS',
            $rps->statusRPS,
            true,
            'Status do RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'TributacaoRPS',
            $rps->tributacaoRPS,
            true,
            'Tributação do RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorServicos',
            $rps->valorServicosRPS,
            true,
            'Valor dos serviços',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorDeducoes',
            $rps->valorDeducoesRPS,
            true,
            'Valor das Deduções',
            true
        );
        self::$dom->addChild(
            $root,
            'ValorPis',
            $rps->valorPISRPS,
            false,
            'Valor do PIS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorCOFINS',
            $rps->valorCOFINSRPS,
            false,
            'Valor do COFINS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorINSS',
            $rps->valorINSSRPS,
            false,
            'Valor do INSS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorIR',
            $rps->valorIRRPS,
            false,
            'Valor do IR',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorCSLL',
            $rps->valorCSLLRPS,
            false,
            'Valor do CSLL',
            false
        );
        self::$dom->addChild(
            $root,
            'CodigoServico',
            $rps->codigoServicoRPS,
            true,
            'Código do serviço',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaServicos',
            $rps->aliquotaServicosRPS,
            true,
            'Aliquota do serviço',
            false
        );
        $issRet = 'false';
        if ($rps->issRetidoRPS) {
            $issRet = 'true';
        }
        self::$dom->addChild(
            $root,
            'ISSRetido',
            $issRet,
            true,
            'ISS Retido',
            false
        );
        //tag CPFCNPJTomador
        if ($rps->tomadorTipoDoc != '3') {
            $tomador = self::$dom->createElement('CPFCNPJTomador');
            if ($rps->tomadorTipoDoc == '2') {
                self::$dom->addChild(
                    $tomador,
                    'CNPJ',
                    $rps->tomadorCNPJCPF,
                    true,
                    "CNPJ do tomador",
                    false
                );
            } elseif ($rps->tomadorTipoDoc == '1') {
                self::$dom->addChild(
                    $tomador,
                    'CPF',
                    $rps->tomadorCNPJCPF,
                    true,
                    "CPF do tomador",
                    false
                );
            }
            self::$dom->appChild($root, $tomador, 'Adicionando tag CPFCNPJTomador');
        }
        //outras tags
        self::$dom->addChild(
            $root,
            'RazaoSocialTomador',
            $rps->tomadorRazao,
            true,
            'Razão Social do tomador',
            false
        );
        //tag EnderecoTomador
        $endtomador = self::$dom->createElement('EnderecoTomador');
        self::$dom->addChild(
            $endtomador,
            'TipoLogradouro',
            $rps->tomadorTipoLogradouro,
            true,
            'Tipo de logradouro do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'Logradouro',
            $rps->tomadorLogradouro,
            true,
            'Logradouro do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'NumeroEndereco',
            $rps->tomadorNumeroEndereco,
            true,
            'Numero do Logradouro do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'ComplementoEndereco',
            $rps->tomadorComplementoEndereco,
            true,
            'Complemento endereço do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'Bairro',
            $rps->tomadorBairro,
            true,
            'Bairro endereço do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'Cidade',
            $rps->tomadorCodCidade,
            true,
            'Cidade endereço do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'UF',
            $rps->tomadorSiglaUF,
            true,
            'UF endereço do tomador',
            false
        );
        self::$dom->addChild(
            $endtomador,
            'CEP',
            $rps->tomadorCEP,
            true,
            'CEP endereço do tomador',
            false
        );
        self::$dom->appChild($root, $endtomador, 'Adicionando tag EnderecoTomador');
        //outras tags
        self::$dom->addChild(
            $root,
            'EmailTomador',
            $rps->tomadorEmail,
            false,
            'Email do tomador',
            false
        );
        //tag intermediario
        //se existir incluir dados do intermediário
        if ($rps->intermediarioCNPJCPF) {
            $intermediario = self::$dom->createElement('CPFCNPJIntermediario');
            self::$dom->addChild(
                $intermediario,
                'CNPJ',
                $rps->intermediarioCNPJCPF,
                true,
                "CNPJ do intermediario",
                false
            );
            self::$dom->appChild($root, $intermediario, 'Adicionando tag CPFCNPJIntermediario');
            self::$dom->addChild(
                $root,
                'InscricaoMunicipalIntermediario',
                $rps->intermediarioIM,
                false,
                'IM do intermediario',
                false
            );
            self::$dom->addChild(
                $root,
                'EmailIntermediario',
                $rps->intermediarioEmail,
                false,
                'email do intermediario',
                false
            );
        }
        self::$dom->addChild(
            $root,
            'Discriminacao',
            $rps->discriminacaoRPS,
            true,
            'Discriminação do serviço',
            false
        );
	 self::$dom->addChild(
            $root,
            'ValorCargaTributaria',
            $rps->valorCargaTributariaRPS,
            true,
            'Valor da carga tributária total em R$.',
            false
        );
        self::$dom->addChild(
            $root,
            'PercentualCargaTributaria',
            $rps->percentualCargaTributariaRPS,
            true,
            'Valor percentual da carga tributária',
            false
        );
        self::$dom->addChild(
            $root,
            'FonteCargaTributaria',
            $rps->fonteCargaTributariaRPS,
            true,
            'Fonte de informação da carga tributária ',
            false
        );
        //finaliza
        self::$dom->appendChild($root);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', self::$dom->saveXML());
        return $xml;
    }

    /**
     * Cria o valor da assinatura do RPS
     * @param Rps $rps
     * @return string
     */
    private static function signstr(Rps $rps)
    {
        $content = str_pad($rps->prestadorIM, 8, '0', STR_PAD_LEFT);
        $content .= str_pad($rps->serieRPS, 5, ' ', STR_PAD_RIGHT);
        $content .= str_pad($rps->numeroRPS, 12, '0', STR_PAD_LEFT);
        $content .= str_replace("-", "", $rps->dtEmiRPS);
        $content .= $rps->tributacaoRPS;
        $content .= $rps->statusRPS;
        $content .= ($rps->issRetidoRPS) ? 'S' : 'N';
        $content .= str_pad(
            str_replace(['.', ','], '', number_format($rps->valorServicosRPS, 2)),
            15,
            '0',
            STR_PAD_LEFT
        );
        $content .= str_pad(
            str_replace(['.', ','], '', number_format($rps->valorDeducoesRPS, 2)),
            15,
            '0',
            STR_PAD_LEFT
        );
        $content .= str_pad($rps->codigoServicoRPS, 5, '0', STR_PAD_LEFT);
        $content .= $rps->tomadorTipoDoc;
        $content .= str_pad($rps->tomadorCNPJCPF, 14, '0', STR_PAD_LEFT);
        if ($rps->intermediarioTipoDoc != '3' && $rps->intermediarioCNPJCPF != '') {
            $content .= $rps->intermediarioTipoDoc;
            $content .= str_pad($rps->intermediarioCNPJCPF, 14, '0', STR_PAD_LEFT);
            $content .= $rps->intermediarioISSRetido;
        }
        //$contentBytes = self::getBytes($content);
        $signature = base64_encode(self::$certificate->sign($content, self::$algorithm));
        return $signature;
    }
}
