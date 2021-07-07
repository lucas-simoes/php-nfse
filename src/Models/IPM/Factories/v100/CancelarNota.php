<?php

namespace NFePHP\NFSe\Models\IPM\Factories\v100;

use Exception;
use InvalidArgumentException;
use stdClass;
use NFePHP\NFSe\Models\IPM\CancelarRps;
use NFePHP\NFSe\Common\DOMImproved as Dom;
use NFePHP\NFSe\Models\IPM\Factories\Signer;
use NFePHP\NFSe\Models\IPM\Factories\Factory;

class CancelarNota extends Factory
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
        //Cria o elemento nfse
        $root = $dom->createElement('nfse');
        if ($this->certificate) {
            $root->setAttribute('id', 'nota');
        }

        //Adiciona as tags ao DOM
        $dom->appendChild($root);

        if($config->teste) {
            $dom->addChild(
                $root,
                'nfse_teste',
                1,
                true,
                "Definir como teste de integração",
                true
            );
        }

        //Cria o elemento nf
        $nf = $dom->createElement('nf');

        $dom->addChild(
            $nf,
            'numero',
            $rps->infNumeroNfse,
            true,
            "Número da nota fiscal",
            true
        );

        $dom->addChild(
            $nf,
            'situacao',
            $rps->infSituacao,
            true,
            "Status para cancelamento da nota. Deve ser preenchido como C",
            true
        );

        $dom->addChild(
            $nf,
            'observacao',
            $rps->infObservacao,
            true,
            "Motivo do cancelamento da NFS-e",
            true
        );

        //Adiciona as tags ao DOM
        $root->appendChild($nf);

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

        $body = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());

        #Se prefeitura trabalhar com assinatutura deve ser passado o certificado
        if ($this->certificate) {

            $body = Signer::sign(
                $this->certificate,
                $body,
                'nfse',
                'id',
                $this->algorithm,
                [false, false, null, null],
                '',
                true
            );
        }

        $body = $this->clear($body);        
        return '<?xml version="1.0" encoding="ISO-8859-1"?>' . $body;
    }
}
