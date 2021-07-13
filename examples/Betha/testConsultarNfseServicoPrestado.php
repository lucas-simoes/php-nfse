<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';

use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFSe\Models\Abrasf\NfseServicoPrestado;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "cmun" => "4118402", //PARANAVAI
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
$contentpfx = file_get_contents('/var/www/sped/sped-nfse/certs/certificado.pfx'); 

try {
    
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
        
    $nsPrestado = new NfseServicoPrestado();
    $nsPrestado->numeroNfse(201200000000123);
    $nsPrestado->prestador(2, 11111111111111, 1234);
    $nsPrestado->tomador(2, 11111111111111, 1234);
    
    $timezone = new \DateTimeZone('America/Sao_Paulo');
    //dataEmissao ou dataCompetencia (passar um ou outro)
    #nsPrestado->dataEmissao(new \DateTime("now", $timezone), new \DateTime("now", $timezone));
    $nsPrestado->dataCompetencia(new \DateTime("now", $timezone), new \DateTime("now", $timezone));
    $nsPrestado->intermediario(2, '99999999999999', '222222', 'Teste');
    $nsPrestado->pagina(1);

    $content = $nfse->tools->consultarNfseServicoPrestado($nsPrestado);
    
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