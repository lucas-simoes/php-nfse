<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';

//as classes abaixo serão sempre usadas e serão instanciadas
use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapNative;

//Para cada Prefeitura, o que identifica as classe a serem usadas é o numero 
//cmun indicado no config. A partir desse numero as classes especificas serão
//localizadas e carregadas.

//As classes estão separadas em blocos:
//  Na pasta Counties/ estão as classes para cada municipio, que extendem as classes
//  de cada modelo, que por sua vez estão na pasta Models/ e que extendem as classes
//  básicas que estão na pasta Common/

//Cada Prefeitura irá fornecer 4 classes básicas para o uso
// 1 - Rps::class classe para carregar os dados de um Rps (cada modelo usado 
//     possue dados e regras diferentes para os RPS)
// 2 - Convert::class classe para transformar dados TXT estruturados em um ou 
//     mais Rps::class (referente ao modelo utilizado)
// 3 - Tools::class classe que realiza a comunicação com os webservices
//     (lembrando novamente que modelos diferentes tem métodos diferentes)
// 4 - Response::class classe que converte os retornos xml em stdClass para facilitar
//     a extração dos dados, neste ponto deve ficar claro tambem que esses retornos 
//     são muito diferentes a conforme o modelo sendo usado pela Prefeitura

//ATENÇÃO : cada modelo diferente possuirá métodos com nomes e 
//          parametros diferentes!!!  

//NOTA: Por ora, não serão automaticamente salvos NENHUM arquivo em disco, 
//apenas os certificados serão salvos e de forma temporária no momento do uso, 
//pelas classes SOAP, pois as mesmas não permitem o uso em memoria e em seguida 
//esses arquivos serão removidos. Como os nomes desses arquivos são gerados de
//forma aleatória não haverão conflitos.

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário 
///trabalhar com arquivos, este script abaixo serve apenas como 
//exemplo durante a fase de desenvolvimento e testes.
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
    "pathNFSeFiles" => "\/dados\/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]    
];
$configJson = json_encode($arr);

//esse certificado pode estar em uma base de dados e para isso não esqueça 
//de converter para base64 ao gravar na base e desconverter para pode usar,
//esses dados também podem ser compactados usando o gunzip para diminuir o 
//seu tamanho
$contentpfx = file_get_contents('/var/www/sped/sped-nfse/certs/certificado.pfx');

try {
    //com os dados do config e do certificado já obtidos e descompactado 
    //e desconvertido para a sua forma original é só passa-los para a 
    //classe principal. A classe principal usa o config para localizar as 
    //classes especificas de cada municipio
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'senha'));
    
    //Aqui podemos escolher entre usar o SOAP nativo ou o cURL,
    //em ambos os casos os comandos são os mesmos pois observam
    //a mesma interface
    $nfse->tools->loadSoapClass(new SoapNative());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    //abaixo esta a chamada do método que consulta as informações do lote
    //enviado por um emitente autorizado, os dados devem ser fornecidos 
    //sempre no formato indicado, o metodo não irá validar os dados fornecidos!!
    $im = '99999999';
    $lote = '20101'; //este numero de lote é fornecido na resposta do envio de lote
                     //pelo sistema da prefeitura NÃO É UM NUMERO GERADO PELO EMITENTE
    $response = $nfse->tools->consultaInformacoesLote($im, $lote);
    
    //esse XML retornado na resposta SOAP poderá ser convertido, de assim for desejado, em uma stdClass 
    //para facilitar a extração dos dados para uso da aplicação. Para isso 
    //usamos a classe Response::readReturn($tag, $response) passando 
    //o nome da tag desejada, e o xml. Lembrando que o nome da TAG desejada irá 
    //variar de modelo para modelo
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