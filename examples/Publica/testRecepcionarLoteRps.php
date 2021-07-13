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
$contentpfx = file_get_contents('/var/www/sped/sped-nfse/certs/certificado.pfx'); 

try {
    
    #permite utilizar tanto para prefeituras que exigem ou nao certificados
    #$nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
     
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
        
    $timezone = new \DateTimeZone('America/Sao_Paulo');
    $rps->dataEmissao(new \DateTime("now", $timezone));
    $rps->competencia('2020','04');
    $rps->naturezaOperacao(Rps::NATUREZA_SUSPENSA_JUS);
    $rps->optanteSimplesNacional(Rps::SIM);
    $rps->incentivadorCultural(Rps::NAO);
    $rps->status(Rps::SIM);
    $rps->valorServicos(1321.50);
    $rps->valorDeducoes(5.0000);
    $rps->valorPis(0.00);
    $rps->valorCofins(0.00);
    $rps->valorInss(0.00);
    $rps->valorIr(0.00);
    $rps->valorCsll(0.00);
    $rps->issRetido(Rps::SIM);
    $rps->valorIss(0.00);
    $rps->outrasRetencoes(0.00);
    $rps->baseCalculo(2.00);
    $rps->aliquota(3.00);
    $rps->valorLiquidoNfse(0.00);
    $rps->valorIssRetido(0.00);
    $rps->descontoCondicionado(0.00);
    $rps->descontoIncondicionado(0.00);

    #$rps->issConstrucaoCivil(1,'99999999999999', 300.67, '3884848ujuguhsu94949499');
    $rps->itemListaServico(1401);

    
    #$rps->discriminacao('teste'); //ou
    $rps->addItemDiscriminacao('descricao',102,0.03,5,4.50);
    $rps->addItemDiscriminacao('descricao2',102,0.03,5,4.50);
    $rps->informacoesComplementares('informacao complementar');
    $rps->codigoMunicipio(12344); #referente ao local do
    $rps->codigoPais(3);

    //intermediario do servico
    $rps->intermediario(Rps::CPF,'11111111111',3445, 'Razao social do intermediario');

    //add as parcelas das condicoes de pagamentos
    $rps->addParcela(1, 1, 10.54, new \DateTime("now", $timezone));
    $rps->addParcela(1, 2, 10.54, new \DateTime("now", $timezone));

    //informacoes de construcao civil
    #$rps->construcaoCivil(234, 'Art da obra'); #CAÇADOR NAO ACEITA CONSTRUCAO CIVIL

    #$rps->retificacao(1,'23',Rps::TIPO_RPS, 'retificar a nfse'); #CAÇADOR NAO ACEITA RETIFICACAO

    $lote = 1;
    //envio do RPS
    $response = $nfse->tools->recepcionarLoteRps($lote, [$rps]);
    
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
