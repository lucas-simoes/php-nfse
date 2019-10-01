<?php

namespace NFePHP\NFSe\Models\BHISS\Factories\v100;

/**
 * Classe para a renderização dos RPS em XML
 * conforme o modelo BHISS
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\BHISS\RenderRPS
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\Abrasf\Factories\Signer;
use NFePHP\NFSe\Models\Abrasf\RenderRps as RenderRPSBase;
use NFePHP\NFSe\Models\Abrasf\Rps;
use NFePHP\NFSe\Models\BHISS\Factories\SignerRps;

class RenderRps extends RenderRPSBase
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $data
     * @param \DateTimeZone $timezone
     * @param Certificate $certificate
     * @param int $algorithm
     * @return mixed|string
     */
    public static function toXml(
        $data,
        \DateTimeZone $timezone,
        Certificate $certificate,
        $algorithm = OPENSSL_ALGO_SHA1
    ) {
        self::$certificate = $certificate;
        self::$algorithm = $algorithm;
        self::$timezone = $timezone;
        $xml = '';
        if (is_object($data)) {
            $xml = self::render($data);
        } elseif (is_array($data)) {
            foreach ($data as $rps) {
                $xml .= self::render($rps);
            }
        }

        $xmlSigned = Signer::sign(
            self::$certificate,
            $xml,
            'Rps',
            'Id',
            self::$algorithm,
            [true, false, null, null],
            '',
            false,
            1,
            false

        );

        $xmlSigned = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xmlSigned);

        return $xmlSigned;
    }

    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @param $dom
     * @param $parent
     * @return mixed
     */
    protected static function render(Rps $rps, &$dom, &$parent)
    {
        self::$dom = $dom;
        $root = self::$dom->createElement('Rps');

        $infRPS = self::$dom->createElement("InfRps");
        $infRPS->setAttribute('Id', "infRps{$rps->infNumero}");

        $rps->infDataEmissao->setTimezone(self::$timezone);

        /** Identificação da RPS **/
        $identificacaoRps = self::$dom->createElement('IdentificacaoRps');

        self::$dom->addChild(
            $identificacaoRps,
            'Numero',
            $rps->infNumero,
            true,
            "Numero do RPS",
            false
        );
        self::$dom->addChild(
            $identificacaoRps,
            'Serie',
            $rps->infSerie,
            true,
            "Serie do RPS",
            false
        );
        self::$dom->addChild(
            $identificacaoRps,
            'Tipo',
            $rps->infTipo,
            true,
            "Tipo do RPS",
            false
        );
        self::$dom->appChild($infRPS, $identificacaoRps, 'Adicionando tag IdentificacaoRPS');
        /** FIM Identificação da RPS **/

        self::$dom->addChild(
            $infRPS,
            'DataEmissao',
            $rps->infDataEmissao->format('Y-m-d\TH:i:s'),
            true,
            'Data de Emissão do RPS',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'NaturezaOperacao',
            $rps->infNaturezaOperacao,
            true,
            'Natureza da operação',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'RegimeEspecialTributacao',
            $rps->infRegimeEspecialTributacao,
            false,
            'RegimeEspecialTributacao',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'OptanteSimplesNacional',
            $rps->infOptanteSimplesNacional,
            true,
            'OptanteSimplesNacional',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'IncentivadorCultural',
            $rps->infIncentivadorCultural,
            true,
            'IncentivadorCultural',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'Status',
            $rps->infStatus,
            true,
            'Status',
            false
        );

        /** RPS Substituto */
        if (!empty($rps->infRpsSubstituido['numero'])) {
            $rpssubs = self::$dom->createElement('RpsSubstituido');
            self::$dom->addChild(
                $rpssubs,
                'Numero',
                $rps->infRpsSubstituido['numero'],
                true,
                'Numero',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'Serie',
                $rps->infRpsSubstituido['serie'],
                true,
                'Serie',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'Tipo',
                $rps->infRpsSubstituido['tipo'],
                true,
                'tipo',
                false
            );
            self::$dom->appChild($infRPS, $rpssubs, 'Adicionando tag RpsSubstituido em infRps');
        }
        /** FIM RPS Substituto **/


        /** Serviços **/
        $servico = self::$dom->createElement('Servico');
        /** Valores **/
        $valores = self::$dom->createElement('Valores');
        self::$dom->addChild(
            $valores,
            'ValorServicos',
            $rps->infValorServicos,
            true,
            'ValorServicos',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorDeducoes',
            $rps->infValorDeducoes,
            false,
            'ValorDeducoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorPis',
            $rps->infValorPis,
            false,
            'ValorPis',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorCofins',
            $rps->infValorCofins,
            false,
            'ValorCofins',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorInss',
            $rps->infValorInss,
            false,
            'ValorInss',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorIr',
            $rps->infValorIr,
            false,
            'ValorIr',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorCsll',
            $rps->infValorCsll,
            false,
            'ValorCsll',
            false
        );
        self::$dom->addChild(
            $valores,
            'IssRetido',
            $rps->infIssRetido,
            true,
            'IssRetido',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorIss',
            $rps->infValorIss,
            false,
            'ValorIss',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorIssRetido',
            $rps->infValorIssRetido,
            false,
            'ValorIssRetido',
            false
        );
        self::$dom->addChild(
            $valores,
            'OutrasRetencoes',
            $rps->infOutrasRetencoes,
            false,
            'OutrasRetencoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'BaseCalculo',
            $rps->infBaseCalculo,
            false,
            'BaseCalculo',
            false
        );
        self::$dom->addChild(
            $valores,
            'Aliquota',
            number_format(round($rps->infAliquota / 100, 4), 2, '.', ''),
            false,
            'Aliquota',
            false
        );
        self::$dom->addChild(
            $valores,
            'ValorLiquidoNfse',
            $rps->infValorLiquidoNfse,
            false,
            'ValorLiquidoNfse',
            false
        );
        self::$dom->addChild(
            $valores,
            'DescontoIncondicionado',
            $rps->infDescontoIncondicionado,
            false,
            'DescontoIncondicionado',
            false
        );
        self::$dom->addChild(
            $valores,
            'DescontoCondicionado',
            $rps->infDescontoCondicionado,
            false,
            'DescontoCondicionado',
            false
        );
        self::$dom->appChild($servico, $valores, 'Adicionando tag Valores em Servico');
        /** FIM Valores **/


        self::$dom->addChild(
            $servico,
            'ItemListaServico',
            $rps->infItemListaServico,
            true,
            'ItemListaServico',
            false
        );
        self::$dom->addChild(
            $servico,
            'CodigoCnae',
            $rps->infCodigoCnae,
            false,
            'CodigoCnae',
            false
        );
        self::$dom->addChild(
            $servico,
            'CodigoTributacaoMunicipio',
            $rps->infCodigoTributacaoMunicipio,
            false,
            'CodigoTributacaoMunicipio',
            false
        );
        self::$dom->addChild(
            $servico,
            'Discriminacao',
            $rps->infDiscriminacao,
            true,
            'Discriminacao',
            false
        );
        self::$dom->addChild(
            $servico,
            'CodigoMunicipio',
            $rps->infMunicipioPrestacaoServico,
            true,
            'CodigoMunicipio',
            false
        );
        self::$dom->appChild($infRPS, $servico, 'Adicionando tag Servico');
        /** FIM Serviços **/

        /** Prestador **/
        $prestador = self::$dom->createElement('Prestador');
        self::$dom->addChild(
            $prestador,
            'Cnpj',
            $rps->infPrestador['cnpjcpf'],
            true,
            'Prestador CNPJ',
            false
        );

        self::$dom->addChild(
            $prestador,
            'InscricaoMunicipal',
            $rps->infPrestador['im'],
            false,
            'InscricaoMunicipal',
            false
        );
        self::$dom->appChild($infRPS, $prestador, 'Adicionando tag Prestador em infRPS');
        /** FIM Prestador **/

        /** Tomador **/
        if (!empty($rps->infTomador['razao'])) {
            $tomador = self::$dom->createElement('Tomador');
            //Identificação do tomador
            if (!empty($rps->infTomador['cnpjcpf'])) {
                //CPF/CNPJ
                $identificacaoTomador = self::$dom->createElement('IdentificacaoTomador');
                $cpfCnpjTomador = self::$dom->createElement('CpfCnpj');
                if ($rps->infTomador['tipo'] == 2) {
                    self::$dom->addChild(
                        $cpfCnpjTomador,
                        'Cnpj',
                        $rps->infTomador['cnpjcpf'],
                        true,
                        'Tomador CNPJ',
                        false
                    );
                } else {
                    self::$dom->addChild(
                        $cpfCnpjTomador,
                        'Cpf',
                        $rps->infTomador['cnpjcpf'],
                        true,
                        'Tomador CPF',
                        false
                    );
                }
                self::$dom->appChild($identificacaoTomador, $cpfCnpjTomador,
                    'Adicionando tag CpfCnpj em IdentificacaTomador');

                //Inscrição Municipal
                self::$dom->addChild(
                    $identificacaoTomador,
                    'InscricaoMunicipal',
                    $rps->infTomador['im'],
                    false,
                    'InscricaoMunicipal',
                    false
                );
                self::$dom->appChild($tomador, $identificacaoTomador,
                    'Adicionando tag IdentificacaoTomador em Tomador');

            }

            //Razão Social
            self::$dom->addChild(
                $tomador,
                'RazaoSocial',
                $rps->infTomador['razao'],
                true,
                'RazaoSocial',
                false
            );

            //Endereço
            if (!empty($rps->infTomadorEndereco['end'])) {
                $endereco = self::$dom->createElement('Endereco');
                self::$dom->addChild(
                    $endereco,
                    'Endereco',
                    $rps->infTomadorEndereco['end'],
                    true,
                    'Endereco',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Numero',
                    $rps->infTomadorEndereco['numero'],
                    false,
                    'Numero',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Complemento',
                    $rps->infTomadorEndereco['complemento'],
                    false,
                    'Complemento',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Bairro',
                    $rps->infTomadorEndereco['bairro'],
                    false,
                    'Bairro',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'CodigoMunicipio',
                    $rps->infTomadorEndereco['cmun'],
                    false,
                    'CodigoMunicipio',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Uf',
                    $rps->infTomadorEndereco['uf'],
                    false,
                    'Uf',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'Cep',
                    $rps->infTomadorEndereco['cep'],
                    false,
                    'Cep',
                    false
                );
                self::$dom->appChild($tomador, $endereco, 'Adicionando tag Endereco em Tomador');
            }

            //Contato
            if ($rps->infTomador['tel'] != '' || $rps->infTomador['email'] != '') {
                $contato = self::$dom->createElement('Contato');
                self::$dom->addChild(
                    $contato,
                    'Telefone',
                    $rps->infTomador['tel'],
                    false,
                    'Telefone Tomador',
                    false
                );
                self::$dom->addChild(
                    $contato,
                    'Email',
                    $rps->infTomador['email'],
                    false,
                    'Email Tomador',
                    false
                );
                self::$dom->appChild($tomador, $contato, 'Adicionando tag Contato em Tomador');
            }
            self::$dom->appChild($infRPS, $tomador, 'Adicionando tag Tomador em infRPS');

        }
        /** Fim Tomador **/

        /** Intermediário Serviço **/
        if (!empty($rps->infIntermediario['razao'])) {
            $intermediario = self::$dom->createElement('IntermediarioServico');
            self::$dom->addChild(
                $intermediario,
                'RazaoSocial',
                $rps->infIntermediario['razao'],
                true,
                'Razao Intermediario',
                false
            );
            $cpfCnpj = self::$dom->createElement('CpfCnpj');
            if ($rps->infIntermediario['tipo'] == 2) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'Cnpj',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'CNPJ Intermediario',
                    false
                );
            } elseif ($rps->infIntermediario['tipo'] == 1) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'Cpf',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'CPF Intermediario',
                    false
                );
            }
            self::$dom->appChild($intermediario, $cpfCnpj, 'Adicionando tag CpfCnpj em Intermediario');
            self::$dom->addChild(
                $intermediario,
                'InscricaoMunicipal',
                $rps->infIntermediario['im'],
                false,
                'IM Intermediario',
                false
            );
            self::$dom->appChild($infRPS, $intermediario, 'Adicionando tag Intermediario em infRPS');
        }
        /** FIM Intermediário Serviço **/

        /** Construção Civil **/
        if (!empty($rps->infConstrucaoCivil['obra'])) {
            $construcao = self::$dom->createElement('ContrucaoCivil');
            self::$dom->addChild(
                $construcao,
                'CodigoObra',
                $rps->infConstrucaoCivil['obra'],
                true,
                'Codigo da Obra',
                false
            );
            self::$dom->addChild(
                $construcao,
                'Art',
                $rps->infConstrucaoCivil['art'],
                true,
                'Art da Obra',
                false
            );
            self::$dom->appChild($infRPS, $construcao, 'Adicionando tag Construcao em infRPS');
        }
        /** FIM Construção Civil **/

        self::$dom->appChild($root, $infRPS, 'Adicionando tag infRPS em RPS');
        self::$dom->appChild($parent, $root, 'Adicionando tag RPS na ListaRps');

        return $root;
    }

    public static function appendRps(
        $data,
        \DateTimeZone $timezone,
        Certificate $certificate,
        $algorithm = OPENSSL_ALGO_SHA1,
        &$dom,
        &$parent
    ) {

        self::$certificate = $certificate;
        self::$algorithm = $algorithm;
        self::$timezone = $timezone;

        if (is_object($data)) {
            //Gera a RPS
            $rootNode = self::render($data, $dom, $parent);
        }

        //Gera o nó com a assinatura
        $signatureNode = SignerRps::sign(
            self::$certificate,
            'InfRps',
            'Id',
            self::$algorithm,
            [false, false, null, null],
            $dom,
            $rootNode
        );
    }
}
