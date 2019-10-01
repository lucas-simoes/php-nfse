<?php

namespace NFePHP\NFSe\Models\Infisc;

/**
 * Classe a montagem do RPS para a Cidade de São Paulo
 * conforme o modelo Infisc
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Infisc\Rps
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use InvalidArgumentException;
use NFePHP\Common\Strings;
use NFePHP\NFSe\Common\Rps as RpsBase;

class Rps extends RpsBase
{
    public $versao = '1.0';
    public $dhTrans = '';
    public $infNFSe = '';
    public $Id;
    public $prest;
    public $TomS;
    public $transportadora;
    public $det = [];
    public $serv = [];
    public $total;
    public $ISS;
    public $ISSST;
}
