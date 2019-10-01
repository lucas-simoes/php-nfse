<?php

namespace NFePHP\NFSe\Models\Betha;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo BETHA 2.02
 * NOTA: BETHA extende ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Betha\Tools
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
    protected $xmlns = 'http://www.betha.com.br/e-nota-contribuinte-ws';
    protected $schemeFolder = 'Betha';

    /**
     * Consulta Lote
     * @param string $numeroLote
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\ConsultarLoteRps";
        $fact = new $class($this->certificate);
        return $this->consultarLoteRpsCommon($fact, $protocolo);
    }

    /**
     * Recepciona lote
     * @param string $numeroLote
     * @param array $rpss
     * @return string
     */
    public function recepcionarLoteRps($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);
        return $this->recepcionarLoteRpsCommon($fact, $lote, $rpss);
    }
}
