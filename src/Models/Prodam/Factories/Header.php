<?php

namespace NFePHP\NFSe\Models\Prodam\Factories;

/**
 * Classe para a construção dos cabaçalhos XML relativo aos serviços
 * dos webservices da Cidade de São Paulo conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Factories\Header
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
     * @param int $remetenteTipoDoc
     * @param string $remetenteCNPJCPF
     * @param string $transacao
     * @param string $cnpj
     * @param string $cpf
     * @param string $im
     * @param date $dtIni
     * @param date $dtFim
     * @param int $pagina
     * @param int $qtdRPS
     * @param float $valorTotalServicos
     * @param float $valorTotalDeducoes
     * @param string $numeroLote
     * @param string $prestadorIM
     * @return string
     */
    public static function render(
        $versao = null,
        $remetenteTipoDoc = null,
        $remetenteCNPJCPF = null,
        $transacao = null,
        $cnpj = null,
        $cpf = null,
        $im = null,
        $dtIni = null,
        $dtFim = null,
        $pagina = null,
        $qtdRPS = null,
        $valorTotalServicos = null,
        $valorTotalDeducoes = null,
        $numeroLote = null,
        $prestadorIM = null
    ) {
        $content = "<Cabecalho xmlns=\"\" Versao=\"$versao\">";
        $content .= "<CPFCNPJRemetente>";
        if ($remetenteTipoDoc == '2') {
            $content .= self::check('CNPJ', $remetenteCNPJCPF);
        } else {
            $content .= self::check('CPF', $remetenteCNPJCPF);
        }
        $content .= "</CPFCNPJRemetente>";
        $content .= self::check('transacao', $transacao);
        if ($cnpj != '') {
            $content .= "<CPFCNPJ><CNPJ>$cnpj</CNPJ></CPFCNPJ>";
        } elseif ($cpf != '') {
            $content .= "<CPFCNPJ><CPF>$cpf</CPF></CPFCNPJ>";
        }
        $content .= self::check('Inscricao', $im);
        $content .= self::check('dtInicio', $dtIni);
        $content .= self::check('dtFim', $dtFim);
        $content .= self::check('NumeroPagina', $pagina);
        if ($valorTotalServicos != 0) {
            $content .= self::check('QtdRPS', $qtdRPS);
            $content .= "<ValorTotalServicos>" . number_format($valorTotalServicos, 2, '.',
                    '') . "</ValorTotalServicos>";
            $content .= "<ValorTotalDeducoes>" . number_format($valorTotalDeducoes, 2, '.',
                    '') . "</ValorTotalDeducoes>";
        }
        $content .= self::check('NumeroLote', $numeroLote);
        $content .= self::check('InscricaoPrestador', $prestadorIM);
        $content .= "</Cabecalho>";
        return $content;
    }
}
