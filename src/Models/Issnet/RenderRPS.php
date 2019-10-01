<?php

namespace NFePHP\NFSe\Models\Issnet;

/**
 * Classe para a renderização dos RPS em XML
 * conforme o modelo ISSNET
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Issnet\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;

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
    /**
     * @var \DateTimeZone
     */
    protected static $timezone;

    public static function toXml($data, \DateTimeZone $timezone, $algorithm = OPENSSL_ALGO_SHA1)
    {
        //self::$certificate = $certificate;
        self::$algorithm = $algorithm;
        self::$timezone = $timezone;
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
        $root = self::$dom->createElement('tc:Rps');
        $infRPS = self::$dom->createElement('tc:InfRps');

        $identificacaoRps = self::$dom->createElement('tc:IdentificacaoRps');
        self::$dom->addChild(
            $identificacaoRps,
            'tc:Numero',
            $rps->infNumero,
            true,
            "Numero do RPS",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'tc:Serie',
            $rps->infSerie,
            true,
            "Serie do RPS",
            true
        );
        self::$dom->addChild(
            $identificacaoRps,
            'tc:Tipo',
            $rps->infTipo,
            true,
            "Tipo do RPS",
            true
        );
        self::$dom->appChild($infRPS, $identificacaoRps, 'Adicionando tag IdentificacaoRPS');
        $rps->infDataEmissao->setTimezone(self::$timezone);
        self::$dom->addChild(
            $infRPS,
            'tc:DataEmissao',
            $rps->infDataEmissao->format('Y-m-d\TH:i:s'),
            true,
            'Data de Emissão do RPS',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'tc:NaturezaOperacao',
            $rps->infNaturezaOperacao,
            true,
            'Natureza da operação',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'tc:OptanteSimplesNacional',
            $rps->infOptanteSimplesNacional,
            true,
            'OptanteSimplesNacional',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'tc:IncentivadorCultural',
            $rps->infIncentivadorCultural,
            true,
            'IncentivadorCultural',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'tc:Status',
            $rps->infStatus,
            true,
            'Status',
            false
        );

        if (!empty($rps->infRpsSubstituido['numero'])) {
            $rpssubs = self::$dom->createElement('tc:RpsSubstituido');
            self::$dom->addChild(
                $rpssubs,
                'tc:Numero',
                $rps->infRpsSubstituido['numero'],
                true,
                'Numero',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'tc:Serie',
                $rps->infRpsSubstituido['serie'],
                true,
                'Serie',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'tc:Tipo',
                $rps->infRpsSubstituido['tipo'],
                true,
                'tipo',
                false
            );
            self::$dom->appChild($infRPS, $rpssubs, 'Adicionando tag RpsSubstituido em infRps');
        }

        self::$dom->addChild(
            $infRPS,
            'tc:RegimeEspecialTributacao',
            $rps->infRegimeEspecialTributacao,
            true,
            'RegimeEspecialTributacao',
            false
        );
        $servico = self::$dom->createElement('tc:Servico');
        $valores = self::$dom->createElement('tc:Valores');
        self::$dom->addChild(
            $valores,
            'tc:ValorServicos',
            $rps->infValorServicos,
            true,
            'ValorServicos',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorDeducoes',
            $rps->infValorDeducoes,
            false,
            'ValorDeducoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorPis',
            $rps->infValorPis,
            false,
            'ValorPis',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorCofins',
            $rps->infValorCofins,
            false,
            'ValorCofins',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorInss',
            $rps->infValorInss,
            false,
            'ValorInss',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorIr',
            $rps->infValorIr,
            false,
            'ValorIr',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorCsll',
            $rps->infValorCsll,
            false,
            'ValorCsll',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:IssRetido',
            $rps->infIssRetido,
            true,
            'IssRetido',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorIss',
            $rps->infValorIss,
            false,
            'ValorIss',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorIssRetido',
            $rps->infValorIssRetido,
            false,
            'ValorIssRetido',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:OutrasRetencoes',
            $rps->infOutrasRetencoes,
            false,
            'OutrasRetencoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:BaseCalculo',
            $rps->infBaseCalculo,
            false,
            'BaseCalculo',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:Aliquota',
            number_format($rps->infAliquota, 2, '.', ''),
            false,
            'Aliquota',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:ValorLiquidoNfse',
            $rps->infValorLiquidoNfse,
            false,
            'ValorLiquidoNfse',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:DescontoIncondicionado',
            $rps->infDescontoIncondicionado,
            false,
            'DescontoIncondicionado',
            false
        );
        self::$dom->addChild(
            $valores,
            'tc:DescontoCondicionado',
            $rps->infDescontoCondicionado,
            false,
            'DescontoCondicionado',
            false
        );
        self::$dom->appChild($servico, $valores, 'Adicionando tag Valores em Servico');

        self::$dom->addChild(
            $servico,
            'tc:ItemListaServico',
            $rps->infItemListaServico,
            true,
            'ItemListaServico',
            false
        );
        self::$dom->addChild(
            $servico,
            'tc:CodigoCnae',
            $rps->infCodigoCnae,
            true,
            'CodigoCnae',
            false
        );
        self::$dom->addChild(
            $servico,
            'tc:CodigoTributacaoMunicipio',
            $rps->infCodigoTributacaoMunicipio,
            true,
            'CodigoTributacaoMunicipio',
            false
        );
        self::$dom->addChild(
            $servico,
            'tc:Discriminacao',
            $rps->infDiscriminacao,
            true,
            'Discriminacao',
            false
        );
        self::$dom->addChild(
            $servico,
            'tc:MunicipioPrestacaoServico',
            $rps->infMunicipioPrestacaoServico,
            true,
            'MunicipioPrestacaoServico',
            false
        );
        self::$dom->appChild($infRPS, $servico, 'Adicionando tag Servico');

        $prestador = self::$dom->createElement('tc:Prestador');
        $cpfCnpj = self::$dom->createElement('tc:CpfCnpj');
        if ($rps->infPrestador['tipo'] == 2) {
            self::$dom->addChild(
                $cpfCnpj,
                'tc:Cnpj',
                $rps->infPrestador['cnpjcpf'],
                true,
                'Prestador CNPJ',
                false
            );
        } else {
            self::$dom->addChild(
                $cpfCnpj,
                'tc:Cpf',
                $rps->infPrestador['cnpjcpf'],
                true,
                'Prestador CPF',
                false
            );
        }
        self::$dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj em Prestador');
        self::$dom->addChild(
            $prestador,
            'tc:InscricaoMunicipal',
            $rps->infPrestador['im'],
            true,
            'InscricaoMunicipal',
            false
        );
        self::$dom->appChild($infRPS, $prestador, 'Adicionando tag Prestador em infRPS');

        $tomador = self::$dom->createElement('tc:Tomador');
        $identificacaoTomador = self::$dom->createElement('tc:IdentificacaoTomador');
        $cpfCnpjTomador = self::$dom->createElement('tc:CpfCnpj');
        if ($rps->infTomador['tipo'] == 2) {
            self::$dom->addChild(
                $cpfCnpjTomador,
                'tc:Cnpj',
                $rps->infTomador['cnpjcpf'],
                true,
                'Tomador CNPJ',
                false
            );
        } else {
            self::$dom->addChild(
                $cpfCnpjTomador,
                'tc:Cpf',
                $rps->infTomador['cnpjcpf'],
                true,
                'Tomador CPF',
                false
            );
        }
        self::$dom->appChild($identificacaoTomador, $cpfCnpjTomador, 'Adicionando tag CpfCnpj em IdentificacaTomador');
        self::$dom->addChild(
            $identificacaoTomador,
            'tc:InscricaoMunicipal',
            $rps->infTomador['im'],
            true,
            'InscricaoMunicipal',
            false
        );
        self::$dom->appChild($tomador, $identificacaoTomador, 'Adicionando tag IdentificacaoTomador em Tomador');
        self::$dom->addChild(
            $tomador,
            'tc:RazaoSocial',
            $rps->infTomador['razao'],
            true,
            'RazaoSocial',
            false
        );
        $endereco = self::$dom->createElement('tc:Endereco');
        self::$dom->addChild(
            $endereco,
            'tc:Endereco',
            $rps->infTomadorEndereco['end'],
            true,
            'Endereco',
            false
        );
        self::$dom->addChild(
            $endereco,
            'tc:Numero',
            $rps->infTomadorEndereco['numero'],
            true,
            'Numero',
            false
        );
        self::$dom->addChild(
            $endereco,
            'tc:Complemento',
            $rps->infTomadorEndereco['complemento'],
            true,
            'Complemento',
            false
        );
        self::$dom->addChild(
            $endereco,
            'tc:Bairro',
            $rps->infTomadorEndereco['bairro'],
            true,
            'Bairro',
            false
        );
        self::$dom->addChild(
            $endereco,
            'tc:Cidade',
            $rps->infTomadorEndereco['cmun'],
            true,
            'Cidade',
            false
        );
        self::$dom->addChild(
            $endereco,
            'tc:Estado',
            $rps->infTomadorEndereco['uf'],
            true,
            'Estado',
            false
        );
        self::$dom->addChild(
            $endereco,
            'tc:Cep',
            $rps->infTomadorEndereco['cep'],
            true,
            'Cep',
            false
        );
        self::$dom->appChild($tomador, $endereco, 'Adicionando tag Endereco em Tomador');

        if ($rps->infTomador['tel'] != '' || $rps->infTomador['email'] != '') {
            $contato = self::$dom->createElement('tc:Contato');
            self::$dom->addChild(
                $contato,
                'tc:Telefone',
                $rps->infTomador['tel'],
                false,
                'Telefone Tomador',
                false
            );
            self::$dom->addChild(
                $contato,
                'tc:Email',
                $rps->infTomador['email'],
                false,
                'Email Tomador',
                false
            );
            self::$dom->appChild($tomador, $contato, 'Adicionando tag Contato em Tomador');
        }
        self::$dom->appChild($infRPS, $tomador, 'Adicionando tag Tomador em infRPS');

        if (!empty($rps->infIntermediario['razao'])) {
            $intermediario = self::$dom->createElement('tc:IntermediarioServico');
            self::$dom->addChild(
                $intermediario,
                'tc:RazaoSocial',
                $rps->infIntermediario['razao'],
                true,
                'Razao Intermediario',
                false
            );
            $cpfCnpj = self::$dom->createElement('tc:CpfCnpj');
            if ($rps->infIntermediario['tipo'] == 2) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'tc:Cnpj',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'CNPJ Intermediario',
                    false
                );
            } elseif ($rps->infIntermediario['tipo'] == 1) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'tc:Cpf',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'CPF Intermediario',
                    false
                );
            }
            self::$dom->appChild($intermediario, $cpfCnpj, 'Adicionando tag CpfCnpj em Intermediario');
            self::$dom->addChild(
                $intermediario,
                'tc:InscricaoMunicipal',
                $rps->infIntermediario['im'],
                false,
                'IM Intermediario',
                false
            );
            self::$dom->appChild($infRPS, $intermediario, 'Adicionando tag Intermediario em infRPS');
        }
        if (!empty($rps->infConstrucaoCivil['obra'])) {
            $construcao = self::$dom->createElement('tc:ContrucaoCivil');
            self::$dom->addChild(
                $construcao,
                'tc:CodigoObra',
                $rps->infConstrucaoCivil['obra'],
                true,
                'Codigo da Obra',
                false
            );
            self::$dom->addChild(
                $construcao,
                'tc:Art',
                $rps->infConstrucaoCivil['art'],
                true,
                'Art da Obra',
                false
            );
            self::$dom->appChild($infRPS, $construcao, 'Adicionando tag Construcao em infRPS');
        }

        self::$dom->appChild($root, $infRPS, 'Adicionando tag infRPS em RPS');
        self::$dom->appendChild($root);
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', self::$dom->saveXML());
        return $xml;
    }
}
