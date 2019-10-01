<?php

namespace NFePHP\NFSe\Models\Infisc;

/**
 * Classe para a renderização dos RPS em XML
 * conforme o modelo ISSNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Infisc\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Infisc\Rps;
use NFePHP\Common\Certificate;

class RenderRPS
{

    /**
     * @var DOMImproved
     */
    protected static $dom;

    /**
     * @var Certificate
     */
    protected static $certificate;

    /**
     * @var int
     */
    protected static $algorithm;

    public static function toXml($data, $algorithm = OPENSSL_ALGO_SHA1)
    {
        self::$algorithm = $algorithm;
        $xml = '';
        if (is_object($data)) {
            return self::render($data);
        } elseif (is_array($data)) {
            foreach ($data as $rps) {
                $xml .= self::render($rps);
            }
        }
        return $xml;
    }

    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    private static function render(Rps $rps)
    {
        self::$dom = new Dom('1.0', 'utf-8');
        $root = self::$dom->createElement('NFS-e');
        $infRPS = self::$dom->createElement('infNFSe');
        $infRPS->setAttribute("versao", "1.1");
        $identificacaoRps = self::$dom->createElement('Id');
        self::$dom->addChild(
            $identificacaoRps,
            'cNFS-e',
            $rps->Id->cNFSe,
            true,
            "Numero Aleatório",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'mod',
            $rps->Id->mod,
            true,
            "Modelo do RPS",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'serie',
            $rps->Id->serie,
            true,
            "Série do RPS",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'nNFS-e',
            $rps->Id->nNFSe,
            true,
            "Número da nota",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'dEmi',
            $rps->Id->dEmi,
            true,
            "Data de emissão",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'hEmi',
            $rps->Id->hEmi,
            true,
            "Hora de emissão",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'tpNF',
            $rps->Id->tpNF,
            true,
            "Tipo de nota",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'refNF',
            $rps->Id->refNF,
            true,
            "Chave",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'tpEmis',
            $rps->Id->tpEmis,
            true,
            "Tipo de emissão",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'ambienteEmi',
            $rps->Id->ambienteEmi,
            true,
            "Ambiente",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'formaEmi',
            $rps->Id->formaEmi,
            true,
            "Forma de emissão",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'empreitadaGlobal',
            $rps->Id->empreitadaGlobal,
            true,
            "Empreitada Global",
            true
        );
        self::$dom->appChild($infRPS, $identificacaoRps, 'Adicionando tag IdentificacaoRPS');

        $prestador = self::$dom->createElement('prest');
        self::$dom->addChild(
            $prestador,
            'CNPJ',
            $rps->prest->CNPJ,
            true,
            "CNPJ",
            true
        );
        self::$dom->addChild(
            $prestador,
            'xNome',
            $rps->prest->xNome,
            true,
            'Razão Social',
            false
        );
        self::$dom->addChild(
            $prestador,
            'IM',
            $rps->prest->IM,
            true,
            'Inscrição Municipal',
            false
        );
        self::$dom->addChild(
            $prestador,
            'xEmail',
            $rps->prest->xEmail,
            false,
            'Email',
            false
        );
        self::$dom->addChild(
            $prestador,
            'xSite',
            $rps->prest->xSite,
            false,
            'Site',
            false
        );
        
        $endereco = self::$dom->createElement('end');
        self::$dom->addChild(
            $endereco,
            'xLgr',
            $rps->prest->end->xLgr,
            true,
            'Logradouro',
            false
        );
        self::$dom->addChild(
            $endereco,
            'nro',
            $rps->prest->end->nro,
            true,
            'Numero',
            false
        );
        self::$dom->addChild(
            $endereco,
            'xCpl',
            $rps->prest->end->xCpl,
            true,
            'Complemento',
            false
        );
        self::$dom->addChild(
            $endereco,
            'xBairro',
            $rps->prest->end->xBairro,
            true,
            'Bairro',
            false
        );
        self::$dom->addChild(
            $endereco,
            'cMun',
            $rps->prest->end->cMun,
            true,
            'Cidade',
            false
        );
        self::$dom->addChild(
            $endereco,
            'xMun',
            $rps->prest->end->xMun,
            true,
            'Cidade',
            false
        );
        self::$dom->addChild(
            $endereco,
            'UF',
            $rps->prest->end->UF,
            true,
            'Estado',
            false
        );
        self::$dom->addChild(
            $endereco,
            'CEP',
            $rps->prest->end->CEP,
            true,
            'Cep',
            false
        );
        self::$dom->addChild(
            $endereco,
            'cPais',
            $rps->prest->end->cPais,
            true,
            'País',
            false
        );
        self::$dom->addChild(
            $endereco,
            'xPais',
            $rps->prest->end->xPais,
            true,
            'País',
            false
        );

        self::$dom->appChild($prestador, $endereco, 'Adicionando tag Endereco do Prestador');
        //Fim endereço
        
        self::$dom->addChild(
            $prestador,
            'fone',
            $rps->prest->fone,
            false,
            'Telefone',
            false
        );
        self::$dom->addChild(
            $prestador,
            'fone2',
            $rps->prest->fone2,
            false,
            'Telefone Alternativo',
            false
        );
        self::$dom->addChild(
            $prestador,
            'IE',
            $rps->prest->IE,
            false,
            'Inscrição Estadual',
            false
        );
        self::$dom->addChild(
            $prestador,
            'regimeTrib',
            $rps->prest->regimeTrib,
            true,
            'Regime',
            false
        );
        self::$dom->appChild($infRPS, $prestador, 'Adicionando tag Prestador em infRPS');

        $tomador = self::$dom->createElement('TomS');
        if (!empty($rps->TomS->CNPJ)) {
            self::$dom->addChild(
                $tomador,
                'CNPJ',
                $rps->TomS->CNPJ,
                true,
                'Tomador CNPJ',
                false
            );
        } else {
            self::$dom->addChild(
                $tomador,
                'CPF',
                $rps->TomS->CPF,
                true,
                'Tomador CPF',
                false
            );
        }
        self::$dom->addChild(
            $tomador,
            'xNome',
            $rps->TomS->xNome,
            true,
            'Razao Social',
            false
        );

        $ender = self::$dom->createElement('ender');
        self::$dom->addChild(
            $ender,
            'xLgr',
            $rps->TomS->ender->xLgr,
            true,
            'Logradouro',
            false
        );
        self::$dom->addChild(
            $ender,
            'nro',
            $rps->TomS->ender->nro,
            true,
            'Numero',
            false
        );
        self::$dom->addChild(
            $ender,
            'xCpl',
            $rps->TomS->ender->xCpl,
            true,
            'Complemento',
            false
        );
        self::$dom->addChild(
            $ender,
            'xBairro',
            $rps->TomS->ender->xBairro,
            true,
            'Bairro',
            false
        );
        self::$dom->addChild(
            $ender,
            'cMun',
            $rps->TomS->ender->cMun,
            true,
            'Cidade',
            false
        );
        self::$dom->addChild(
            $ender,
            'xMun',
            $rps->TomS->ender->xMun,
            true,
            'Cidade',
            false
        );
        self::$dom->addChild(
            $ender,
            'UF',
            $rps->TomS->ender->UF,
            true,
            'Estado',
            false
        );
        self::$dom->addChild(
            $ender,
            'CEP',
            $rps->TomS->ender->CEP,
            true,
            'Cep',
            false
        );
        self::$dom->addChild(
            $ender,
            'cPais',
            $rps->TomS->ender->cPais,
            true,
            'País',
            false
        );
        self::$dom->addChild(
            $ender,
            'xPais',
            $rps->TomS->ender->xPais,
            true,
            'País',
            false
        );

        self::$dom->appChild($tomador, $ender, 'Adicionando tag Endereco do Prestador');
        //Fim endereço tomador
        self::$dom->appChild($infRPS, $tomador, 'Adicionando tag Tomador em infRPS');

        //Transportadora
        if (isset($rps->transportadora)) {
            $transportadora = self::$dom->createElement('transportadora');
            self::$dom->addChild(
                $transportadora,
                'xNomeTrans',
                $rps->transportadora->xNomeTrans,
                true,
                'Razao Social',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xCpfCnpjTrans',
                $rps->transportadora->xCpfCnpjTrans,
                false,
                'CPF ou CNPJ',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xInscEstTrans',
                $rps->transportadora->xInscEstTrans,
                false,
                'IE',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xPlacaTrans',
                $rps->transportadora->xPlacaTrans,
                false,
                'Placa',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xEndTrans',
                $rps->transportadora->xEndTrans,
                false,
                'Endereço',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'cMunTrans',
                $rps->transportadora->cMunTrans,
                false,
                'Código Cidade',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xMunTrans',
                $rps->transportadora->xMunTrans,
                false,
                'Cidade',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xUfTrans',
                $rps->transportadora->xUfTrans,
                false,
                'UF',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'cPaisTrans',
                $rps->transportadora->cPaisTrans,
                false,
                'País',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'xPaisTrans',
                $rps->transportadora->xPaisTrans,
                false,
                'País',
                false
            );
            self::$dom->addChild(
                $transportadora,
                'vTipoFreteTrans',
                $rps->transportadora->vTipoFreteTrans,
                false,
                'Tipo frete',
                false
            );
            self::$dom->appChild($infRPS, $transportadora, 'Adicionando tag Transportadora em infRPS');
        }

        //Detalhamento dos serviços
        $rps->totalvISS = 0;
        $rps->totalvBCISS = 0;
        $rps->totalvSTISS = 0;
        $rps->totalvBCSTISS = 0;
        foreach ($rps->det as $d) {
            $det = self::$dom->createElement('det');
            self::$dom->addChild(
                $det,
                'nItem',
                $d->nItem,
                true,
                'Número do Item',
                false
            );

            //Serviço da NFS-e
            $serv = self::$dom->createElement('serv');
            self::$dom->addChild(
                $serv,
                'cServ',
                $rps->serv[$d->nItem]->cServ,
                true,
                'Código Municipal do serviço',
                false
            );
            self::$dom->addChild(
                $serv,
                'cLCServ',
                $rps->serv[$d->nItem]->cLCServ,
                true,
                'Código do Serviço',
                false
            );
            self::$dom->addChild(
                $serv,
                'xServ',
                $rps->serv[$d->nItem]->xServ,
                true,
                'Discriminação do Serviço',
                false
            );
            self::$dom->addChild(
                $serv,
                'localTributacao',
                $rps->serv[$d->nItem]->localTributacao,
                true,
                'Local tributação IBGE',
                false
            );
            self::$dom->addChild(
                $serv,
                'localVerifResServ',
                $rps->serv[$d->nItem]->localVerifResServ,
                true,
                'Local verificação do serviço',
                false
            );
            self::$dom->addChild(
                $serv,
                'uTrib',
                $rps->serv[$d->nItem]->uTrib,
                true,
                'Unidade',
                false
            );
            self::$dom->addChild(
                $serv,
                'qTrib',
                $rps->serv[$d->nItem]->qTrib,
                true,
                'Quantidade',
                false
            );
            self::$dom->addChild(
                $serv,
                'vUnit',
                $rps->serv[$d->nItem]->vUnit,
                true,
                'Valor unitário',
                false
            );
            self::$dom->addChild(
                $serv,
                'vServ',
                $rps->serv[$d->nItem]->vServ,
                true,
                'Valor do Serviço',
                false
            );
            self::$dom->addChild(
                $serv,
                'vDesc',
                $rps->serv[$d->nItem]->vDesc,
                true,
                'Desconto',
                false
            );
            self::$dom->addChild(
                $serv,
                'vBCISS',
                $rps->serv[$d->nItem]->vBCISS,
                false,
                'BaseISSQN',
                false
            );
            $rps->totalvBCISS += $rps->serv[$d->nItem]->vBCISS;
            self::$dom->addChild(
                $serv,
                'pISS',
                $rps->serv[$d->nItem]->pISS,
                false,
                'ISS',
                false
            );
            self::$dom->addChild(
                $serv,
                'vISS',
                $rps->serv[$d->nItem]->vISS,
                false,
                'Valor iss',
                false
            );
            $rps->totalvISS += $rps->serv[$d->nItem]->vISS;
            self::$dom->addChild(
                $serv,
                'vBCINSS',
                $rps->serv[$d->nItem]->vBCINSS,
                false,
                'Base INSS',
                false
            );
            self::$dom->addChild(
                $serv,
                'pRetINSS',
                $rps->serv[$d->nItem]->pRetINSS,
                false,
                'Retenção INSS',
                false
            );
            self::$dom->addChild(
                $serv,
                'vRetINSS',
                $rps->serv[$d->nItem]->vRetINSS,
                false,
                'Retenção INSS',
                false
            );
            self::$dom->addChild(
                $serv,
                'vRed',
                $rps->serv[$d->nItem]->vRed,
                false,
                'Valor redução ISS',
                false
            );
            self::$dom->addChild(
                $serv,
                'vBCRetIR',
                $rps->serv[$d->nItem]->vBCRetIR,
                false,
                'Retenção IR',
                false
            );
            self::$dom->addChild(
                $serv,
                'pRetIR',
                $rps->serv[$d->nItem]->pRetIR,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'vRetIR',
                $rps->serv[$d->nItem]->vRetIR,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'vBCCOFINS',
                $rps->serv[$d->nItem]->vBCCOFINS,
                false,
                'Base Cofins',
                false
            );
            self::$dom->addChild(
                $serv,
                'pRetCOFINS',
                $rps->serv[$d->nItem]->pRetCOFINS,
                false,
                'Retenção Cofins',
                false
            );
            self::$dom->addChild(
                $serv,
                'vRetCOFINS',
                $rps->serv[$d->nItem]->pRetCOFINS,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'vBCCSLL',
                $rps->serv[$d->nItem]->vBCCSLL,
                false,
                'Base CSLL',
                false
            );
            self::$dom->addChild(
                $serv,
                'pRetCSLL',
                $rps->serv[$d->nItem]->pRetCSLL,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'vRetCSLL',
                $rps->serv[$d->nItem]->vRetCSLL,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'vBCPISPASEP',
                $rps->serv[$d->nItem]->vBCPISPASEP,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'pRetPISPASEP',
                $rps->serv[$d->nItem]->pRetPISPASEP,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'vRetPISPASEP',
                $rps->serv[$d->nItem]->vRetPISPASEP,
                false,
                '',
                false
            );
            self::$dom->addChild(
                $serv,
                'totalAproxTribServ',
                $rps->serv[$d->nItem]->totalAproxTribServ,
                false,
                '',
                false
            );

            self::$dom->appChild($det, $serv, 'Adicionando tag Endereco do Prestador');

            //ISSST
            if (isset($rps->ISSST[$d->nItem])) {
                $ISSST = self::$dom->createElement('ISSST');
                self::$dom->addChild(
                    $ISSST,
                    'vRedBCST',
                    $rps->ISSST[$d->nItem]->vRedBCST,
                    false,
                    'Valor da redução da base de cálculo do ISSQN retido',
                    false
                );
                self::$dom->addChild(
                    $ISSST,
                    'vBCST',
                    $rps->ISSST[$d->nItem]->vBCST,
                    true,
                    'Valor da base de cálculo do ISSQN retido',
                    false
                );
                $rps->totalvBCSTISS += $rps->ISSST[$d->nItem]->vBCST;
                self::$dom->addChild(
                    $ISSST,
                    'pISSST',
                    $rps->ISSST[$d->nItem]->pISSST,
                    true,
                    'Alíquota do ISSQN retido do item de serviço',
                    false
                );
                self::$dom->addChild(
                    $ISSST,
                    'vISSST',
                    $rps->ISSST[$d->nItem]->vISSST,
                    true,
                    'Valor do ISSQN retido do item de serviço',
                    false
                );
                $rps->totalvSTISS += $rps->ISSST[$d->nItem]->vISSST;

                self::$dom->appChild($det, $ISSST, 'Adicionando tag ISSQN retido em um item de serviço da NFS-e');
            }
            //Serviço da NFS-e
            self::$dom->appChild($infRPS, $det, 'Adicionando tag Transportadora em infRPS');
        }

        //Totais
        $total = self::$dom->createElement('total');
        self::$dom->addChild(
            $total,
            'vServ',
            $rps->total->vServ,
            true,
            'Valor Serviço',
            false
        );
        self::$dom->addChild(
            $total,
            'vRedBCCivil',
            $rps->total->vRedBCCivil,
            false,
            'Valor BC construção civil',
            false
        );
        self::$dom->addChild(
            $total,
            'vDesc',
            $rps->total->vDesc,
            false,
            'Valor Desconto',
            false
        );
        self::$dom->addChild(
            $total,
            'vtNF',
            $rps->total->vtNF,
            true,
            'Valor Nota',
            false
        );
        self::$dom->addChild(
            $total,
            'vtLiq',
            $rps->total->vtLiq,
            true,
            'Valor Total Liquido',
            false
        );
        //Serviço da NFS-e
        $ISS = self::$dom->createElement('ISS');
        self::$dom->addChild(
            $ISS,
            'vBCISS',
            number_format($rps->totalvBCISS, 2),
            false,
            'Valor total da base cálculo ISSQN',
            false
        );
        self::$dom->addChild(
            $ISS,
            'vISS',
            number_format($rps->totalvISS, 2),
            false,
            'Valor total ISS',
            false
        );
        self::$dom->addChild(
            $ISS,
            'vBCSTISS',
            number_format($rps->totalvBCSTISS, 2),
            false,
            'Valor total da base cálculo ISSQN ST',
            false
        );
        self::$dom->addChild(
            $ISS,
            'vSTISS',
            number_format($rps->totalvSTISS, 2),
            false,
            'Valor total ISS ST ',
            false
        );
        
        self::$dom->appChild($total, $ISS, 'Adicionando tag ISS');
        self::$dom->appChild($infRPS, $total, 'Adicionando tag Total em infRPS');
        
        //Faturas
        $faturas = self::$dom->createElement('faturas');
        foreach ($rps->fat as $fatura) {
            $fat = self::$dom->createElement('fat');
            self::$dom->addChild(
                $fat,
                'nItem',
                $fatura->nItem,
                true,
                'Número sequencial para ordenar faturas',
                false
            );
            self::$dom->addChild(
                $fat,
                'nFat',
                $fatura->nFat,
                true,
                'Número da fatura',
                false
            );
            self::$dom->addChild(
                $fat,
                'dVenc',
                $fatura->dVenc,
                false,
                'Data de vencimento da fatura',
                false
            );
            self::$dom->addChild(
                $fat,
                'vFat',
                $fatura->vFat,
                true,
                'Valor da fatura',
                false
            );
            self::$dom->appChild($faturas, $fat, 'Adicionando tag fat em faturas');
        }
        self::$dom->appChild($infRPS, $faturas, 'Adicionando tag fatura em infRPS');
        
        //Informações adicionais
        self::$dom->addChild(
            $infRPS,
            'infAdicLT',
            $rps->infAdicLT,
            true,
            'Local da tributação utilizando código do município conforme IBGE',
            false
        );
        foreach ($rps->infAdic as $inf) {
            self::$dom->addChild(
                $infRPS,
                'infAdic',
                $inf,
                true,
                'Informações adicionais',
                false
            );
        }


        self::$dom->appChild($root, $infRPS, 'Adicionando tag infRPS em RPS');
        self::$dom->appendChild($root);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', self::$dom->saveXML());
        return $xml;
    }
}
