<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';


use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapNative;
use NFePHP\NFSe\Models\Prodam\Response;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 1,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "cmun" => "3550308",
    "siglaUF" => "SP",
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
    //Aqui podemos escolher entre usar o SOAP nativo ou o cURL,
    //em ambos os casos os comandos são os mesmos pois observam
    //a mesma interface
    $nfse->tools->loadSoapClass(new SoapNative());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
   
    $nfse->rps->versao(1);
    $nfse->rps->prestador('99999999');
    $nfse->rps->status('N');
    $nfse->rps->tipo('RPS');
    $nfse->rps->numero('100');
    $nfse->rps->serie(1);
    $nfse->rps->data('2016-10-29');
    $nfse->rps->tomador('SEU CLIENTE LTDA', 2, '99999999999911', '', '8888888', 'cliente@dominio.com');
    $nfse->rps->tomadorEndereco('RUA', 'IRARI', '001', 'SALA 22', 'CENTRO', '3550308', 'SP', '01111000');
    $nfse->rps->municipioPrestacao('3550308');
    $nfse->rps->codigoServico('7285');
    $nfse->rps->discriminacao('Teste de Emissao de NFSe');
    $nfse->rps->valorServicos(1.00);
    $nfse->rps->aliquotaServico(0.05);
    
    //$nfse->rps->cargaTributaria($valor, $percentual, $fonte);
    
    $nfse->rps->tributacao('T');
    $nfse->rps->issRetido(false);
    //$nfse->rps->valorPIS(0.00);
    //$nfse->rps->valorCOFINS(0.00);
    //$nfse->rps->valorCSLL(0.00);
    $nfse->rps->valorDeducoes(0.00);
    //$nfse->rps->valorINSS(0.00);
    
    //$nfse->rps->intermediario($tipo, $cnpj, $im, $email);
    //$nfse->rps->codigoCEI($cod);
    //$nfse->rps->matriculaObra($matricula);
        
    //echo "<pre>";
    //print_r($nfse->rps);
    //echo "</pre>";
        
    $response = $nfse->tools->testeEnvioLoteRPS([$nfse->rps]);
    file_put_contents('/tmp/rettestEnvioLoteRPS.xml',$response);
    $response = $nfse->response->readReturn('RetornoXML', $response);
    echo "<pre>";
    print_r($response);
    echo "</pre>";

} catch (\NFePHP\Common\Exception\SoapException $e) {
    echo $e->getMessage();
} catch (NFePHP\Common\Exception\CertificateException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
