<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Tests\Providers\Nacional\Unit;

use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;
use NFePHP\NFSe\Providers\Nacional\Nacional;
use NFePHP\NFSe\Providers\Nacional\NacionalClient;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaConsulta;
use PHPUnit\Framework\TestCase;

/**
 * T024 — ConsultarTest
 *
 * Verifica que Nacional::consultar($chaveAcesso) retorna RespostaConsulta
 * corretamente mapeada a partir da resposta do ADN.
 *
 * Usa PHPUnit mock de NacionalClient para que get('/api/v1/nfse/{chave}')
 * retorne o conteúdo de response-consulta-200.json sem dependência de rede.
 *
 * RED FIRST: todos os testes falham até T025 ser implementado, pois
 * Nacional::consultar() lança BadMethodCallException.
 */
class ConsultarTest extends TestCase
{
    private const CHAVE_ACESSO = '35260512345678000195000123000000001000000001';

    private ConfiguracaoNacional $config;

    /** @var array<string, mixed> */
    private array $fixtureConsulta200;

    protected function setUp(): void
    {
        // ConfiguracaoNacional com strings vazias — NacionalClient não é instanciado
        // pois injetamos um mock diretamente no construtor de Nacional.
        $this->config = new ConfiguracaoNacional(
            certificadoP12:   '',
            senhaCertificado: '',
            ambiente:         ConfiguracaoNacional::HOMOLOGACAO,
        );

        $fixtureFile = __DIR__ . '/../Fixtures/response-consulta-200.json';
        $raw         = file_get_contents($fixtureFile);
        $this->assertNotFalse($raw, 'Fixture response-consulta-200.json não encontrada');
        $this->fixtureConsulta200 = json_decode($raw, true);
    }

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    /**
     * Cria Nacional com NacionalClient mockado que devolve $returnValue
     * quando get() é chamado com o endpoint esperado.
     *
     * @param array<string, mixed> $returnValue
     */
    private function makeNacionalWithMockGet(
        string $expectedEndpoint,
        array  $returnValue
    ): Nacional {
        /** @var NacionalClient&\PHPUnit\Framework\MockObject\MockObject $clientMock */
        $clientMock = $this->createMock(NacionalClient::class);
        $clientMock
            ->expects($this->once())
            ->method('get')
            ->with($expectedEndpoint)
            ->willReturn($returnValue);

        return new Nacional($this->config, $clientMock);
    }

    // -----------------------------------------------------------------------
    // Tests: happy path
    // -----------------------------------------------------------------------

    public function testConsultarRetornaRespostaConsulta(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        $this->assertInstanceOf(RespostaConsulta::class, $resultado);
    }

    public function testConsultarRetornaStatusAtiva(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        $this->assertSame('Ativa', $resultado->status);
    }

    public function testConsultarRetornaChaveAcessoCorreta(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        $this->assertSame(self::CHAVE_ACESSO, $resultado->chaveAcesso);
    }

    public function testConsultarRetornaNumeroNfse(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        $this->assertSame('000000001', $resultado->numeroNfse);
    }

    public function testConsultarRetornaDataEmissaoComoDateTimeImmutable(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        $this->assertInstanceOf(\DateTimeImmutable::class, $resultado->dataEmissao);
    }

    public function testConsultarRetornaDataEmissaoCorreta(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        // Fixture: "2026-05-23T10:00:05-03:00"
        $this->assertSame('2026-05-23', $resultado->dataEmissao->format('Y-m-d'));
    }

    public function testConsultarRetornaDpsOriginalComoArray(): void
    {
        $nacional  = $this->makeNacionalWithMockGet(
            '/api/v1/nfse/' . self::CHAVE_ACESSO,
            $this->fixtureConsulta200
        );

        $resultado = $nacional->consultar(self::CHAVE_ACESSO);

        $this->assertIsArray($resultado->dpsOriginal);
        $this->assertNotEmpty($resultado->dpsOriginal);
    }

    // -----------------------------------------------------------------------
    // Tests: erro 404 lança NotFoundException
    // -----------------------------------------------------------------------

    public function testConsultarComChaveInexistenteLancaNotFoundException(): void
    {
        $this->expectException(
            \NFePHP\NFSe\Providers\Nacional\Exceptions\NotFoundException::class
        );

        /** @var NacionalClient&\PHPUnit\Framework\MockObject\MockObject $clientMock */
        $clientMock = $this->createMock(NacionalClient::class);
        $clientMock
            ->expects($this->once())
            ->method('get')
            ->willThrowException(
                new \NFePHP\NFSe\Providers\Nacional\Exceptions\NotFoundException(
                    'NFS-e não encontrada. HTTP 404',
                    404
                )
            );

        $nacional = new Nacional($this->config, $clientMock);
        $nacional->consultar('00000000000000000000000000000000000000000000');
    }
}
