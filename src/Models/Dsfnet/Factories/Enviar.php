<?php

namespace NFePHP\NFSe\Models\Dsfnet\Factories;

/**
 * Classe para a construção do XML relativo ao serviço de
 * Pedido de Envio de Lote de RPS dos webservices
 * conforme o modelo DSFNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Dsfnet\Factories\Enviar
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\NFSe\Models\Dsfnet\RenderRPS;

class Enviar extends Factory
{
    private $dtIni = '';
    private $dtFim = '';
    private $qtdRPS = 0;
    private $valorTotalServicos = 0;
    private $valorTotalDeducoes = 0;
    private $versaoComponente = '1.0.0';

    public function render(
        $versao,
        $remetenteCNPJCPF,
        $remetenteRazao,
        $transacao,
        $codcidade,
        $rpss,
        $numeroLote
    ) {
        $method = 'ReqEnvioLoteRPS';
        $this->totalizeRps($rpss);
        $content = $this->requestFirstPart($method);
        $content .= Header::render(
            $versao,
            $remetenteCNPJCPF,
            $remetenteRazao,
            $transacao,
            $codcidade,
            null, //$codcid
            null, //$token
            null, //$prestadorIM
            null, //$seriePrestacao
            null, //$numeroLote
            $this->dtIni,
            $this->dtFim,
            null, //$notaInicial
            $this->qtdRPS,
            $this->valorTotalServicos,
            $this->valorTotalDeducoes,
            'WS', //$metodoEnvio
            $this->versaoComponente
        );
        $content .= "<Lote Id=\"lote:$numeroLote\">";
        foreach ($rpss as $rps) {
            $content .= RenderRPS::toXml($rps, $this->certificate);
        }
        $content .= "</Lote>";
        $content .= "</ns1:$method>";
        $content = $this->signer($content, 'Lote', 'Id', [false, false, null, null]);
        $body = $this->clear($content);
        $this->validar($versao, $body, 'Dsfnet', $method, '');
        return $body;
    }

    /**
     * Totaliza os campos necessários para a montagem do cabeçalho
     * quando envio de Lote de RPS
     * @param array $rpss
     */
    private function totalizeRps($rpss)
    {
        foreach ($rpss as $rps) {
            $this->qtdRPS++;
            $data = \DateTime::createFromFormat('Y-m-d\TH:i:s', $rps->dataEmissaoRPS);
            if ($this->dtIni == '') {
                $this->dtIni = $data->format('Y-m-d');
            }
            if ($this->dtFim == '') {
                $this->dtFim = $data->format('Y-m-d');
            }
            if ($data->format('Y-m-d') <= $this->dtIni) {
                $this->dtIni = $data->format('Y-m-d');
            }
            if ($data->format('Y-m-d') >= $this->dtFim) {
                $this->dtFim = $data->format('Y-m-d');
            }
            foreach ($rps->itens as $item) {
                $this->valorTotalServicos += $item['ValorTotal'];
            }
            foreach ($rps->deducoes as $deducao) {
                $this->valorTotalDeducoes += $deducao['ValorDeduzir'];
            }
        }
    }
}
