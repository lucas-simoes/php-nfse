<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional;

use NFePHP\NFSe\Providers\Nacional\Interfaces\NacionalTransformerInterface;
use NFePHP\NFSe\Providers\Nacional\Models\Dps;
use NFePHP\NFSe\Providers\Nacional\Models\Emitente;
use NFePHP\NFSe\Providers\Nacional\Models\Servico;
use NFePHP\NFSe\Providers\Nacional\Models\Tomador;
use NFePHP\NFSe\Providers\Nacional\Models\Valores;

/**
 * Serializa Dps → array PHP pronto para json_encode() no formato ADN.
 *
 * Todos os blocos são mapeados conforme o schema DPS 1.00 documentado em
 * contracts/api-nacional.md. Campos com valor null são omitidos do output
 * (array_filter com ARRAY_FILTER_USE_BOTH não é suficiente para arrays
 * aninhados — usamos filtragem manual por bloco).
 */
class NacionalTransformer implements NacionalTransformerInterface
{
    /**
     * Converte DPS em array com estrutura infDPS conforme contracts/api-nacional.md.
     *
     * @return array<string, mixed>
     */
    public function transform(Dps $dps): array
    {
        $infDps = $this->filterNulls([
            'Id'      => $dps->id,
            'tpAmb'   => $dps->ambiente,
            'dhEmi'   => $dps->dataEmissao->format(\DateTimeInterface::ATOM),
            'verAplic' => $dps->versaoAplicacao,
            'dCompet' => $dps->competencia,
            'subst'   => $dps->substituicao !== null ? [
                'chNFSeSubst' => $dps->substituicao->chaveAcessoSubstituida,
                'cMotivo'     => $dps->substituicao->codigoMotivo,
            ] : null,
            'emit'    => $this->buildEmit($dps->emitente),
            'tomador' => $this->buildTomador($dps->tomador),
            'serv'    => $this->buildServ($dps->servico),
            'valores' => $this->buildValores($dps->valores),
        ]);

        return ['infDPS' => $infDps];
    }

    // -------------------------------------------------------------------------
    // Private builders
    // -------------------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    private function buildEmit(Emitente $emitente): array
    {
        return $this->filterNulls([
            'CNPJ'   => $emitente->cnpj,
            'IM'     => $emitente->inscricaoMunicipal,
            'CRT'    => $emitente->codigoRegimeTributario,
            'regTrib' => $this->filterNulls([
                'opSimpNac' => $emitente->regimeTributario->opcaoSimplesNacional,
                'CNAE'      => $emitente->regimeTributario->cnae,
                'cLocEmi'   => $emitente->regimeTributario->codigoLocalEmissao,
            ]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTomador(Tomador $tomador): array
    {
        $data = $this->filterNulls([
            'CNPJ'           => $tomador->cnpj,
            'CPF'            => $tomador->cpf,
            'NIF'            => $tomador->nifEstrangeiro,
            'IM'             => $tomador->inscricaoMunicipal,
            'enderTomador'   => $this->buildEnderTomador($tomador),
        ]);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEnderTomador(Tomador $tomador): array
    {
        return $this->filterNulls([
            'xLgr'   => $tomador->endereco->logradouro,
            'nro'    => $tomador->endereco->numero,
            'xCpl'   => $tomador->endereco->complemento,
            'xBairro' => $tomador->endereco->bairro,
            'cMun'   => $tomador->endereco->codigoMunicipio,
            'UF'     => $tomador->endereco->uf,
            'CEP'    => $tomador->endereco->cep,
            'xPais'  => $tomador->endereco->nomePais,
            'cPais'  => $tomador->endereco->codigoPais,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildServ(Servico $servico): array
    {
        $data = [
            'cServ' => $this->filterNulls([
                'cTribNac'  => $servico->codigoServico->codigoTributacaoNacional,
                'cTribMun'  => $servico->codigoServico->codigoTributacaoMunicipal,
                'CNAE'      => $servico->codigoServico->cnae,
                'xDescServ' => $servico->codigoServico->descricaoServico,
            ]),
            'loc' => $this->filterNulls([
                'cLocPrestacao'  => $servico->localPrestacao->codigoLocalPrestacao,
                'cPaisPrestacao' => $servico->localPrestacao->codigoPais,
            ]),
        ];

        if ($servico->complemento !== null) {
            $compl = $this->filterNulls([
                'xCompl' => $servico->complemento->textoComplemento,
                'cBenef' => $servico->complemento->codigoIncentivoBeneficio,
            ]);
            if ($compl !== []) {
                $data['compl'] = $compl;
            }
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildValores(Valores $valores): array
    {
        $trib = [
            'tribMun' => $this->filterNulls([
                'tribISSQN' => $valores->tributacao->tributacaoMunicipal->tributacaoIssqn,
                'cLocIncid' => $valores->tributacao->tributacaoMunicipal->codigoLocalIncidencia,
                'pAliq'     => $valores->tributacao->tributacaoMunicipal->aliquota,
                'tpRetBM'   => $valores->tributacao->tributacaoMunicipal->tipoRetencaoBM,
            ]),
            'totTrib' => $this->filterNulls([
                'pTotTrib'   => $valores->tributacao->totalTributos->percentualTotalTributos,
                'vTotTrib'   => $valores->tributacao->totalTributos->valorTotalTributos,
                'indTotTrib' => $valores->tributacao->totalTributos->indicadorTotalTributos,
            ]),
        ];

        if ($valores->tributacao->tributacaoFederal !== null) {
            $tribFed = $this->filterNulls([
                'pisCofins' => $valores->tributacao->tributacaoFederal->pisCofins,
                'irpj'      => $valores->tributacao->tributacaoFederal->irpj,
                'csll'      => $valores->tributacao->tributacaoFederal->csll,
            ]);
            if ($tribFed !== []) {
                $trib['tribFed'] = $tribFed;
            }
        }

        return [
            'vServPrest' => $this->filterNulls([
                'vReceb' => $valores->valorServicoPrestado->valorRecebido,
                'vDesc'  => $valores->valorServicoPrestado->valorDesconto,
            ]),
            'pTotMaior' => $this->filterNulls([
                'vLiq'       => $valores->totalMaior->valorLiquido,
                'vCarga'     => $valores->totalMaior->valorCargaTributaria,
                'pCargaTrib' => $valores->totalMaior->percentualCargaTributaria,
            ]),
            'trib' => $trib,
        ];
    }

    // -------------------------------------------------------------------------
    // Utility
    // -------------------------------------------------------------------------

    /**
     * Remove entradas com valor null de um array (nível único).
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function filterNulls(array $data): array
    {
        return array_filter($data, fn ($v) => $v !== null);
    }
}
