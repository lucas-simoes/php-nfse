<?php

namespace NFePHP\NFSe\Counties\M4113700\v103;

use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\SIGISS\Factories\Factory;

class ConsultarRps extends Factory
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
        $method = 'ConsultarRpsServicoPrestado';
        $xsd = "nfse-londrina-schema-v1_03";

        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento pai
        $root = $dom->createElement('ConsultarRpsServicoPrestadoEnvio');

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
            'numero_rps',
            $rps->infNumero,
            true,
            "Número do Rps procurado",
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

        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());
        $body = $this->clear($body);
        #$this->validar($versao, $body, "SIGISS/Londrina", $xsd, '');
        return $body;
    }
}
