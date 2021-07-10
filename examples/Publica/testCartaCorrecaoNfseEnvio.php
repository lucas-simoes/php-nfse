<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';


use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\Publica\Rps;
use NFePHP\NFSe\Models\Publica\SoapCurl;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "ie" => "23445",
    "cmun" => "4203006", //CACADOR
    "siglaUF" => "SC",
    "pathNFSeFiles" => "/dados/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]    
];

$configJson = json_encode($arr);
$contentpfx = file_get_contents('/var/www/html/arquivos/Certificados/certs/23096.pfx');

try {
    
    #permite utilizar tanto para prefeituras que exigem ou nao certificados
    #$nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'neoqeav'));
     
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    //Construção do RPS
    $rps = new Rps();
    $rps->prestador('99999999999999', '99999999');
    $rps->tomador(Rps::CNPJ,'11111111111111','TOMADOR TESTE', '999999999', 'teste@teste.com');
    $rps->tomadorEndereco(
        'Rua 12',
        '1234',
        'casa 2',
        'Centro',
        789955,
        'PR',
        '78088408'
    );

    $rps->numero(2);
    $rps->serie(1);
    $rps->tipo(RPS::TIPO_RPS);
    
    #$rps->discriminacao('teste'); //ou
    $rps->addItemDiscriminacao('descricao',102,0.03,5,4.50);
    $rps->addItemDiscriminacao('descricao2',102,0.03,5,4.50);
    
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
