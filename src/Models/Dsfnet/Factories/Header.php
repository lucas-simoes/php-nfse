<?php

namespace NFePHP\NFSe\Models\Dsfnet\Factories;

/**
 * Classe para a construção dos cabaçalhos XML relativo aos serviços
 * dos webservices conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Factories\Header
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Header as HeaderBase;

class Header extends HeaderBase
{
    /**
     * Renderiza as tag do cabecalho
     * @param int $versao
     * @param string $remetenteCNPJCPF
     * @param string $remetenteRazao
     * @param string $transacao
     * @param int $codcidade
     * @param int $codcid
     * @param string $token
     * @param string $prestadorIM
     * @param string $seriePrestacao
     * @param string $numeroLote
     * @param date $dtInicio
     * @param date $dtFim
     * @param int $qtdRPS
     * @param float $valorTotalServicos
     * @param float $valorTotalDeducoes
     * @param string $metodoEnvio
     * @param string $versaoComponente
     * @return string
     */
    public static function render(
        $versao = null,
        $remetenteCNPJCPF = null,
        $remetenteRazao = null,
        $transacao = null,
        $codcidade = null,
        $codcid = null,
        $token = null,
        $prestadorIM = null,
        $seriePrestacao = null,
        $numeroLote = null,
        $dtInicio = null,
        $dtFim = null,
        $notaInicial = null,
        $qtdRPS = null,
        $valorTotalServicos = null,
        $valorTotalDeducoes = null,
        $metodoEnvio = null,
        $versaoComponente = null
    ) {
        $content = "<Cabecalho>";
        if ($codcid != '') {
            $content .= self::check('CodCid', $codcid);
            $content .= self::check('IMPrestador', $prestadorIM);
            $content .= self::check('CPFCNPJRemetente', $remetenteCNPJCPF);
            $content .= self::check('SeriePrestacao', $seriePrestacao);
            $content .= self::check('Versao', $versao);
        } else {
            $content .= self::check('TokenEnvio', $token);
            $content .= self::check('CodCidade', $codcidade);
            $content .= self::check('CPFCNPJRemetente', $remetenteCNPJCPF);
            $content .= self::check('RazaoSocialRemetente', $remetenteRazao);
            $content .= self::check('InscricaoMunicipalPrestador', $prestadorIM);
            $content .= self::check('transacao', $transacao);
            $content .= self::check('dtInicio', $dtInicio);
            $content .= self::check('dtFim', $dtFim);
            $content .= self::check('NotaInicial', $notaInicial);
            if ($valorTotalServicos != 0) {
                $content .= self::check('QtdRPS', $qtdRPS);
                $content .= "<ValorTotalServicos>"
                    . number_format($valorTotalServicos, 2, '.', '')
                    . "</ValorTotalServicos>";
                $content .= "<ValorTotalDeducoes>"
                    . number_format($valorTotalDeducoes, 2, '.', '')
                    . "</ValorTotalDeducoes>";
            }
            $content .= self::check('Versao', $versao);
            $content .= self::check('NumeroLote', $numeroLote);
            $content .= self::check('MetodoEnvio', $metodoEnvio);
            $content .= self::check('VersaoComponente', $versaoComponente);
        }
        $content .= "</Cabecalho>";
        return $content;
    }
}
