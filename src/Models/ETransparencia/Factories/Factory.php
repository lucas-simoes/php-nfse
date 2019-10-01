<?php

namespace NFePHP\NFSe\Models\ETransparencia\Factories;

/**
 * Classe base para a construção dos XMLs relativos ao serviços
 * dos webservices do modelo ETransparencia
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\ETransparencia\Factories\Factory
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
    protected function requestFirstPart($method, $xsd)
    {
        return "";
    }
}
