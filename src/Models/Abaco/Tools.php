<?php

namespace NFePHP\NFSe\Models\Abaco;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo Abaco
 * NOTA: Abaco extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abaco
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
    protected $schemeFolder = 'Abaco';

    /**
     * Consulta Lote
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Abaco\\Factories\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);
        $url = $this->url[$this->config->tpAmb]['ConsultarSituacaoLoteRps'];
        return $this->consultarLoteRpsCommon($fact, $protocolo, $url);
    }

    /**
     * Envia lote de RPS
     * @param string $lote
     * @param array $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Abaco\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);
        $url = $this->url[$this->config->tpAmb]['RecepcionarLoteRps'];
        return $this->recepcionarLoteRpsCommon($fact, $lote, $rpss, $url);
    }

    /**
     * Monta o corpo do request SOAP
     * @param $message
     * @return string
     */
    protected function makeRequest($message)
    {

        $request =
            "<e:{$this->method}.Execute>"
            . "<e:Nfsecabecmsg>"
            . "&lt;cabecalho versao=\"201001\"&gt;&lt;versaoDados&gt;V2010&lt;/versaoDados&gt;&lt;/cabecalho&gt;"
            . "</e:Nfsecabecmsg>"
            . "<e:Nfsedadosmsg>"
            . $message
            . "</e:Nfsedadosmsg>"
            . "</e:{$this->method}.Execute>";
        return $request;
    }
}
