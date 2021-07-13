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

use NFePHP\NFSe\Models\Abrasf\NfseServicoTomado;
use NFePHP\NFSe\Models\Abrasf\NfseServicoPrestado;
use NFePHP\NFSe\Models\Abrasf\Tools as ToolsAbrasft;

class Tools extends ToolsAbrasft
{
    protected $xmlns = 'http://www.betha.com.br/e-nota-contribuinte-ws';
    protected $schemeFolder = 'Betha';
    
    /**
     * @param $numero
     * @param $serie
     * @param $tipo
     * @param string $url
     * @return string
     */
    public function consultarNfsePorRps($numero, $serie, $tipo)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\ConsultarNfsePorRps";
        $fact = new $class($this->certificate);
        return $this->consultarNfsePorRpsCommon($fact, $numero, $serie, $tipo);
    }

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
     * Recepciona lote de forma sincrona
     * @param $lote
     * @param $rpss
     * @return string
     */
    public function recepcionarLoteRpsSincrono($lote, $rpss)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\RecepcionarLoteRps";
        $fact = new $class($this->certificate);

        return $this->recepcionarLoteRpsSincronoCommon($fact, $lote, $rpss);
    }

    /**
     * @param $rps
     * @return string
     */
    public function gerarNfse($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\GerarNfse";
        $fact = new $class($this->certificate);
        return $this->gerarNfseCommon($fact, $rps);
    }

    /**
     * @param $numeroNfseInicial
     * @param $numeroNfseFinal
     * @param $pagina
     * @return string
     */
    public function consultarNfsePorFaixa($numeroNfseInicial, $numeroNfseFinal, $pagina)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\ConsultarNfsePorFaixa";
        $fact = new $class($this->certificate);
        return $this->consultarNfsePorFaixaCommon($fact, $numeroNfseInicial, $numeroNfseFinal, $pagina);
    }

    /**
     * @param NfseServicoPrestado $nsPrestado   
     * @param string $url
     * @return string
     */
    public function consultarNfseServicoPrestado(NfseServicoPrestado $nsPrestado)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\ConsultarNfseServicoPrestado";
        $fact = new $class($this->certificate);
        return $this->consultarNfseServicoPrestadoCommon($fact, $nsPrestado);
    }

    /**
     * @param NfseServicoTomado $nsTomado   
     * @param string $url
     * @return string
     */
    public function consultarNfseServicoTomado(NfseServicoTomado $nsTomado)
    {
        $class = "NFePHP\\NFSe\\Models\\Betha\\Factories\\v{$this->versao}\\ConsultarNfseServicoTomado";
        $fact = new $class($this->certificate);
        return $this->consultarNfseServicoTomadoCommon($fact, $nsTomado);
    }
}
