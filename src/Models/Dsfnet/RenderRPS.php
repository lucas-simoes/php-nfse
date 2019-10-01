<?php

namespace NFePHP\NFSe\Models\Dsfnet;

/**
 * Classe para a renderização dos RPS em XML
 * conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\RenderRPS
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
        self::$dom = new Dom();
        $root = self::$dom->createElement('RPS');
        $idAttribute = self::$dom->createAttribute('Id');
        $idAttribute->value = 'rps:' . $rps->numeroRPS;
        $root->appendChild($idAttribute);
        self::$dom->addChild(
            $root,
            'Assinatura',
            self::signstr($rps),
            true,
            'Tag assinatura do RPS vazia',
            true
        );
        self::$dom->addChild(
            $root,
            'InscricaoMunicipalPrestador',
            $rps->inscricaoMunicipalPrestador,
            true,
            'Tag InscricaoMunicipalPrestador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'RazaoSocialPrestador',
            $rps->razaoSocialPrestador,
            true,
            'Tag RazaoSocialPrestador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'TipoRPS',
            $rps->tipoRPS,
            true,
            'Tag TipoRPS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'SerieRPS',
            $rps->serieRPS,
            true,
            'Tag SerieRPS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'NumeroRPS',
            $rps->numeroRPS,
            true,
            'Tag NumeroRPS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'DataEmissaoRPS',
            $rps->dataEmissaoRPS,
            true,
            'Tag DataEmissaoRPS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'SituacaoRPS',
            $rps->situacaoRPS,
            true,
            'Tag SituacaoRPS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'SerieRPSSubstituido',
            $rps->serieRPSSubstituido,
            true,
            'Tag SerieRPSSubstituido to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'NumeroRPSSubstituido',
            $rps->numeroRPSSubstituido,
            true,
            'Tag NumeroRPSSubstituido to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'NumeroNFSeSubstituida',
            $rps->numeroNFSeSubstituida,
            true,
            'Tag NumeroNFSeSubstituida to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'DataEmissaoNFSeSubstituida',
            $rps->dataEmissaoNFSeSubstituida,
            true,
            'Tag DataEmissaoNFSeSubstituida to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'SeriePrestacao',
            $rps->seriePrestacao,
            true,
            'Tag SeriePrestacao to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'InscricaoMunicipalTomador',
            $rps->inscricaoMunicipalTomador,
            true,
            'Tag InscricaoMunicipalTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'CPFCNPJTomador',
            $rps->cPFCNPJTomador,
            true,
            'Tag CPFCNPJTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'RazaoSocialTomador',
            $rps->razaoSocialTomador,
            true,
            'Tag RazaoSocialTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'TipoLogradouroTomador',
            $rps->tipoLogradouroTomador,
            true,
            'Tag TipoLogradouroTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'LogradouroTomador',
            $rps->logradouroTomador,
            true,
            'Tag LogradouroTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'NumeroEnderecoTomador',
            $rps->numeroEnderecoTomador,
            true,
            'Tag NumeroEnderecoTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ComplementoEnderecoTomador',
            $rps->complementoTomador,
            true,
            'Tag ComplementoEnderecoTomador to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'TipoBairroTomador',
            $rps->tipoBairroTomador,
            true,
            'Tag TipoBairroTomador to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'BairroTomador',
            $rps->bairroTomador,
            true,
            'Tag BairroTomador to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'CidadeTomador',
            $rps->cidadeTomador,
            true,
            'Tag CidadeTomador to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'CidadeTomadorDescricao',
            $rps->cidadeTomadorDescricao,
            true,
            'Tag CidadeTomadorDescricao to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'CEPTomador',
            $rps->cEPTomador,
            true,
            'Tag CEPTomador to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'EmailTomador',
            $rps->emailTomador,
            true,
            'Tag EmailTomador to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'CodigoAtividade',
            $rps->codigoAtividade,
            true,
            'Tag CodigoAtividade to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaAtividade',
            $rps->aliquotaAtividade,
            true,
            'Tag AliquotaAtividade to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'TipoRecolhimento',
            $rps->tipoRecolhimento,
            true,
            'Tag TipoRecolhimento to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'MunicipioPrestacao',
            $rps->municipioPrestacao,
            true,
            'Tag MunicipioPrestacao to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'MunicipioPrestacaoDescricao',
            $rps->municipioPrestacaoDescricao,
            true,
            'Tag MunicipioPrestacaoDescricao to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'Operacao',
            $rps->operacao,
            true,
            'Tag Operacao to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'Tributacao',
            $rps->tributacao,
            true,
            'Tag Tributacao to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorPIS',
            $rps->valorPIS,
            true,
            'Tag ValorPIS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorCOFINS',
            $rps->valorCOFINS,
            true,
            'Tag ValorCOFINS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorINSS',
            $rps->valorINSS,
            true,
            'Tag ValorINSS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorIR',
            $rps->valorIR,
            true,
            'Tag ValorIR to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'ValorCSLL',
            $rps->valorCSLL,
            true,
            'Tag ValorCSLL to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaPIS',
            $rps->aliquotaPIS,
            true,
            'Tag AliquotaPIS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaCOFINS',
            $rps->aliquotaCOFINS,
            true,
            'Tag AliquotaCOFINS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaINSS',
            $rps->aliquotaINSS,
            true,
            'Tag AliquotaINSS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaIR',
            $rps->aliquotaIR,
            true,
            'Tag AliquotaIR to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'AliquotaCSLL',
            $rps->aliquotaCSLL,
            true,
            'Tag AliquotaCSLL to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'DescricaoRPS',
            $rps->descricaoRPS,
            true,
            'Tag DescricaoRPS to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'DDDPrestador',
            $rps->dDDPrestador,
            true,
            'Tag DDDPrestador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'TelefonePrestador',
            $rps->telefonePrestador,
            true,
            'Tag TelefonePrestador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'DDDTomador',
            $rps->dDDTomador,
            true,
            'Tag DDDTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'TelefoneTomador',
            $rps->telefoneTomador,
            true,
            'Tag TelefoneTomador to RPS',
            false
        );
        self::$dom->addChild(
            $root,
            'MotCancelamento',
            $rps->motCancelamento,
            true,
            'Tag MotCancelamento to RPS',
            true
        );
        self::$dom->addChild(
            $root,
            'CPFCNPJIntermediario',
            $rps->cpfCnpjIntermediario,
            true,
            'Adding Tag CPFCNPJIntermediario to RPS',
            true
        );
        $deducoes = self::$dom->createElement('Deducoes');
        foreach ($rps->deducoes as $deduc) {
            $node = self::$dom->createElement('Deducao');
            foreach ($deduc as $tag => $value) {
                self::$dom->addChild(
                    $node,
                    $tag,
                    $value,
                    true,
                    "Adding Tag $tag to Deducoes",
                    false
                );
            }
            self::$dom->appChild($deducoes, $node, 'Append Deducao to Deducoes');
        }
        self::$dom->appChild($root, $deducoes, 'Append Deducoes to RPS');
        $itens = self::$dom->createElement('Itens');
        foreach ($rps->itens as $item) {
            $node = self::$dom->createElement('Item');
            foreach ($item as $tag => $value) {
                self::$dom->addChild(
                    $node,
                    $tag,
                    $value,
                    true,
                    "Adding Tag $tag to Item",
                    false
                );
            }
            self::$dom->appChild($itens, $node, 'Append Item to Itens');
        }
        self::$dom->appChild($root, $itens, 'Append Itens to RPS');
        self::$dom->appendChild($root);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', self::$dom->saveXML());
        return $xml;
    }

    /**
     * Cria a assinatura do RPS
     * @param Rps $rps
     * @return string
     */
    private static function signstr(Rps $rps)
    {
        $content = str_pad($rps->inscricaoMunicipalPrestador, 11, '0', STR_PAD_LEFT);
        $content .= str_pad($rps->serieRPS, 5, ' ', STR_PAD_RIGHT);
        $content .= str_pad($rps->numeroRPS, 12, '0', STR_PAD_LEFT);
        $dt = new \DateTime($rps->dataEmissaoRPS);
        $content .= $dt->format('Ymd');
        $content .= str_pad($rps->tributacao, 2, ' ', STR_PAD_RIGHT);
        $content .= $rps->situacaoRPS;
        $content .= ($rps->tipoRecolhimento == 'A') ? 'N' : 'S';
        $valores = self::calcValor($rps);
        $content .= str_pad(round($valores['valorFinal'] * 100, 0), 15, '0', STR_PAD_LEFT);
        $content .= str_pad(round($valores['valorDeducao'] * 100, 0), 15, '0', STR_PAD_LEFT);
        $content .= str_pad($rps->codigoAtividade, 10, '0', STR_PAD_LEFT);
        $content .= str_pad($rps->cPFCNPJTomador, 14, '0', STR_PAD_LEFT);
        $signature = base64_encode(self::$certificate->sign($content, self::$algorithm));
        return $signature;
    }

    private static function calcValor(Rps $rps)
    {
        $valorItens = 0;
        foreach ($rps->itens as $item) {
            $valorItens += $item['ValorTotal'];
        }
        $valorDeducao = 0;
        foreach ($rps->deducoes as $deducao) {
            $valorDeducao += $deducao['ValorDeduzir'];
        }
        $valor = ($valorItens - $valorDeducao);
        return ['valorFinal' => $valor, 'valorItens' => $valorItens, 'valorDeducao' => $valorDeducao];
    }
}
