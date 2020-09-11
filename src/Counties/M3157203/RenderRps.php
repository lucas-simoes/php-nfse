<?php

namespace NFePHP\NFSe\Counties\M3157203;

use NFePHP\Common\Certificate;
use NFePHP\NFSe\Counties\M3157203\SignerRps as Signer;
use NFePHP\NFSe\Models\Abrasf\Factories\v203\RenderRps as RenderRPS203;
use NFePHP\NFSe\Models\Abrasf\Rps;

class RenderRps extends RenderRPS203
{
    /**
     * Monta o xml com base no objeto Rps
     * @param Rps $rps
     * @return string
     */
    protected static function render(Rps $rps, &$dom, &$parent)
    {
        self::$dom = $dom;
        $root = self::$dom->createElement('nfs:Rps');

        $infRPS = self::$dom->createElement("nfs:InfDeclaracaoPrestacaoServico");
        $infRPS->setAttribute('Id', "infRPS{$rps->infNumero}");

        /** RPS Filha **/
        $rpsInf = self::$dom->createElement('nfs:Rps');
        $rpsInf->setAttribute('Id', $rps->infNumero);

        //Identificação RPS
        $identificacaoRps = self::$dom->createElement('nfs:IdentificacaoRps');

        $rps->infDataEmissao->setTimezone(self::$timezone);

        self::$dom->addChild(
            $identificacaoRps,
            'nfs:Numero',
            $rps->infNumero,
            true,
            "Numero do RPS",
            false
        );
        self::$dom->addChild(
            $identificacaoRps,
            'nfs:Serie',
            $rps->infSerie,
            true,
            "Serie do RPS",
            false
        );
        self::$dom->addChild(
            $identificacaoRps,
            'nfs:Tipo',
            $rps->infTipo,
            true,
            "Tipo do RPS",
            false
        );
        self::$dom->appChild($rpsInf, $identificacaoRps, 'Adicionando tag IdentificacaoRPS');
        //FIM Identificação RPS

        self::$dom->addChild(
            $rpsInf,
            'nfs:DataEmissao',
            $rps->infDataEmissao->format('Y-m-d'),
            true,
            'Data de Emissão do RPS',
            false
        );

        self::$dom->addChild(
            $rpsInf,
            'nfs:Status',
            $rps->infStatus,
            true,
            'Status',
            false
        );

        //RPS Substituido
        if (!empty($rps->infRpsSubstituido['numero'])) {
            $rpssubs = self::$dom->createElement('nfs:RpsSubstituido');
            self::$dom->addChild(
                $rpssubs,
                'nfs:Numero',
                $rps->infRpsSubstituido['numero'],
                true,
                'Numero',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'nfs:Serie',
                $rps->infRpsSubstituido['serie'],
                true,
                'Serie',
                false
            );
            self::$dom->addChild(
                $rpssubs,
                'nfs:Tipo',
                $rps->infRpsSubstituido['tipo'],
                true,
                'tipo',
                false
            );
            self::$dom->appChild($rpsInf, $rpssubs, 'Adicionando tag RpsSubstituido em infRps');
        }

        self::$dom->appChild($infRPS, $rpsInf, 'Adicionando tag Rps');
        /** FIM RPS Filha **/

        self::$dom->addChild(
            $infRPS,
            'nfs:Competencia',
            $rps->infDataEmissao->format('Y-m-d'),
            true,
            'Competencia Emissão do RPS',
            false
        );

        /** Serviços **/
        $servico = self::$dom->createElement('nfs:Servico');

        //Valores
        $valores = self::$dom->createElement('nfs:Valores');
        self::$dom->addChild(
            $valores,
            'nfs:ValorServicos',
            $rps->infValorServicos,
            true,
            'ValorServicos',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:ValorDeducoes',
            $rps->infValorDeducoes,
            false,
            'ValorDeducoes',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:ValorPis',
            $rps->infValorPis,
            false,
            'ValorPis',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:ValorCofins',
            $rps->infValorCofins,
            false,
            'ValorCofins',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:ValorInss',
            $rps->infValorInss,
            false,
            'ValorInss',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:ValorIr',
            $rps->infValorIr,
            false,
            'ValorIr',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:ValorCsll',
            $rps->infValorCsll,
            false,
            'ValorCsll',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:OutrasRetencoes',
            $rps->infOutrasRetencoes,
            false,
            'OutrasRetencoes',
            false
        );

        self::$dom->addChild(
            $valores,
            'nfs:ValorIss',
            $rps->infValorIss,
            false,
            'ValorIss',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:Aliquota',
            number_format($rps->infAliquota, 2, '.', ''),
            false,
            'Aliquota',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:DescontoCondicionado',
            $rps->infDescontoCondicionado,
            false,
            'DescontoCondicionado',
            false
        );
        self::$dom->addChild(
            $valores,
            'nfs:DescontoIncondicionado',
            $rps->infDescontoIncondicionado,
            false,
            'DescontoIncondicionado',
            false
        );
        self::$dom->appChild($servico, $valores, 'Adicionando tag Valores em Servico');
        //FIM Valores

        self::$dom->addChild(
            $servico,
            'nfs:IssRetido',
            $rps->infIssRetido,
            true,
            'IssRetido',
            false
        );
        // <======= RESPONSAVEL RETENCAO AQUI =======>
        self::$dom->addChild(
            $servico,
            'nfs:ItemListaServico',
            $rps->infItemListaServico,
            true,
            'ItemListaServico',
            false
        );
        self::$dom->addChild(
            $servico,
            'nfs:CodigoCnae',
            $rps->infCodigoCnae,
            false,
            'CodigoCnae',
            false
        );
        self::$dom->addChild(
            $servico,
            'nfs:CodigoTributacaoMunicipio',
            $rps->infCodigoTributacaoMunicipio,
            false,
            'CodigoTributacaoMunicipio',
            false
        );
        self::$dom->addChild(
            $servico,
            'nfs:Discriminacao',
            $rps->infDiscriminacao,
            true,
            'Discriminacao',
            false
        );
        self::$dom->addChild(
            $servico,
            'nfs:CodigoMunicipio',
            $rps->infMunicipioPrestacaoServico,
            true,
            'nfs:CodigoMunicipio',
            false
        );

        self::$dom->addChild(
            $servico,
            'nfs:ExigibilidadeISS',
            1,
            true,
            'ExigibilidadeISS',
            false
        );
        self::$dom->addChild(
            $servico,
            'nfs:MunicipioIncidencia',
            $rps->infMunicipioPrestacaoServico,
            false,
            'MunicipioIncidencia',
            false
        );

        self::$dom->appChild($infRPS, $servico, 'Adicionando tag Servico');
        /** FIM Serviços **/

        /** Prestador **/
        $prestador = self::$dom->createElement('nfs:Prestador');

        //Cpf/Cnpj
        if (!empty($rps->infPrestador['cnpjcpf'])) {
            $cpfCnpj = self::$dom->createElement('nfs:CpfCnpj');
            if ($rps->infPrestador['tipo'] == 2) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'nfs:Cnpj',
                    $rps->infPrestador['cnpjcpf'],
                    true,
                    'Prestador CNPJ',
                    false
                );
            } else {
                self::$dom->addChild(
                    $cpfCnpj,
                    'nfs:Cpf',
                    $rps->infPrestador['cnpjcpf'],
                    true,
                    'Prestador CPF',
                    false
                );
            }
            self::$dom->appChild($prestador, $cpfCnpj, 'Adicionando tag CpfCnpj em Prestador');
        }

        //Inscrição Municipal
        self::$dom->addChild(
            $prestador,
            'nfs:InscricaoMunicipal',
            $rps->infPrestador['im'],
            false,
            'InscricaoMunicipal',
            false
        );
        self::$dom->appChild($infRPS, $prestador, 'Adicionando tag Prestador em infRPS');
        /** FIM Prestador **/

        /** Tomador **/
        if (!empty($rps->infTomador['razao'])) {
            $tomador = self::$dom->createElement('nfs:Tomador');

            //Identificação Tomador
            if (!empty($rps->infTomador['cnpjcpf'])) {
                $identificacaoTomador = self::$dom->createElement('nfs:IdentificacaoTomador');
                $cpfCnpjTomador = self::$dom->createElement('nfs:CpfCnpj');
                if ($rps->infTomador['tipo'] == 2) {
                    self::$dom->addChild(
                        $cpfCnpjTomador,
                        'nfs:Cnpj',
                        $rps->infTomador['cnpjcpf'],
                        true,
                        'Tomador CNPJ',
                        false
                    );
                } else {
                    self::$dom->addChild(
                        $cpfCnpjTomador,
                        'nfs:Cpf',
                        $rps->infTomador['cnpjcpf'],
                        true,
                        'Tomador CPF',
                        false
                    );
                }
                self::$dom->appChild(
                    $identificacaoTomador,
                    $cpfCnpjTomador,
                    'Adicionando tag CpfCnpj em IdentificacaTomador'
                );


                //Inscrição Municipal
                self::$dom->addChild(
                    $identificacaoTomador,
                    'nfs:InscricaoMunicipal',
                    $rps->infTomador['im'],
                    false,
                    'InscricaoMunicipal',
                    false
                );
                self::$dom->appChild(
                    $tomador,
                    $identificacaoTomador,
                    'Adicionando tag IdentificacaoTomador em Tomador'
                );
            }

            //Razao Social
            self::$dom->addChild(
                $tomador,
                'nfs:RazaoSocial',
                $rps->infTomador['razao'],
                true,
                'RazaoSocial',
                false
            );

            //Endereço
            if (!empty($rps->infTomadorEndereco['end'])) {
                $endereco = self::$dom->createElement('nfs:Endereco');
                self::$dom->addChild(
                    $endereco,
                    'nfs:Endereco',
                    $rps->infTomadorEndereco['end'],
                    true,
                    'Endereco',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'nfs:Numero',
                    $rps->infTomadorEndereco['numero'],
                    false,
                    'Numero',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'nfs:Complemento',
                    $rps->infTomadorEndereco['complemento'],
                    false,
                    'Complemento',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'nfs:Bairro',
                    $rps->infTomadorEndereco['bairro'],
                    false,
                    'Bairro',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'nfs:CodigoMunicipio',
                    $rps->infTomadorEndereco['cmun'],
                    false,
                    'CodigoMunicipio',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'nfs:Uf',
                    $rps->infTomadorEndereco['uf'],
                    false,
                    'Uf',
                    false
                );
                self::$dom->addChild(
                    $endereco,
                    'nfs:Cep',
                    $rps->infTomadorEndereco['cep'],
                    false,
                    'Cep',
                    false
                );

                self::$dom->appChild($tomador, $endereco, 'Adicionando tag Endereco em Tomador');
            }

            //Contato
            if ($rps->infTomador['tel'] != '' || $rps->infTomador['email'] != '') {
                $contato = self::$dom->createElement('nfs:Contato');
                self::$dom->addChild(
                    $contato,
                    'nfs:Telefone',
                    $rps->infTomador['tel'],
                    false,
                    'Telefone Tomador',
                    false
                );
                self::$dom->addChild(
                    $contato,
                    'nfs:Email',
                    $rps->infTomador['email'],
                    false,
                    'Email Tomador',
                    false
                );
                self::$dom->appChild($tomador, $contato, 'Adicionando tag Contato em Tomador');
            }
            self::$dom->appChild($infRPS, $tomador, 'Adicionando tag Tomador em infRPS');
        }

        /** FIM Tomador **/

        /** Intermediario **/
        if (!empty($rps->infIntermediario['razao'])) {
            $intermediario = self::$dom->createElement('nfs:Intermediario');
            $cpfCnpj = self::$dom->createElement('nfs:CpfCnpj');
            if ($rps->infIntermediario['tipo'] == 2) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'nfs:Cnpj',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'CNPJ Intermediario',
                    false
                );
            } elseif ($rps->infIntermediario['tipo'] == 1) {
                self::$dom->addChild(
                    $cpfCnpj,
                    'nfs:Cpf',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'CPF Intermediario',
                    false
                );
            }
            self::$dom->appChild($intermediario, $cpfCnpj, 'Adicionando tag CpfCnpj em Intermediario');
            self::$dom->addChild(
                $intermediario,
                'nfs:InscricaoMunicipal',
                $rps->infIntermediario['im'],
                false,
                'IM Intermediario',
                false
            );

            //Razao Social
            self::$dom->addChild(
                $intermediario,
                'nfs:RazaoSocial',
                $rps->infIntermediario['razao'],
                true,
                'Razao Intermediario',
                false
            );
            self::$dom->appChild($infRPS, $intermediario, 'Adicionando tag Intermediario em infRPS');
        }
        /** FIM Intermediario **/

        /** Construção Civil **/
        if (!empty($rps->infConstrucaoCivil['obra'])) {
            $construcao = self::$dom->createElement('nfs:ContrucaoCivil');
            self::$dom->addChild(
                $construcao,
                'nfs:CodigoObra',
                $rps->infConstrucaoCivil['obra'],
                false,
                'Codigo da Obra',
                false
            );
            self::$dom->addChild(
                $construcao,
                'nfs:Art',
                $rps->infConstrucaoCivil['art'],
                true,
                'Art da Obra',
                false
            );
            self::$dom->appChild($infRPS, $construcao, 'Adicionando tag Construcao em infRPS');
        }
        /** FIM Construção Civil **/

        self::$dom->addChild(
            $infRPS,
            'nfs:RegimeEspecialTributacao',
            $rps->infRegimeEspecialTributacao,
            false,
            'RegimeEspecialTributacao',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'nfs:OptanteSimplesNacional',
            $rps->infOptanteSimplesNacional,
            true,
            'OptanteSimplesNacional',
            false
        );
        self::$dom->addChild(
            $infRPS,
            'nfs:IncentivoFiscal',
            $rps->infIncentivadorCultural,
            true,
            'IncentivoFiscal',
            false
        );

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
        $signatureNode = Signer::signDoc(
            self::$certificate,
            'nfs:Rps',
            'Id',
            self::$algorithm,
            [false, false, null, null],
            $dom,
            $rootNode
        );
    }
}
