<?php

namespace NFePHP\NFSe\Models\Prodam\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Envio de NFSe dos webservices da
 * Cidade de São Paulo conforme o modelo Prodam
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Factories\EnvioRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Prodam\RenderRPS;
use NFePHP\NFSe\Models\Prodam\Rps;

class EnvioRPS extends Factory
{
    private $dtIni = null;
    private $dtFim = null;
    private $qtdRPS = null;
    private $valorTotalServicos = null;
    private $valorTotalDeducoes = null;

    /**
     * Renderiza o pedido em seu respectivo xml e faz
     * a validação com o xsd
     * @param int $versao
     * @param int $remetenteTipoDoc
     * @param string $remetenteCNPJCPF
     * @param string $transacao
     * @param NFePHP\NFSe\Models\Prodam\Rps|array|null $data
     * @return string
     */
    public function render(
        $versao,
        $remetenteTipoDoc,
        $remetenteCNPJCPF,
        $transacao = 'true',
        $data = null
    ) {
        $xmlRPS = '';
        $method = "PedidoEnvioRPS";
        $content = $this->requestFirstPart($method);
        if (is_object($data)) {
            $xmlRPS .= $this->individual($data);
        } elseif (is_array($data)) {
            if (count($data) == 1) {
                $xmlRPS .= $this->individual($data[0]);
            } else {
                $method = "PedidoEnvioLoteRPS";
                $content = $this->requestFirstPart($method);
                $xmlRPS .= $this->lote($data);
            }
        } else {
            return '';
        }
        $content .= Header::render(
            $versao,
            $remetenteTipoDoc,
            $remetenteCNPJCPF,
            null,
            null,
            null,
            null,
            $this->dtIni,
            $this->dtFim,
            null,
            $this->qtdRPS,
            $this->valorTotalServicos,
            $this->valorTotalDeducoes
        );
        $content .= $xmlRPS . "</$method>";
        $content = $this->signer($content, $method, '', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Prodam', $method);
        return $body;
    }

    /**
     * Processa quando temos apenas um RPS
     * @param NFePHP\NFSe\Models\Prodam\Rps $data
     * @return string
     */
    private function individual(Rps $data)
    {
        return RenderRPS::toXml($data, $this->certificate, $this->algorithm);
    }

    /**
     * Processa vários Rps dentro de um array
     * @param array $data
     * @return string
     */
    private function lote(array $data)
    {
        $xmlRPS = '';
        $this->totalizeRps($data);
        foreach ($data as $rps) {
            $xmlRPS .= RenderRPS::toXml($data, $this->certificate, $this->algorithm);
        }
        return $xmlRPS;
    }

    /**
     * Totaliza os campos necessários para a montagem do cabeçalho
     * quando envio de Lote de RPS
     * @param array $rpss
     */
    private function totalizeRps(array $rpss)
    {
        $this->valorTotalServicos = 0;
        $this->valorTotalDeducoes = 0;
        foreach ($rpss as $rps) {
            $this->valorTotalServicos += $rps->valorServicosRPS;
            $this->valorTotalDeducoes += $rps->valorDeducoesRPS;
            $this->qtdRPS++;
            if (is_null($this->dtIni)) {
                $this->dtIni = $rps->dtEmiRPS;
            }
            if (is_null($this->dtFim)) {
                $this->dtFim = $rps->dtEmiRPS;
            }
            if ($rps->dtEmiRPS <= $this->dtIni) {
                $this->dtIni = $rps->dtEmiRPS;
            }
            if ($rps->dtEmiRPS >= $this->dtFim) {
                $this->dtFim = $rps->dtEmiRPS;
            }
        }
    }
}
