<?php

namespace NFePHP\NFSe\Tests;

use PHPUnit\Framework\TestCase;

class NFSeTestCase extends TestCase
{
    public $fixturesPath = '';
    public $configJson = '';
    public $contentpfx = '';
    public $passwordpfx = '';

    public function __construct()
    {
        $this->fixturesPath = dirname(__FILE__) . '/fixtures/';
        $config = [
            "atualizacao" => "2016-08-03 18:01:21",
            "tpAmb" => 2,
            "versao" => 1,
            "razaosocial" => "Sua empresa ltda",
            "cnpj" => "99999090910270",
            "cpf" => "",
            "im" => "39111111",
            "cmun" => "3550308",
            "siglaUF" => "SP",
            "pathNFSeFiles" => "/tmp/nfse",
            "aProxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];
        $this->contentpfx = file_get_contents($this->fixturesPath . "certs/certificado_teste.pfx");
        $this->passwordpfx = "associacao";
        $this->configJson = json_encode($config);
    }
}
