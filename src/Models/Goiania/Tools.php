<?php

namespace NFePHP\NFSe\Models\Goiania;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo Goiania 2.0
 * NOTA: Goiania extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Goiania\Tools
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Abrasf\Tools as ToolsAbrasft;

class Tools extends ToolsAbrasft
{
    protected $xmlns = '';
    protected $schemeFolder = 'Goiania';

    /**
     * Consulta Lote
     * @param string $numeroLote
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Goiania\\Factories\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);
        return $this->consultarLoteRpsCommon($fact, $protocolo);
    }

    /**
     *
     * @param string $numeroLote
     * @param array $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Goiania\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);
        return $this->recepcionarLoteRpsCommon($fact, $lote, $rpss);
    }

    public function gerarNfse($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\Goiania\\Factories\\v{$this->versao}\\GerarNfse";
        $fact = new $class($this->certificate);
        return $this->gerarNfseCommon($fact, $rps);

    }

    protected function makeRequest($message)
    {
        $this->params = [
            "Content-Type: text/xml;charset=utf-8;",
        ];
        $request =
            "<ws:{$this->method}>"
            . "<ws:ArquivoXML>"
            . "<![CDATA["
            . $message
            . "]]>"
            . "</ws:ArquivoXML>"
            . "</ws:{$this->method}>";

        // $request = '';
        return $request;
    }
}
