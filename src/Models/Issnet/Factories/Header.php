<?php

namespace NFePHP\NFSe\Models\Issnet\Factories;

/**
 * Classe para a construção dos cabaçalhos XML relativo aos serviços
 * dos webservices do modelo Issnet
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Issnet\Factories\Header
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
     * @param $remetenteTipoDoc
     * @param $remetenteCNPJCPF
     * @param $inscricaoMunicipal
     *
     * @param $dtInicio
     * @param $dtFim
     * @param $numeroLote
     * @param $cnpjTomador
     * @param $cpfTomador
     * @param $inscricaoMunicipalTomador
     *
     * @return string
     */
    public static function render(
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $inscricaoMunicipal
    ) {
        $content = "<Prestador>";
        $content .= "<tc:CpfCnpj>";
        if ($remetenteTipoDoc == '2') {
            $content .= self::check('tc:Cnpj', $remetenteCNPJCPF);
        } else {
            $content .= self::check('tc:Cpf', $remetenteCNPJCPF);
        }
        $content .= "</tc:CpfCnpj>";
        $content .= self::check('tc:InscricaoMunicipal', $inscricaoMunicipal);
        $content .= "</Prestador>";
        return $content;
    }
}
