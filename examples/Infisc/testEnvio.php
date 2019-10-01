<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
require_once '../../bootstrap.php';

use NFePHP\NFSe\Models\Infisc\Rps;
use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapNative;
use NFePHP\NFSe\Models\Infisc\Response;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "999999999999",
    "cpf" => "",
    "im" => "99999999",
    "cmun" => "4305108", //CAXIAS DO SUL
    "siglaUF" => "RS",
    "pathNFSeFiles" => "/dados/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];
$configJson = json_encode($arr);
$contentpfx = file_get_contents('../../certs/certificado.pfx');

try {

    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, '123456'));
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(true);

    //Construção do RPS
    $rps = new Rps();

    $nfse->tools->CNPJ = '999999999999';
    $nfse->tools->dhTrans = date('Y-m-d H:i:s');

    $id = new stdClass();
    $id->cNFSe = '123';
    $id->mod = '98';
    $id->serie = 'S';
    $id->nNFSe = '1';
    $id->dEmi = date('Y-m-d');
    $id->hEmi = date('H:i');
    $id->tpNF = '1';
    $id->refNF = '1';
    $id->tpEmis = 'N';
    $id->ambienteEmi = '2';
    $id->formaEmi = '2';
    $id->empreitadaGlobal = '2';

    $rps->Id = $id;

    //Prestador do serviço
    $prestador = new stdClass();
    $prestador->CNPJ = '08291851000142';
    $prestador->xNome = 'Nome';
    $prestador->IM = 'im';
    $prestador->end = new stdClass();
    $prestador->end->xLgr = 'enderecço 123';
    $prestador->end->xCpl = '';
    $prestador->end->nro = '123';
    $prestador->end->xBairro = 'Bairro';
    $prestador->end->cMun = '4321122';
    $prestador->end->xMun = 'Bairro';
    $prestador->end->UF = 'SC';
    $prestador->end->CEP = '88899000';
    $prestador->end->cPais = '1058';
    $prestador->end->xPais = 'Brasil';
    $prestador->regimeTrib = '1';
    $rps->prest = $prestador;

    //Tomador do serviço
    $tomador = new stdClass();
    $tomador->CNPJ = '08291851000142';
    $tomador->CPF = '03900722900';
    $tomador->xNome = 'Nome';

    $tomador->ender = new stdClass();
    $tomador->ender->xLgr = 'enderecço 123';
    $tomador->ender->xCpl = '';
    $tomador->ender->nro = '123';
    $tomador->ender->xBairro = 'Bairro';
    $tomador->ender->cMun = '4321122';
    $tomador->ender->xMun = 'Bairro';
    $tomador->ender->UF = 'SC';
    $tomador->ender->CEP = '88899000';
    $tomador->ender->cPais = '1058';
    $tomador->ender->xPais = 'Brasul';
    $rps->TomS = $tomador;

    //Empresa transportadora
    $transportadora = new stdClass();
    $transportadora->xNomeTrans = 'transportadora xyz';
    $transportadora->xCpfCnpjTrans = '';
    $transportadora->xInscEstTrans = '';
    $transportadora->xPlacaTrans = 'XYZ1212';
    $transportadora->xEndTrans = '';
    $transportadora->cMunTrans = '4312233';
    $transportadora->xMunTrans = '';
    $transportadora->xUfTrans = '';
    $transportadora->cPaisTrans = '1058';
    $transportadora->xPaisTrans = 'Brasil';
    $transportadora->vTipoFreteTrans = '0';
    $rps->transportadora = $transportadora;

    //Detalhamento dos serviços
    $i = 1;
    while ($i < 3) {
        $det = new stdClass();
        $det->nItem = $i;        

        //Serviço da NFS-e
        $serv = new stdClass();
        $serv->nItem = $i;
        $serv->cServ = '8394';
        $serv->cLCServ = '1602';
        $serv->xServ = 'TRANSPORTE RODOVIARIO DE CARGA, MUNICIPAL (NOVO)';
        $serv->localTributacao = '4305108';
        $serv->localVerifResServ = '1';
        $serv->uTrib = 'UN';
        $serv->qTrib = '1';
        $serv->vUnit = '100.00';
        $serv->vServ = '100.00';
        $serv->vDesc = 0.00;
        $serv->vBCISS = '100.00';
        $serv->pISS = '4.00';
        $serv->vISS = '4.00';
        $serv->vBCINSS = '100.00';
        $serv->pRetINSS = '0.00';
        $serv->vRetINSS = '0.00';
        $serv->nItemPed = '0';
        $serv->vRed = '0.00';
        $serv->vBCRetIR = '0.00';
        $serv->pRetIR = '0.00';
        $serv->vRetIR = '0.00';
        $serv->vBCCOFINS = '0.00';
        $serv->pRetCOFINS = '0.00';
        $serv->vRetCOFINS = '0.00';
        $serv->vBCCSLL = '0.00';
        $serv->pRetCSLL = '0.00';
        $serv->vRetCSLL = '0.00';
        $serv->vBCPISPASEP = '0.00';
        $serv->pRetPISPASEP = '0.00';
        $serv->vRetPISPASEP = '0.00';
        $serv->totalAproxTribServ = '0.00';
        $rps->serv[$i] = $serv;
        $rps->det[$i] = $det;
        $i++;
    }

    //Totais
    $total = new stdClass();
    $total->vServ = '100.00';
    $total->vRedBCCivil = '0.00';
    $total->vDesc = '0.00';
    $total->vtNF = '100.00';
    $total->vtLiq = '100.00';
    $rps->total = $total;

    $ISS = new stdClass();
    $ISS->vBCISS = '100.00';
    $ISS->vISS = '4.00';
    $rps->ISS = $ISS;
    
    //Informações Adicionais
    $rps->infAdicLT = '4321122';
    $rps->infAdic[] = 'Informação 1';
    $rps->infAdic[] = 'Informação 2';

    $nfse->rps = $rps;

    $content = \NFePHP\NFSe\Models\Infisc\RenderRPS::toXml($rps);
//    //echo "<pre>";
//    header('Content-type: text/xml; charset=UTF-8');
//    print_r($content);
//    exit();
//    
    //envio do RPS
    $response = $nfse->tools->envioLote([$nfse->rps]);

    //Converte em objeto
    $return = $nfse->response->readReturn('return', $response);

    //Lote recebido    
    if ($return->confirmaLote->sit == 100) {
        echo "Lote: " . $return->confirmaLote->cLote . "<br/>";
        echo "Situação: " . $return->confirmaLote->sit . "<br/>";
        echo "CNPJ: " . $return->confirmaLote->CNPJ . "<br/>";
        echo "Data: " . $return->confirmaLote->dhRecbto . "<br/>";
    }
    //echo "<pre>";
    //header('Content-type: text/xml; charset=UTF-8');
    //print_r($response);
    exit();
} catch (\NFePHP\Common\Exception\SoapException $e) {
    echo $e->getMessage();
} catch (NFePHP\Common\Exception\CertificateException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}    
