<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\Simpliss\SoapCurl;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "4796831",
    "cmun" => "3549102", //SAO JOAO DA BOA VISTA
    "siglaUF" => "PR",
    "senha" => "senha",
    "pathNFSeFiles" => "/dados/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]    
];
$configJson = json_encode($arr);
$contentpfx = file_get_contents('/var/www/sped/sped-nfse/certs/certificado.pfx');

try {
    //com os dados do config e do certificado já obtidos e desconvertidos
    //a sua forma original e só passa-los para a classe 
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    $content = $nfse->tools->cancelarNfse('12345', '5');
    
    header("Content-type: text/xml");
    echo $content;
    
    //echo "<pre>";
    //print_r($response);
    //echo "</pre>";
    
} catch (\NFePHP\Common\Exception\SoapException $e) {
    echo $e->getMessage();
} catch (NFePHP\Common\Exception\CertificateException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}