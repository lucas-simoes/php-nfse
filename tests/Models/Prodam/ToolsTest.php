<?php

namespace NFePHP\NFSe\Tests\Models\Prodam;

use NFePHP\NFSe\Tests\NFSeTestCase;
use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;

class ToolsTest extends NFSeTestCase
{
    public $nfse;
    public $dummySoap;
    
    public function __construct()
    {
        parent::__construct();
        $certificate = Certificate::readPfx($this->contentpfx, $this->passwordpfx);
        $this->nfse = new NFSe($this->configJson, $certificate);
        $this->dummySoap = $this->getMockBuilder('\NFePHP\Common\Soap\SoapCurl')
            ->setMethods(['send'])    
            ->getMock();
        $this->dummySoap->disableCertValidation(true);
    }
    
    public function testEnvioRPS()
    {
        $expected = '';
        //$this->dummySoap->method('send')->willReturn($expected);
        //$this->nfse->tools->loadSoapClass($this->dummySoap);
        //$rps = $this->nfse->rps->
        //$actual = $this->nfse->tools->envioRPS($rps);
        $this->assertTrue(true);
    }
    
    public function testEnvioLoteRPS()
    {
        $expected = '';
        //$this->dummySoap->method('send')->willReturn($expected);
        //$this->nfse->tools->loadSoapClass($this->dummySoap);
        //$rpss[] = $this->nfse->rps->
        //$rpss[] = $this->nfse->rps->
        //$actual = $this->nfse->tools->envioLoteRPS($rpss);
        $this->assertTrue(true);
    }
    
    public function testTesteEnvioLoteRPS()
    {
        $expected = '';
        //$this->dummySoap->method('send')->willReturn($expected);
        //$this->nfse->tools->loadSoapClass($this->dummySoap);
        //$rpss[] = $this->nfse->rps->
        //$rpss[] = $this->nfse->rps->
        //$actual = $this->nfse->tools->testeEnvioLoteRPS($rpss);
        $this->assertTrue(true);
    }
    
    public function testCancelamentoNFSe()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_retornoCancelamentoNFe.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $prestadorIM = '11111111';
        $numeroNFSe = '9999999999';
        $actual = $this->nfse->tools->cancelamentoNFSe($prestadorIM, $numeroNFSe);
        $this->assertEquals($expected, $actual);
    }
    
    public function testConsultaNFSeEmitidas()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_consultaNFSeEmitidas.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $cnpj = '08894935000170';
        $cpf = '';
        $im = '36443573';
        $dtInicial = '2016-08-01';
        $dtFinal = '2016-09-01';
        $pagina = 1;
        $actual = $this->nfse->tools->consultaNFSeEmitidas($cnpj, $cpf, $im, $dtInicial, $dtFinal, $pagina);
        $this->assertEquals($expected, $actual);
    }
    
    public function testConsultaNFSeRecebidas()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_consultaNFSeRecebidas.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $cnpj = '08894935000170';
        $cpf = '';
        $im = '36443573';
        $dtInicial = '2016-07-01';
        $dtFinal = '2016-07-31';
        $pagina = 1;
        $actual = $this->nfse->tools->consultaNFSeEmitidas($cnpj, $cpf, $im, $dtInicial, $dtFinal, $pagina);
        $this->assertEquals($expected, $actual);
    }
    
    public function testConsultaInformacoesLote()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_consultaInformacoesLoteErro.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $im = '36443573';
        $lote = '1';
        $actual = $this->nfse->tools->consultaInformacoesLote($im, $lote);
        $this->assertEquals($expected, $actual);
    }
    
    public function testConsultaLote()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_consultaLoteErro.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $lote = '1';
        $actual = $this->nfse->tools->consultaLote($lote);
        $this->assertEquals($expected, $actual);
    }
    
    public function testConsultaNFSe()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_consultaNFSe.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $im = '36443573';
        $actual = $this->nfse->tools->consultaNFSe(
            [0=>['prestadorIM'=>$im,'numeroNFSe'=>'577']],
            [0=>['prestadorIM'=>$im,'serieRPS'=>'1', 'numeroRPS'=>'12']]
        );
        $this->assertEquals($expected, $actual);
    }
    
    public function testConsultaCNPJ()
    {
        $expected = file_get_contents($this->fixturesPath."Prodam/response_consultaCnpj.xml");
        $this->dummySoap->method('send')->willReturn($expected);
        $this->nfse->tools->loadSoapClass($this->dummySoap);
        $actual = $this->nfse->tools->consultaCNPJ('08894935000170');
        $this->assertEquals($expected, $actual);
    }
}
