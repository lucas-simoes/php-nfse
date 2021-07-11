<?php


namespace NFePHP\NFSe\Models\Publica\Factories\v300;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\Publica\Factories\SignerRps;
use NFePHP\NFSe\Models\Publica\Factories\CartaCorrecaoNfseEnvio as CartaCorrecaoNfseEnvioBase;

class CartaCorrecaoNfseEnvio extends CartaCorrecaoNfseEnvioBase
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $remetenteCNPJCPF,
     * @param $inscricaoMunicipal,
     * @param $rps
     * @param $nfseNumero
     * @param $retificaValor
     * @return bool|string
     */
    public function render(
        $versao,
        $remetenteCNPJCPF,
        $inscricaoMunicipal,
        $nfseNumero,
        $rps,
        $retificaValor = false
    ) {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('CartaCorrecaoNfseEnvio');
        $root->setAttribute('xmlns', 'http://www.publica.inf.br');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        //Cria a tag Pedido
        $pedido = $dom->createElement('Pedido');

        //Adiciona a tag Pedido a consulta
        $dom->appChild($root, $pedido, 'Adicionando tag Pedido');

        //Cria a tag InfPedidoCartaCorrecao
        $infPedidoCartaCorrecao = $dom->createElement('InfPedidoCartaCorrecao');
        $infPedidoCartaCorrecao->setAttribute('id', "assinar");

        //Identificação Nfse
        $identificacaoNfse = $dom->createElement('IdentificacaoNfse');

        $dom->addChild(
            $identificacaoNfse,
            'Numero',
            $nfseNumero,
            true,
            "Numero do RPS",
            false
        );
        $dom->addChild(
            $identificacaoNfse,
            'Cnpj',
            $remetenteCNPJCPF,
            true,
            "Cnpj do Prestador",
            false
        );
        $dom->addChild(
            $identificacaoNfse,
            'InscricaoMunicipal',
            $inscricaoMunicipal,
            true,
            "Inscricao Municipal do prestador",
            false
        );
        $dom->addChild(
            $identificacaoNfse,
            'CodigoMunicipio',
            $rps->infCodigoMunicipio,
            true,
            'Codigo IBGE do municipio',
            false
        );

        if ($rps->infCodigoVerificacao) {
            $dom->addChild(
                $identificacaoNfse,
                'CodigoVerificacao',
                $rps->infCodigoVerificacao,
                true,
                'Codigo de Verificacao da nota fiscal',
                false
            );
        }

        //Adiciona a tag identificacaoNfse a consulta
        $dom->appChild($infPedidoCartaCorrecao, $identificacaoNfse, 'Adicionando tag IdentificacaoNfse');

        /** TomadorServico **/
        if (!empty($rps->infTomadorServico['razao'])) {
            $tomadorServico = $dom->createElement('TomadorServico');

            //Identificação TomadorServico
            if (!empty($rps->infTomadorServico['cnpjcpf'])) {
                $identificacaoTomador = $dom->createElement('IdentificacaoTomador');
                $cpfCnpjTomador = $dom->createElement('CpfCnpj');
                if ($rps->infTomadorServico['tipo'] == 2) {
                    $dom->addChild(
                        $cpfCnpjTomador,
                        'Cnpj',
                        $rps->infTomadorServico['cnpjcpf'],
                        true,
                        'TomadorServico CNPJ',
                        false
                    );
                } else {
                    $dom->addChild(
                        $cpfCnpjTomador,
                        'Cpf',
                        $rps->infTomadorServico['cnpjcpf'],
                        true,
                        'TomadorServico CPF',
                        false
                    );
                }
                $dom->appChild(
                    $identificacaoTomador,
                    $cpfCnpjTomador,
                    'Adicionando tag CpfCnpj em IdentificacaoTomador'
                );

                $dom->appChild(
                    $tomadorServico,
                    $identificacaoTomador,
                    'Adicionando tag IdentificacaoTomador em TomadorServico'
                );
            }

            //Razao Social
            $dom->addChild(
                $tomadorServico,
                'RazaoSocial',
                $rps->infTomadorServico['razao'],
                true,
                'RazaoSocial',
                false
            );

            //Endereço
            if (!empty($rps->infTomadorEndereco['end'])) {
                $endereco = $dom->createElement('Endereco');
                $dom->addChild(
                    $endereco,
                    'Endereco',
                    $rps->infTomadorEndereco['end'],
                    true,
                    'Endereco',
                    false
                );
                $dom->addChild(
                    $endereco,
                    'Numero',
                    $rps->infTomadorEndereco['numero'],
                    false,
                    'Numero',
                    false
                );
                if ($this->infTomadorEndereco['complemento']) {
                    $dom->addChild(
                        $endereco,
                        'Complemento',
                        $rps->infTomadorEndereco['complemento'],
                        false,
                        'Complemento',
                        false
                    );
                }
                $dom->addChild(
                    $endereco,
                    'Bairro',
                    $rps->infTomadorEndereco['bairro'],
                    false,
                    'Bairro',
                    false
                );
                if ($rps->infTomadorEndereco['cmun']) {
                    $dom->addChild(
                        $endereco,
                        'CodigoMunicipio',
                        $rps->infTomadorEndereco['cmun'],
                        false,
                        'CodigoMunicipio',
                        false
                    );
                }
                if ($rps->infTomadorEndereco['uf']) {
                    $dom->addChild(
                        $endereco,
                        'Uf',
                        $rps->infTomadorEndereco['uf'],
                        false,
                        'Uf',
                        false
                    );
                }
                $dom->addChild(
                    $endereco,
                    'Cep',
                    $rps->infTomadorEndereco['cep'],
                    false,
                    'Cep',
                    false
                );
                if ($rps->infTomadorEndereco['cod_pais']) {
                    $dom->addChild(
                        $endereco,
                        'CodigoPais',
                        $rps->infTomadorEndereco['cod_pais'],
                        false,
                        'Codigo do pais',
                        false
                    );
                }
                if ($rps->infTomadorEndereco['nome_municipio']) {
                    $dom->addChild(
                        $endereco,
                        'Municipio',
                        $rps->infTomadorEndereco['nome_municipio'],
                        false,
                        'Nome do municipio',
                        false
                    );
                }

                $dom->appChild($tomadorServico, $endereco, 'Adicionando tag Endereco em TomadorServico');
            }

            //Contato
            if ($rps->infTomadorServico['tel'] != '' || $rps->infTomadorServico['email'] != '') {
                $contato = $dom->createElement('Contato');
                $dom->addChild(
                    $contato,
                    'Telefone',
                    $rps->infTomadorServico['tel'],
                    false,
                    'Telefone TomadorServico',
                    false
                );
                $dom->addChild(
                    $contato,
                    'Email',
                    $rps->infTomadorServico['email'],
                    false,
                    'Email TomadorServico',
                    false
                );
                $dom->appChild($tomadorServico, $contato, 'Adicionando tag Contato em TomadorServico');
            }
            $dom->appChild($infPedidoCartaCorrecao, $tomadorServico, 'Adicionando tag TomadorServico em infPed$infPedidoCartaCorrecao');
        }

        /** FIM TomadorServico **/

        /** Intermediario Servico **/
        if (!empty($rps->infIntermediario['razao'])) {
            $intermediario = $dom->createElement('IntermediarioServico');
            //Razao Social
            $dom->addChild(
                $intermediario,
                'RazaoSocial',
                $rps->infIntermediario['razao'],
                true,
                'Razao Intermediario',
                false
            );
            $cpfCnpjIntermediario = $dom->createElement('CpfCnpj');
            if ($rps->infIntermediario['tipo'] == 2) {
                $dom->addChild(
                    $cpfCnpjIntermediario,
                    'Cnpj',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'Tomador CNPJ',
                    false
                );
            } else {
                $dom->addChild(
                    $cpfCnpjIntermediario,
                    'Cpf',
                    $rps->infIntermediario['cnpjcpf'],
                    true,
                    'Tomador CPF',
                    false
                );
            }
            $dom->appChild(
                $intermediario,
                $cpfCnpjIntermediario,
                'Adicionando tag CpfCnpj em IdentificacaoTomador'
            );

            $dom->addChild(
                $intermediario,
                'InscricaoMunicipal',
                $rps->infIntermediario['im'],
                false,
                'IM Intermediario',
                false
            );
            $dom->appChild($infPedidoCartaCorrecao, $intermediario, 'Adicionando tag IntermediarioServico em infPed$infPedidoCartaCorrecao');
        }
        /** FIM Intermediario Servico **/

        /** Discriminacao **/
        if (is_array($rps->infDiscriminacao)) {
            #se add detalhes sobre cada item do rps ou nfse
            $discriminacao = "{";
            foreach ($rps->infDiscriminacao as $item) {
                $discriminacao .= "[";
                foreach ($item as $key => $value) {
                    $discriminacao .= "[" . $key .= '=' . $value . "]";
                }
                $discriminacao .= "]";
            }
            $discriminacao .= "}";
        } else {
            $discriminacao = $rps->infDiscriminacao;
        }
        $dom->addChild(
            $infPedidoCartaCorrecao,
            'Discriminacao',
            $discriminacao,
            true,
            'Descrição dos serviços prestados',
            false
        );
        /** FIM Discriminacao **/
        //Adiciona a tag InfPedidoCartaCorrecao a consulta

        if ($retificaValor) {
            //Valores
            $valores = $dom->createElement('Valores');
            $dom->addChild(
                $valores,
                'ValorCofins',
                $rps->infValorCofins,
                false,
                'ValorCofins',
                false
            );
            $dom->addChild(
                $valores,
                'ValorInss',
                $rps->infValorInss,
                false,
                'ValorInss',
                false
            );
            $dom->addChild(
                $valores,
                'ValorIr',
                $rps->infValorIr,
                false,
                'ValorIr',
                false
            );
            $dom->addChild(
                $valores,
                'ValorCsll',
                $rps->infValorCsll,
                false,
                'ValorCsll',
                false
            );
            $dom->addChild(
                $valores,
                'OutrasRetencoes',
                $rps->infOutrasRetencoes,
                false,
                'OutrasRetencoes',
                false
            );
            $dom->addChild(
                $valores,
                'ValorPis',
                $rps->infValorPis,
                false,
                'ValorPis',
                false
            );            
            $dom->addChild(
                $valores,
                'DescontoCondicionado',
                $rps->infDescontoCondicionado,
                false,
                'DescontoCondicionado',
                false
            );
            $dom->addChild(
                $valores,
                'ValorLiquidoNfse',
                $rps->infValorLiquidoNfse,
                false,
                'ValorLiquidoNfse',
                false
            );
            $dom->appChild($infPedidoCartaCorrecao, $valores, 'Adicionando tag Valores em InfPedidoCartaCorrecao');
            //FIM Valores
        }
        $dom->appChild($pedido, $infPedidoCartaCorrecao, 'Adicionando tag InfPedidoCartaCorrecao');
        
        $body = SignerRps::sign(
            $this->certificate,            
            'InfPedidoCartaCorrecao',
            'id',
            $this->algorithm,
            [true, false, null, null],
            $dom,
            $pedido
        );
        
        //Parse para XML
        $xml = $dom->saveXML();
        $xml = $this->clear($xml);
        $this->validar($versao, $xml, $this->schemeFolder, 'schema_nfse_v300', '');
        #echo '<pre>'.print_r($xml).'</pre>';die;
        return $xml;
    }
}
