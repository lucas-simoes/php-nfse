<?php

namespace NFePHP\NFSe\Counties\M4113700\v103;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\SIGISS\Factories\Factory;

class GerarNota extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $rpss
     * @return string
     */
    public function render(
        $versao,
        $rps
    ) {
        $method = 'GerarNota';
        $xsd = "nfse-londrina-schema-v1_03";
        
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('DescricaoRps');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        $dom->addChild(
            $root,
            'ccm',
            $rps->infPrestador['ccm'],
            true,
            "CMC do prestador de serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'cnpj',
            $rps->infPrestador['cnpjcpf'],
            true,
            "CNPJ do prestador de serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
 
        $dom->addChild(
            $root,
            'cpf',
            $rps->infPrestador['cpf_usuario'],
            true,
            "CPF do usuário cadastrado",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'senha',
            $rps->infPrestador['senha_usuario'],
            true,
            "Senha do prestador do serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'aliquota',
            $rps->infAliquota,
            true,
            "Alíquota do ISSQN aplicada à base de cálculo",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'servico',
            $rps->infServico,
            true,
            "Código de tributação do município  do serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        if($rps->infConstrucaoCivil) {
            $dom->addChild(
                $root,
                'codigo_obra',
                $rps->infConstrucaoCivil['obra'],
                true,
                "Código da obra (com dv)",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );

            $dom->addChild(
                $root,
                'obra_art',
                $rps->infConstrucaoCivil['art'],
                true,
                "Código ART da obra",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }

        $dom->addChild(
            $root,
            'situacao',
            $rps->infSituacao,
            true,
            "Situação do serviço",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'valor',
            $rps->infValor,
            true,
            "Valor da nota fiscal",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'base',
            $rps->infBase,
            true,
            "Valor da base de cálculo",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
        
        $dom->addChild(
            $root,
            'ir',
            $rps->infIr,
            true,
            "Valor do IR a ser retido na fonte",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'pis',
            $rps->infPis,
            true,
            "Valor do PIS a ser retido na fonte",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'cofins',
            $rps->infCofins,
            true,
            "Valor do Confins a ser retido na fonte",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'csll',
            $rps->infCsll,
            true,
            "Valor do Csll a ser retido na fonte",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'inss',
            $rps->infInss,
            true,
            "Valor da contribuição ao INSS a ser retida na fonte.",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );


        $dom->addChild(
            $root,
            'retencao_iss',
            $rps->infRetencaoIss,
            true,
            "Valor de retenção do ISS. ",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        if($rps->infIncentivoFiscal) {
            $dom->addChild(
                $root,
                'incentivo_fiscal',
                $rps->infIncentivoFiscal,
                true,
                "Incentivo fiscal",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }

        if($rps->infMunicipioPrestacaoServico) {
            $dom->addChild(
                $root,
                'cod_municipio_prestacao_servico',
                $rps->infMunicipioPrestacaoServico,
                true,
                "Código do município em que o serviço foi prestado",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }
        if($rps->infPaisPrestacaoServico) {
            $dom->addChild(
                $root,
                'cod_pais_prestacao_servico',
                $rps->infPaisPrestacaoServico,
                true,
                "Código do país em que o serviço foi prestado (Tabela do BACEN)",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }

        if($rps->infMunicipioIncidencia) {
            $dom->addChild(
                $root,
                'cod_municipio_incidencia',
                $rps->infMunicipioIncidencia,
                true,
                "Código do município onde ocorre a incidência do ISSQN",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }

        $dom->addChild(
            $root,
            'descricaoNF',
            $rps->infDescricaoNF,
            true,
            "Descrição do serviço prestado",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_tipo',
            $rps->infTomador['tipo'],
            true,
            "Tipo de tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_cnpj',
            $rps->infTomador['cnpjcpf'],
            true,
            "Cpf/Cnpj do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_email',
            $rps->infTomador['email'],
            true,
            "Email do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_im',
            $rps->infTomador['im'],
            true,
            "Inscrição municipal do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
        $dom->addChild(
            $root,
            'tomador_ie',
            $rps->infTomador['ie'],
            true,
            "Inscrição estadual do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_razao',
            $rps->infTomador['razao'],
            true,
            "Razão Social do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
        
        $dom->addChild(
            $root,
            'tomador_endereco',
            $rps->infTomadorEndereco['end'],
            true,
            "Endereço do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
        
        $dom->addChild(
            $root,
            'tomador_numero',
            $rps->infTomadorEndereco['numero'],
            true,
            "Número do endereço do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_complemento',
            $rps->infTomadorEndereco['complemento'],
            true,
            "Complemento do ndereço do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
        
        $dom->addChild(
            $root,
            'tomador_bairro',
            $rps->infTomadorEndereco['bairro'],
            true,
            "Bairro do endereço do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_CEP',
            $rps->infTomadorEndereco['cep'],
            true,
            "CEP do endereço do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_cod_cidade',
            $rps->infTomadorEndereco['cmun'],
            true,
            "Código IBGE do municipo do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_fone',
            $rps->infTomador['tel'],
            true,
            "Telefone do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_ramal',
            $rps->infTomador['ramal'],
            true,
            "Ramal do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );

        $dom->addChild(
            $root,
            'tomador_fax',
            $rps->infTomador['fax'],
            true,
            "Fax do tomador",
            true,
            [['attr' => 'xmlns', 'value' => '']]
        );
        if($rps->infConstrucaoCivil) {
            $dom->addChild(
                $root,
                'obra_alvara_numero',
                $rps->infConstrucaoCivil['obra_alvara_numero'],
                true,
                "Número do alvará da obra",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );

            $dom->addChild(
                $root,
                'obra_alvara_ano',
                $rps->infConstrucaoCivil['obra_alvara_ano'],
                true,
                "Ano do alvará da obra.",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );

            $dom->addChild(
                $root,
                'obra_local_lote',
                $rps->infConstrucaoCivil['obra_local_lote'],
                true,
                "Identificacao do lote do local da obra",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );

            $dom->addChild(
                $root,
                'obra_local_quadra',
                $rps->infConstrucaoCivil['obra_local_quadra'],
                true,
                "Identificacao da quadra do local da obra",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );

            $dom->addChild(
                $root,
                'obra_local_bairro',
                $rps->infConstrucaoCivil['obra_local_bairro'],
                true,
                "Identificacao do bairro do local da obra",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }
        if($rps->infNumero) {
            $dom->addChild(
                $root,
                'rps_num',
                $rps->infNumero,
                true,
                "Número do RPS",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );

            $dom->addChild(
                $root,
                'rps_serie',
                $rps->infSerie,
                true,
                "Série do RPS",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
    
            $dom->addChild(
                $root,
                'rps_tipo',
                $rps->infTipo,
                true,
                "Tipo do RPS",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
    
            list($ano, $mes, $dia) = explode("-", $rps->infDataEmissao->format('Y-m-d'));
            $dom->addChild(
                $root,
                'rps_dia',
                $dia,
                true,
                "Dia que foi emitido o RPS",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
    
            $dom->addChild(
                $root,
                'rps_mes',
                $mes,
                true,
                "Mes que foi emitido o RPS",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
            
            $dom->addChild(
                $root,
                'rps_ano',
                $ano,
                true,
                "Ano que foi emitido o RPS",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
        }

        if($rps->infRpsSubstituido) {
            $dom->addChild(
                $root,
                'nfse_substituida',
                $rps->infRpsSubstituido['nfse'],
                true,
                "Número da NFS-e a ser substituída",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );
            $dom->addChild(
                $root,
                'rps_substituido',
                $rps->infRpsSubstituido['rps'],
                true,
                "Número do RPS ser substituído",
                true,
                [['attr' => 'xmlns', 'value' => '']]
            );   
        }

        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        #$this->validar($versao, $body, "SIGISS/Londrina", $xsd, '');
        return $body;
    }
}
