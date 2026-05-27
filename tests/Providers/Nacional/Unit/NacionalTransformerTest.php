<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Tests\Providers\Nacional\Unit;

use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;
use NFePHP\NFSe\Providers\Nacional\Models\CodigoServico;
use NFePHP\NFSe\Providers\Nacional\Models\ComplementoServico;
use NFePHP\NFSe\Providers\Nacional\Models\Dps;
use NFePHP\NFSe\Providers\Nacional\Models\Emitente;
use NFePHP\NFSe\Providers\Nacional\Models\Endereco;
use NFePHP\NFSe\Providers\Nacional\Models\LocalPrestacao;
use NFePHP\NFSe\Providers\Nacional\Models\RegimeTributario;
use NFePHP\NFSe\Providers\Nacional\Models\Servico;
use NFePHP\NFSe\Providers\Nacional\Models\Tomador;
use NFePHP\NFSe\Providers\Nacional\Models\TotalMaior;
use NFePHP\NFSe\Providers\Nacional\Models\TotalTributos;
use NFePHP\NFSe\Providers\Nacional\Models\Tributacao;
use NFePHP\NFSe\Providers\Nacional\Models\TributacaoMunicipal;
use NFePHP\NFSe\Providers\Nacional\Models\ValorServico;
use NFePHP\NFSe\Providers\Nacional\Models\Valores;
use NFePHP\NFSe\Providers\Nacional\NacionalTransformer;
use PHPUnit\Framework\TestCase;

/**
 * T020 — NacionalTransformerTest
 *
 * Verifica que NacionalTransformer::transform(Dps $dps) gera um array com a
 * estrutura infDPS contendo todos os blocos (emit, tomador, serv, valores)
 * conforme contracts/api-nacional.md e que o output corresponde ao payload
 * de dps-valida.json.
 *
 * RED FIRST: todos os testes aqui falham até T022 ser implementado, pois
 * NacionalTransformer::transform() lança BadMethodCallException.
 */
class NacionalTransformerTest extends TestCase
{
    private NacionalTransformer $transformer;
    private Dps $dps;

    protected function setUp(): void
    {
        $this->transformer = new NacionalTransformer();

        $emitente = new Emitente(
            cnpj: '12345678000195',
            inscricaoMunicipal: '000123',
            codigoRegimeTributario: 1,
            regimeTributario: new RegimeTributario(
                opcaoSimplesNacional: 1,
                cnae: '6201501',
                codigoLocalEmissao: '3550308',
            ),
        );

        $tomador = new Tomador(
            cnpj: '98765432000100',
            cpf: null,
            nifEstrangeiro: null,
            inscricaoMunicipal: null,
            endereco: new Endereco(
                logradouro: 'Rua das Flores',
                numero: '123',
                complemento: null,
                bairro: 'Centro',
                codigoMunicipio: '3550308',
                uf: 'SP',
                cep: '01310100',
                nomePais: 'BRASIL',
                codigoPais: '1058',
            ),
        );

        $servico = new Servico(
            codigoServico: new CodigoServico(
                codigoTributacaoNacional: '010700',
                codigoTributacaoMunicipal: '12.03',
                cnae: '6201501',
                descricaoServico: 'Desenvolvimento de sistemas de informação',
            ),
            complemento: new ComplementoServico(
                textoComplemento: 'Sistema de gestão empresarial',
                codigoIncentivoBeneficio: null,
            ),
            localPrestacao: new LocalPrestacao(
                codigoLocalPrestacao: '3550308',
                codigoPais: '1058',
            ),
        );

        $valores = Valores::builder()
            ->valorServicoPrestado(new ValorServico(
                valorRecebido: '1000.00',
                valorDesconto: '0.00',
            ))
            ->totalMaior(new TotalMaior(
                valorLiquido: '1000.00',
                valorCargaTributaria: '50.00',
                percentualCargaTributaria: '0.0500',
            ))
            ->tributacao(new Tributacao(
                tributacaoMunicipal: new TributacaoMunicipal(
                    tributacaoIssqn: 1,
                    codigoLocalIncidencia: '3550308',
                    aliquota: '0.0500',
                    tipoRetencaoBM: 1,
                ),
                tributacaoFederal: null,
                totalTributos: new TotalTributos(
                    percentualTotalTributos: '0.0500',
                    valorTotalTributos: '50.00',
                    indicadorTotalTributos: 1,
                ),
            ))
            ->build();

        $this->dps = Dps::builder()
            ->id('DPS12345678000195000123202605230000000001')
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->dataEmissao(new \DateTimeImmutable('2026-05-23T10:00:00-03:00'))
            ->competencia('2026-05')
            ->versaoAplicacao('1.00')
            ->emitente($emitente)
            ->tomador($tomador)
            ->servico($servico)
            ->valores($valores)
            ->build();
    }

    // -----------------------------------------------------------------------
    // Tests: estrutura raiz infDPS
    // -----------------------------------------------------------------------

    public function testTransformReturnsArrayWithInfDpsKey(): void
    {
        $result = $this->transformer->transform($this->dps);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('infDPS', $result);
    }

    public function testInfDpsContainsTopLevelFields(): void
    {
        $infDps = $this->transformer->transform($this->dps)['infDPS'];

        $this->assertSame('DPS12345678000195000123202605230000000001', $infDps['Id']);
        $this->assertSame(ConfiguracaoNacional::HOMOLOGACAO, $infDps['tpAmb']);
        $this->assertSame('1.00', $infDps['verAplic']);
        $this->assertSame('2026-05', $infDps['dCompet']);
        $this->assertStringContainsString('2026-05-23', $infDps['dhEmi']);
    }

    // -----------------------------------------------------------------------
    // Tests: bloco emit
    // -----------------------------------------------------------------------

    public function testInfDpsContainsEmitBlock(): void
    {
        $infDps = $this->transformer->transform($this->dps)['infDPS'];

        $this->assertArrayHasKey('emit', $infDps);
        $emit = $infDps['emit'];

        $this->assertSame('12345678000195', $emit['CNPJ']);
        $this->assertSame('000123', $emit['IM']);
        $this->assertSame(1, $emit['CRT']);
    }

    public function testEmitContainsRegTribBlock(): void
    {
        $emit = $this->transformer->transform($this->dps)['infDPS']['emit'];

        $this->assertArrayHasKey('regTrib', $emit);
        $regTrib = $emit['regTrib'];

        $this->assertSame(1, $regTrib['opSimpNac']);
        $this->assertSame('6201501', $regTrib['CNAE']);
        $this->assertSame('3550308', $regTrib['cLocEmi']);
    }

    // -----------------------------------------------------------------------
    // Tests: bloco tomador
    // -----------------------------------------------------------------------

    public function testInfDpsContainsTomadorBlock(): void
    {
        $infDps = $this->transformer->transform($this->dps)['infDPS'];

        $this->assertArrayHasKey('tomador', $infDps);
        $tomador = $infDps['tomador'];

        $this->assertSame('98765432000100', $tomador['CNPJ']);
        $this->assertArrayNotHasKey('CPF', $tomador);
    }

    public function testTomadorContainsEnderTomadorBlock(): void
    {
        $tomador = $this->transformer->transform($this->dps)['infDPS']['tomador'];

        $this->assertArrayHasKey('enderTomador', $tomador);
        $end = $tomador['enderTomador'];

        $this->assertSame('Rua das Flores', $end['xLgr']);
        $this->assertSame('123', $end['nro']);
        $this->assertSame('Centro', $end['xBairro']);
        $this->assertSame('3550308', $end['cMun']);
        $this->assertSame('SP', $end['UF']);
        $this->assertSame('01310100', $end['CEP']);
        $this->assertSame('BRASIL', $end['xPais']);
        $this->assertSame('1058', $end['cPais']);
    }

    // -----------------------------------------------------------------------
    // Tests: bloco serv
    // -----------------------------------------------------------------------

    public function testInfDpsContainsServBlock(): void
    {
        $infDps = $this->transformer->transform($this->dps)['infDPS'];

        $this->assertArrayHasKey('serv', $infDps);
        $serv = $infDps['serv'];

        $this->assertArrayHasKey('cServ', $serv);
        $this->assertArrayHasKey('compl', $serv);
        $this->assertArrayHasKey('loc', $serv);
    }

    public function testServCServContainsCorrectFields(): void
    {
        $cServ = $this->transformer->transform($this->dps)['infDPS']['serv']['cServ'];

        $this->assertSame('010700', $cServ['cTribNac']);
        $this->assertSame('12.03', $cServ['cTribMun']);
        $this->assertSame('6201501', $cServ['CNAE']);
        $this->assertSame('Desenvolvimento de sistemas de informação', $cServ['xDescServ']);
    }

    public function testServComplContainsTextoComplemento(): void
    {
        $compl = $this->transformer->transform($this->dps)['infDPS']['serv']['compl'];

        $this->assertSame('Sistema de gestão empresarial', $compl['xCompl']);
    }

    public function testServLocContainsCorrectFields(): void
    {
        $loc = $this->transformer->transform($this->dps)['infDPS']['serv']['loc'];

        $this->assertSame('3550308', $loc['cLocPrestacao']);
        $this->assertSame('1058', $loc['cPaisPrestacao']);
    }

    // -----------------------------------------------------------------------
    // Tests: bloco valores
    // -----------------------------------------------------------------------

    public function testInfDpsContainsValoresBlock(): void
    {
        $infDps = $this->transformer->transform($this->dps)['infDPS'];

        $this->assertArrayHasKey('valores', $infDps);
        $valores = $infDps['valores'];

        $this->assertArrayHasKey('vServPrest', $valores);
        $this->assertArrayHasKey('pTotMaior', $valores);
        $this->assertArrayHasKey('trib', $valores);
    }

    public function testValoresVServPrestContainsCorrectValues(): void
    {
        $vServPrest = $this->transformer->transform($this->dps)['infDPS']['valores']['vServPrest'];

        $this->assertSame('1000.00', $vServPrest['vReceb']);
        $this->assertSame('0.00', $vServPrest['vDesc']);
    }

    public function testValoresPTotMaiorContainsCorrectValues(): void
    {
        $pTotMaior = $this->transformer->transform($this->dps)['infDPS']['valores']['pTotMaior'];

        $this->assertSame('1000.00', $pTotMaior['vLiq']);
        $this->assertSame('50.00', $pTotMaior['vCarga']);
        $this->assertSame('0.0500', $pTotMaior['pCargaTrib']);
    }

    public function testValoresTribContainsTribMunAndTotTrib(): void
    {
        $trib = $this->transformer->transform($this->dps)['infDPS']['valores']['trib'];

        $this->assertArrayHasKey('tribMun', $trib);
        $this->assertArrayHasKey('totTrib', $trib);
        $this->assertArrayNotHasKey('tribFed', $trib); // tributacaoFederal é null → omitido
    }

    public function testTribMunContainsCorrectFields(): void
    {
        $tribMun = $this->transformer->transform($this->dps)['infDPS']['valores']['trib']['tribMun'];

        $this->assertSame(1, $tribMun['tribISSQN']);
        $this->assertSame('3550308', $tribMun['cLocIncid']);
        $this->assertSame('0.0500', $tribMun['pAliq']);
        $this->assertSame(1, $tribMun['tpRetBM']);
    }

    public function testTotTribContainsCorrectFields(): void
    {
        $totTrib = $this->transformer->transform($this->dps)['infDPS']['valores']['trib']['totTrib'];

        $this->assertSame('0.0500', $totTrib['pTotTrib']);
        $this->assertSame('50.00', $totTrib['vTotTrib']);
        $this->assertSame(1, $totTrib['indTotTrib']);
    }

    // -----------------------------------------------------------------------
    // Tests: campos null devem ser omitidos
    // -----------------------------------------------------------------------

    public function testNullFieldsAreOmittedFromOutput(): void
    {
        $result = $this->transformer->transform($this->dps);
        $infDps = $result['infDPS'];

        // substituicao null → não deve aparecer como 'subst'
        $this->assertArrayNotHasKey('subst', $infDps);
    }

    public function testTomadorWithoutInscricaoMunicipalOmitsImKey(): void
    {
        $result = $this->transformer->transform($this->dps);
        $tomador = $result['infDPS']['tomador'];

        // inscricaoMunicipal é null → não deve aparecer como 'IM' no tomador
        $this->assertArrayNotHasKey('IM', $tomador);
    }

    // -----------------------------------------------------------------------
    // Tests: resultado é compatível com dps-valida.json
    // -----------------------------------------------------------------------

    public function testOutputMatchesDpsValidaFixture(): void
    {
        $fixtureFile = __DIR__ . '/../Fixtures/dps-valida.json';
        $expected    = json_decode(file_get_contents($fixtureFile), true);

        $actual = $this->transformer->transform($this->dps);

        // Verificar estrutura raiz idêntica
        $this->assertSame($expected['infDPS']['Id'], $actual['infDPS']['Id']);
        $this->assertSame($expected['infDPS']['tpAmb'], $actual['infDPS']['tpAmb']);
        $this->assertSame($expected['infDPS']['dCompet'], $actual['infDPS']['dCompet']);

        // Blocos principais devem existir com os mesmos campos chave
        $this->assertSame(
            $expected['infDPS']['emit']['CNPJ'],
            $actual['infDPS']['emit']['CNPJ']
        );
        $this->assertSame(
            $expected['infDPS']['tomador']['CNPJ'],
            $actual['infDPS']['tomador']['CNPJ']
        );
        $this->assertSame(
            $expected['infDPS']['serv']['cServ']['cTribNac'],
            $actual['infDPS']['serv']['cServ']['cTribNac']
        );
        $this->assertSame(
            $expected['infDPS']['valores']['vServPrest']['vReceb'],
            $actual['infDPS']['valores']['vServPrest']['vReceb']
        );
    }
}
