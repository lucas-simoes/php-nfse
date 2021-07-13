<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';


use NFePHP\Common\Certificate;
use NFePHP\NFSe\NFSe;
use NFePHP\NFSe\Models\IPM\SoapCurl;
use NFePHP\NFSe\Models\IPM\CancelarRps;


$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "ie" => "23445",
    "cmun" => "4105805", //COLOMBO
    "siglaUF" => "PR",
    "cod_tom_municipio" => "7513", //importante para uso das operacoes IPM
    "teste" => 1, #defini a operacao como teste
    "trabalha_com_rps" => 1, //define se a prefeitura trabalha com rps
    "login" => 'usuario@user.com.br', //usuario e senha para autenticacao 
    "senha" => 'senha',
    "pathNFSeFiles" => "/dados/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]    
];
$configJson = json_encode($arr);
$contentpfx = file_get_contents(__DIR__ . '/../../tests/fixtures/certs/certificado_teste.pfx');

try {
    
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    //definir as informacoes para cancelamento da nota
    $rps = new CancelarRps();
    $rps->cpfCnpjPrestador('99999999999999');
    $rps->numeroNfse(2);
    $rps->observacao('TESTE DE CANCELAMEMTO');
    $rps->situacao(CancelarRps::CANCELAR);
    
    //envio do RPS
    $response = $nfse->tools->cancelarNota($rps);
    
    //apresentação do retorno
    header("Content-type: text/xml");
    echo $response;
    
} catch (\NFePHP\Common\Exception\SoapException $e) {
    echo $e->getMessage();
} catch (NFePHP\Common\Exception\CertificateException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}    
