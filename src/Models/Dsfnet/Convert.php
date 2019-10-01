<?php

namespace NFePHP\NFSe\Models\Dsfnet;

/**
 * Classe a construção do xml
 * conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Convert
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class Convert
{
    /**
     * Leiaute do registro tipo RPS
     * Tipo 'R'
     * @var array
     */
    protected static $tpR = [
        ['Tipo', 1, 'C', ''],
        ['InscricaoMunicipalPrestador', 11, 'C', ''],
        ['RazaoSocialPrestador', 120, 'C', ''],
        ['TipoRPS', 20, 'C', ''],
        ['SerieRPS', 2, 'C', ''],
        ['NumeroRPS', 12, 'N', 0],
        ['DataEmissaoRPS', 19, 'D', ''],
        ['SituacaoRPS', 1, 'C', ''],
        ['SerieRPSSubstituido', 10, 'C', ''],
        ['NumeroRPSSubstituido', 10, 'N', 0],
        ['NumeroNFSeSubstituida', 10, 'N', 0],
        ['DataEmissaoNFSeSubstituida', 10, 'D', ''],
        ['SeriePrestacao', 2, 'N', 0],
        ['InscricaoMunicipalTomador', 11, 'C', ''],
        ['CPFCNPJTomador', 14, 'C', ''],
        ['RazaoSocialTomador', 120, 'C', ''],
        ['DocTomadorEstrangeiro', 20, 'C', ''],
        ['TipoLogradouroTomador', 10, 'C', ''],
        ['LogradouroTomador', 50, 'C', ''],
        ['NumeroEnderecoTomador', 9, 'C', ''],
        ['ComplementoTomador', 30, 'C', ''],
        ['TipoBairroTomador', 10, 'C', ''],
        ['BairroTomador', 50, 'C', ''],
        ['CidadeTomador', 10, 'C', ''],
        ['CidadeTomadorDescricao', 50, 'C', ''],
        ['CEPTomador', 8, 'C', ''],
        ['EmailTomador', 60, 'C', ''],
        ['CodigoAtividade', 9, 'C', ''],
        ['AliquotaAtividade', 6, 'N', 4],
        ['TipoRecolhimento', 1, 'C', ''],
        ['MunicipioPrestacao', 10, 'C', ''],
        ['MunicipioPrestacaoDescricao', 30, 'C', ''],
        ['Operacao', 1, 'C', ''],
        ['Tributacao', 1, 'C', ''],
        ['ValorPIS', 15, 'N', 2],
        ['ValorCOFINS', 15, 'N', 2],
        ['ValorINSS', 15, 'N', 2],
        ['ValorIR', 15, 'N', 2],
        ['ValorCSLL', 15, 'N', 2],
        ['AliquotaPIS', 6, 'N', 4],
        ['AliquotaCOFINS', 6, 'N', 4],
        ['AliquotaINSS', 6, 'N', 4],
        ['AliquotaIR', 6, 'N', 4],
        ['AliquotaCSLL', 6, 'N', 4],
        ['DescricaoRPS', 1500, 'C', ''],
        ['DDDPrestador', 3, 'C', ''],
        ['TelefonePrestador', 8, 'C', ''],
        ['DDDTomador', 3, 'C', ''],
        ['TelefoneTomador', 8, 'C', ''],
        ['MotCancelamento', 80, 'C', ''],
        ['CpfCnpjIntermediario', 14, 'C', '']
    ];

    /**
     * Leiaute do registro tipo Item de RPS
     * Tipo "I"
     * @var array
     */
    protected static $tpD = [
        ['Tipo', 1, 'C', ''],
        ['DeducaoPor', 20, 'C', ''],
        ['TipoDeducao', 255, 'C', ''],
        ['CPFCNPJReferencia', 14, 'C', ''],
        ['NumeroNFReferencia', 10, 'N', 0],
        ['ValorTotalReferencia', 15, 'N', 2],
        ['PercentualDeduzir', 15, 'N', 2],
        ['ValorDeduzir', 15, 'N', 2]
    ];

    /**
     * Leiaute do registro tipo Dedução de RPS
     * Tipo "D"
     * @var array
     */
    protected static $tpI = [
        ['Tipo', 1, 'C', ''],
        ['DiscriminacaoServico', 80, 'C', ''],
        ['Quantidade', 10, 'N', 0],
        ['ValorUnitario', 15, 'N', 2],
        ['ValorTotal', 15, 'N', 2],
        ['Tributavel', 1, 'C', '']
    ];

    /**
     * Formatos das Inscrições municipais
     * complementados com zeros a esquerda
     * @var array
     */
    protected static $tamIM = [
        ['2211001', 'Teresina – PI', 7],
        ['1501402', 'Belém - PA', 7],
        ['3509502', 'Campinas – SP', 9],
        ['5002704', 'Campo Grande – MS', 11],
        ['3170206', 'Uberlândia – MG', 8],
        ['3303500', 'Nova Iguaçu – RJ', 6],
        ['2111300', 'São Luis – MA', 11],
        ['3552205', 'Sorocaba -SP', 9]
    ];

    /**
     * Converte para Objetos RPS
     * @param string $txt lote de RPS em TXT formatado ou path para o arquivo
     * @return array
     */
    public static function toRps($txt = '')
    {
        return $txt;
    }
}
