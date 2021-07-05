<?php

namespace NFePHP\NFSe\Models\SIGISS;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo SIGISS
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\SIGISS\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use stdClass;
use NFePHP\NFSe\Common\DateTime;
use NFePHP\NFSe\Common\Tools as ToolsBase;

abstract class Tools extends ToolsBase
{
    /**
     * Constructor
     * @param stdClass $config
     */
    public function __construct(stdClass $config)
    {
        $this->config = $config;

        //Se o model já possuia  versão não tem necessidade de pegar da configuração
        if (empty($this->versao)) {
            $this->versao = $config->versao;
        }

        $this->remetenteCNPJCPF = $config->cpf;
        $this->remetenteRazao = $config->razaosocial;
        $this->remetenteIM = $config->im;
        $this->remetenteTipoDoc = 1;
        if ($config->cnpj != '') {
            $this->remetenteCNPJCPF = $config->cnpj;
            $this->remetenteTipoDoc = 2;
        }
        
        $this->timezone = DateTime::tzdBR($config->siglaUF);


        if (empty($this->versao)) {
            throw new \LogicException('Informe a versão do modelo.');
        }

    }
}
