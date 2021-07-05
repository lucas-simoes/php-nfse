<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use NFePHP\NFSe\NFSeSemCertif;
use NFePHP\NFSe\Counties\M4113700\Rps;
use NFePHP\NFSe\Models\SIGISS\SoapCurl;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "cmun" => "4113700", //LONDRINA
    "siglaUF" => "PR",
    "pathNFSeFiles" => "/dados/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]    
];
$configJson = json_encode($arr);

try {
    //com os dados do config e do certificado já obtidos e desconvertidos
    //a sua forma original e só passa-los para a classe 
    $nfse = new NFSeSemCertif($configJson);
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    //Construção do RPS
    $rps = new Rps();
    $rps->prestador(223444,'11111111111111', '11111111111', 'senha');
    $rps->numero(1223);

    $timezone = new \DateTimeZone('America/Sao_Paulo');
    $rps->dataEmissao(new \DateTime("now", $timezone));    

    $content = $nfse->tools->consultarRps($rps);
    
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