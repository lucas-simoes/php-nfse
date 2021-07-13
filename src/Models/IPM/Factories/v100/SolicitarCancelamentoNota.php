<?php

namespace NFePHP\NFSe\Models\IPM\Factories\v100;

use Exception;
use InvalidArgumentException;
use stdClass;
use NFePHP\NFSe\Models\IPM\CancelarRps;
use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\IPM\Factories\Signer;
use NFePHP\NFSe\Models\IPM\Factories\Factory;

class SolicitarCancelamentoNota extends Factory
{
    /**
     * Método usado para gerar o XML do Soap Request
     * @param $versao
     * @param $rps
     * @return string
     */
    public function render(
        CancelarRps $rps,
        stdClass $config
    ) {
        $dom = new Dom('1.0', 'utf-8');
        $dom->formatOutput = false;
        //Cria o elemento solicitacao_cancelamento
        $root = $dom->createElement('solicitacao_cancelamento');

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        if ($config->teste) {
            $dom->addChild(
                $root,
                'nfse_teste',
                1,
                true,
                "Definir como teste de integração",
                true
            );
        }

        //Cria o elemento prestador
        $prestador = $dom->createElement('prestador');

        $dom->addChild(
            $prestador,
            'cpfcnpj',
            $rps->infCpfCnpjPrestador,
            true,
            "CPF/CNPJ do emissor da nota",
            true
        );

        $dom->addChild(
            $prestador,
            'cidade',
            $config->cod_tom_municipio,
            true,
            "Código tom do municipio do emissor da nota",
            true
        );

        //Adiciona as tags ao DOM
        $root->appendChild($prestador);

        if (empty($rps->infDocumentos)) {
            throw new InvalidArgumentException("Deve ser informado ao menos um item de documento de cancelamento");
        }

        if (count($rps->infDocumentos) > 25) {
            throw new InvalidArgumentException("Deve ser informado até 25 itens de documento de cancelamento");
        }

        //Cria o elemento documentos
        $documentos = $dom->createElement('documentos');

        /** @var $item CancelarRps[] */
        foreach ($rps->infDocumentos as $documento) {
            //Cria o elemento nfse
            $nfse = $dom->createElement('nfse');

            $dom->addChild(
                $nfse,
                'numero',
                $documento->infNumeroNfse,
                true,
                "Número da nota a ser substituida",
                true
            );

            $dom->addChild(
                $nfse,
                'serie',
                $documento->infSerieNfse,
                true,
                "Séria da nota a ser substituida",
                true
            );

            $dom->addChild(
                $nfse,
                'observacao',
                $documento->infObservacao,
                true,
                "Motivo do cancelamento da NFS-e",
                true
            );

            if ($documento->infNumeroNfseSubstituta || $documento->infSerieNfseSubstituta) {
                //Cria o elemento substituta
                $substituta = $dom->createElement('substituta');

                $dom->addChild(
                    $substituta,
                    'numero',
                    $documento->infNumeroNfseSubstituta,
                    true,
                    "Número da nota substituta",
                    true
                );

                $dom->addChild(
                    $substituta,
                    'serie',
                    $documento->infSerieNfseSubstituta,
                    true,
                    "Séria da nota substituta",
                    true
                );
                //Adiciona as tags ao DOM
                $nfse->appendChild($substituta);
            }


            //Adiciona as tags ao DOM
            $documentos->appendChild($nfse);
        }
        //Adiciona as tags ao DOM
        $root->appendChild($documentos);


        $body = $dom->saveXML();
        $body = $this->clear($body);
        #echo '<pre>'.print_r($body).'</pre>';die;
        return '<?xml version="1.0" encoding="ISO-8859-1"?>' . $body;
    }
}
