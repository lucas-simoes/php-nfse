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
use PHPUnit\Framework\TestCase;

/**
 * T019 — DpsBuilderTest
 *
 * Verifica que o builder monta Dps com todos os campos obrigatórios (usando
 * dps-valida.json como referência), que campos inválidos lançam
 * \InvalidArgumentException, e que Tomador com dois identificadores simultâneos
 * também lança \InvalidArgumentException.
 *
 * RED FIRST: os testes de validação devem FALHAR antes de T021 ser implementado.
 */
class DpsBuilderTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function makeEmitente(): Emitente
    {
        return new Emitente(
            cnpj: '12345678000195',
            inscricaoMunicipal: '000123',
            codigoRegimeTributario: 1,
            regimeTributario: new RegimeTributario(
                opcaoSimplesNacional: 1,
                cnae: '6201501',
                codigoLocalEmissao: '3550308',
            ),
        );
    }

    private function makeTomador(): Tomador
    {
        return new Tomador(
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
    }

    private function makeServico(): Servico
    {
        return new Servico(
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
    }

    private function makeValores(): Valores
    {
        return Valores::builder()
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
    }

    /** Constrói uma DPS totalmente válida (espelho de dps-valida.json). */
    private function buildValidDps(): Dps
    {
        return Dps::builder()
            ->id('DPS12345678000195000123202605230000000001')
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->dataEmissao(new \DateTimeImmutable('2026-05-23T10:00:00-03:00'))
            ->competencia('2026-05')
            ->versaoAplicacao('1.00')
            ->emitente($this->makeEmitente())
            ->tomador($this->makeTomador())
            ->servico($this->makeServico())
            ->valores($this->makeValores())
            ->build();
    }

    // -----------------------------------------------------------------------
    // Tests: happy path
    // -----------------------------------------------------------------------

    public function testBuildsValidDpsWithAllRequiredFields(): void
    {
        $dps = $this->buildValidDps();

        $this->assertSame('DPS12345678000195000123202605230000000001', $dps->id);
        $this->assertSame(ConfiguracaoNacional::HOMOLOGACAO, $dps->ambiente);
        $this->assertSame('2026-05', $dps->competencia);
        $this->assertSame('1.00', $dps->versaoAplicacao);
        $this->assertNull($dps->substituicao);

        // Emitente
        $this->assertSame('12345678000195', $dps->emitente->cnpj);
        $this->assertSame('000123', $dps->emitente->inscricaoMunicipal);
        $this->assertSame(1, $dps->emitente->codigoRegimeTributario);
        $this->assertSame('6201501', $dps->emitente->regimeTributario->cnae);
        $this->assertSame('3550308', $dps->emitente->regimeTributario->codigoLocalEmissao);

        // Tomador
        $this->assertSame('98765432000100', $dps->tomador->cnpj);
        $this->assertNull($dps->tomador->cpf);
        $this->assertSame('Rua das Flores', $dps->tomador->endereco->logradouro);
        $this->assertSame('3550308', $dps->tomador->endereco->codigoMunicipio);

        // Serviço
        $this->assertSame('010700', $dps->servico->codigoServico->codigoTributacaoNacional);
        $this->assertSame('12.03', $dps->servico->codigoServico->codigoTributacaoMunicipal);
        $this->assertNotNull($dps->servico->complemento);
        $this->assertSame('Sistema de gestão empresarial', $dps->servico->complemento->textoComplemento);

        // Valores
        $this->assertSame('1000.00', $dps->valores->valorServicoPrestado->valorRecebido);
        $this->assertSame('0.0500', $dps->valores->tributacao->tributacaoMunicipal->aliquota);
        $this->assertSame('50.00', $dps->valores->tributacao->totalTributos->valorTotalTributos);
    }

    public function testDataEmissaoIsDateTimeImmutable(): void
    {
        $dps = $this->buildValidDps();
        $this->assertInstanceOf(\DateTimeImmutable::class, $dps->dataEmissao);
    }

    // -----------------------------------------------------------------------
    // Tests: validation (RED — falham até T021 ser implementado)
    // -----------------------------------------------------------------------

    public function testEmptyIdThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/id/i');

        Dps::builder()
            ->id('')                                             // ← ID vazio
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->dataEmissao(new \DateTimeImmutable('2026-05-23T10:00:00-03:00'))
            ->competencia('2026-05')
            ->emitente($this->makeEmitente())
            ->tomador($this->makeTomador())
            ->servico($this->makeServico())
            ->valores($this->makeValores())
            ->build();
    }

    public function testInvalidCompetenciaFormatThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/competencia/i');

        Dps::builder()
            ->id('DPS12345678000195000123202605230000000001')
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->dataEmissao(new \DateTimeImmutable('2026-05-23T10:00:00-03:00'))
            ->competencia('2026-5')                             // ← formato inválido (MM precisa de 2 dígitos)
            ->emitente($this->makeEmitente())
            ->tomador($this->makeTomador())
            ->servico($this->makeServico())
            ->valores($this->makeValores())
            ->build();
    }

    public function testCompetenciaWithDayThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/competencia/i');

        Dps::builder()
            ->id('DPS12345678000195000123202605230000000001')
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->dataEmissao(new \DateTimeImmutable('2026-05-23T10:00:00-03:00'))
            ->competencia('2026-05-01')                         // ← inclui dia, inválido
            ->emitente($this->makeEmitente())
            ->tomador($this->makeTomador())
            ->servico($this->makeServico())
            ->valores($this->makeValores())
            ->build();
    }

    public function testDataEmissaoBeforeCompetenciaThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // dataEmissao = 2026-04-30 (abril), competencia = 2026-05 (maio) → inválido
        Dps::builder()
            ->id('DPS12345678000195000123202605230000000001')
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->dataEmissao(new \DateTimeImmutable('2026-04-30T23:59:59-03:00')) // ← antes da competência
            ->competencia('2026-05')
            ->emitente($this->makeEmitente())
            ->tomador($this->makeTomador())
            ->servico($this->makeServico())
            ->valores($this->makeValores())
            ->build();
    }

    public function testEmitenteWithShortCnpjThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/cnpj/i');

        new Emitente(
            cnpj: '123456780001',                               // ← 12 dígitos, inválido
            inscricaoMunicipal: '000123',
            codigoRegimeTributario: 1,
            regimeTributario: new RegimeTributario(
                opcaoSimplesNacional: 1,
                cnae: '6201501',
                codigoLocalEmissao: '3550308',
            ),
        );
    }

    public function testTomadorWithTwoIdentifiersThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Tomador(
            cnpj: '98765432000100',                             // ← dois identificadores simultâneos
            cpf: '12345678901',
            nifEstrangeiro: null,
            inscricaoMunicipal: null,
            endereco: new Endereco(
                logradouro: 'Rua X',
                numero: '1',
                complemento: null,
                bairro: 'Centro',
                codigoMunicipio: '3550308',
                uf: 'SP',
                cep: '01310100',
                nomePais: 'BRASIL',
                codigoPais: '1058',
            ),
        );
    }

    public function testTomadorWithNoIdentifierThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Tomador(
            cnpj: null,                                         // ← nenhum identificador
            cpf: null,
            nifEstrangeiro: null,
            inscricaoMunicipal: null,
            endereco: new Endereco(
                logradouro: 'Rua X',
                numero: '1',
                complemento: null,
                bairro: 'Centro',
                codigoMunicipio: '3550308',
                uf: 'SP',
                cep: '01310100',
                nomePais: 'BRASIL',
                codigoPais: '1058',
            ),
        );
    }
}
