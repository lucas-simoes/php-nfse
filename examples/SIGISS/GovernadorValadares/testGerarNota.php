<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use NFePHP\NFSe\NFSe;
use NFePHP\NFSe\Counties\M3127701\Rps;
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

    $nfse = new NFSe($configJson);
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    //Construção do RPS
    $rps = new Rps();
    $rps->prestador(223444,'11111111111111', '11111111111', 'senha');
    $rps->tomador(RPS::TOMADORPJMUNICIPIO, '11111111111111', '88855', '77885', 'JONAS AVENTURA','5530335740','','','jonas@email.com');
    $rps->tomadorEndereco(
        'Rua 12',
        '1234',
        'casa 2',
        'Centro',
        '4113700',
        'MT',
        '78088408'
    );
    //$rps->intermediario($rsp::CNPJ, '99999999999999', '222222', 'Teste');
    $rps->numero(2);
    $rps->serie('8');
    $rps->tipo($rps::TIPO_RPS);
    
    $timezone = new \DateTimeZone('America/Sao_Paulo');
    $rps->dataEmissao(new \DateTime("now", $timezone));
    $rps->municipioPrestacaoServico('999'); //999 em ambiente de produção   
    $rps->paisPrestacaoServico('999'); //999 em ambiente de produção   
    $rps->municipioIncidencia('999'); //999 em ambiente de produção    
    $rps->servico('702');
    $rps->situacao(Rps::SIT_TRIBUTADA_PRESTADOR);
    $rps->descricaoNF('TESTE ### Valor Aproximado dos Tributos: R$ 0,17');
    #$rps->rpsSubstituido('5555', '23');
    $rps->retencaoIss(2.400);
    $rps->aliquota(5.0000);
    $rps->valor(1321.50);
    
    //(Valor dos serviços - Valor das deduções - descontos incondicionados)
    $rps->base(1321.50);
    $rps->pis(0.00);
    $rps->cofins(0.00);
    $rps->csll(0.00);
    $rps->inss(0.00);
    $rps->ir(0.00);

    #$rps->construcaoCivil('1234', '234-4647-aa','455', '2020', '123', 'Quadra 01', 'Centro');
    
    //envio do RPS
    $response = $nfse->tools->gerarNota($rps);
    
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
