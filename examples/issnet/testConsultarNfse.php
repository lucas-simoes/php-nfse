<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';

use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapNative;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 1,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "cmun" => "5103403", //CUIABA
    "siglaUF" => "MT",
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
    $nfse = new NFSe($config, Certificate::readPfx($contentpfx, 'senha'));
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
        
    $numeroNFSe = '';    
    $dtInicio = '2016-10-01';
    $dtFim = '2016-10-06';
    $tomador = []; //['tipo' => 2, 'doc' => '11111111111111', 'im' => '55555555'];
    $intermediario = []; //['tipo' => 2, 'doc' => '11111111111111', 'im' => '44444444', 'razao' => 'SEI LA LTDA-ME'];
    $content = $nfse->tools->consultarNfse($numeroNFSe, $dtInicio, $dtFim, $tomador, $intermediario);    
    
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