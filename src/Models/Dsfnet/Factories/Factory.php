<?php

namespace NFePHP\NFSe\Models\Dsfnet\Factories;

/**
 * Classe base para a construção dos XMLs relativos ao serviços
 * dos webservices conforme o modelo Dsfnet
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Factories\Factory
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Common\Factory as FactoryBase;

class Factory extends FactoryBase
{
    protected function requestFirstPart($method)
    {
        return "<ns1:$method "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/$method.xsd\""
            . ">";
    }
}
