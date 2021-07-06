<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';


use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\IPM\ItensRps;
use NFePHP\NFSe\NFSeSemCertif;
use NFePHP\NFSe\Models\IPM\Rps;
use NFePHP\NFSe\Models\IPM\SoapCurl;


$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "teste" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "",
    "im" => "99999999",
    "ie" => "23445",
    "cod_tom_municipio" => "7513",
    "cmun" => "4105805", //COLOMBO
    "siglaUF" => "PR",
    "trabalha_com_rps" => 1,
    "login" => 'usuario@user.com.br',
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

try {

    $nfse = new NFSeSemCertif($configJson);
     
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    //Construção do RPS
    $rps = new Rps();
    $rps->tomador(RPS::TOMADORES, '11111111111111','78899999','TOMADOR TESTE', 'TESTE SA', 'teste@teste.com');
    $rps->tomadorEstrangeiro(78855, 'California', 'Estados Unidos');
    $rps->tomadorTelefone('041','99999999','041','999999999');
    $rps->tomadorEndereco(
        Rps::SIM,
        'Rua 12',
        '1234',
        'casa 2',
        'Alameda dos Anjos',
        'Centro',
        3700,
        '78088408'
    );

    $rps->numero(2);
    $rps->serie(1);
    $rps->pedagio('23434');
        
    $timezone = new \DateTimeZone('America/Sao_Paulo');
    $rps->dataEmissao(new \DateTime("now", $timezone));
    $rps->dataFatoGerador(new \DateTime("now", $timezone));
    $rps->valorTotal(1321.50);
    $rps->valorDesconto(5.0000);
    $rps->valorIr(0.00);
    $rps->valorInss(0.00);
    $rps->valorContribuicaoSocial(0.00);
    $rps->valorRps(0.00);
    $rps->valorPis(0.00);
    $rps->valorCofins(0.00);

    $rps->observacao('TESTE ### Valor Aproximado dos Tributos: R$ 0,17');

    $itemNfse = new ItensRps();
    $itemNfse->tributaMunicipioPrestador(Rps::NAO);
    $itemNfse->codigoItemListaServico(456);
    $itemNfse->unidadeCodigo(545);
    $itemNfse->unidadeQuantidade(45.45);
    $itemNfse->unidadeValorUnitario(6.45);
    $itemNfse->codigoItemListaServico(878);
    $itemNfse->descritivo('Nota fiscal sobre uso de sistema');
    $itemNfse->aliquotaItemListaServico(0.5);
    $itemNfse->situacaoTributaria(789);
    $itemNfse->valorTributavel(78.33);
    $itemNfse->valorDeducao(0.00);
    $itemNfse->valorIssrf(0.00);

    $rps->addItens($itemNfse);

    $rps->addLinhaGenerico('TITULO DO CONTEÚDO HTML', 'DESCRICAO HTML', '<table width="875" border="0" cellpadding="0" cellspacing="0"> <tr> <td width="99">C&oacute;digo</td> <td width="454">Descri&ccedil;&atilde;o</td> <td width="98" align="right">Valor Unit&aacute;rio</td> <td width="87" align="right">Quantidade</td> <td width="127" align="right">Valor Total</td> </tr> <tr> <td width="99">123456</td> <td width="454">CONTEUDO 1</td> <td width="98" align="right">25,00</td> <td width="87" align="right">3</td> <td width="127" align="right">75,00</td> </tr> <tr> <td width="99">123457</td> <td width="454">CONTEUDO 2</td> <td width="98" align="right">25,00</td> <td width="87" align="right">1</td> <td width="127" align="right">25,00</td> </tr> </table> </descricao>');

    $rps->produtos('Produtos testes', 300.00);

    $rps->formaPagamento(Rps::CARTAODEBITO);
    $rps->addParcela(1, 45.00, new \DateTime("2021-01-01", $timezone));
    $rps->addParcela(2, 45.00, new \DateTime("2021-02-01", $timezone));
    $rps->addParcela(3, 45.00, new \DateTime("2021-03-01", $timezone));
    $rps->addParcela(4, 45.00, new \DateTime("2021-04-01", $timezone));
    
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
