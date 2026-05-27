<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Tests\Providers\Nacional\Unit;

use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;
use NFePHP\NFSe\Providers\Nacional\Exceptions\ValidationException;
use NFePHP\NFSe\Providers\Nacional\Nacional;
use NFePHP\NFSe\Providers\Nacional\NacionalClient;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaCancelamento;
use PHPUnit\Framework\TestCase;

/**
 * T026 — CancelarTest (US3 — Cancelamento de NFS-e)
 *
 * Verifica que Nacional::cancelar($chaveAcesso, $codigoMotivo) retorna
 * RespostaCancelamento corretamente mapeada a partir da resposta do ADN.
 *
 * Usa PHPUnit mock de NacionalClient para que post(...) retorne o conteúdo
 * de response-cancelamento-200.json sem dependência de rede.
 *
 * RED FIRST (Princípio III — Constituição): todos os testes abaixo DEVEM falhar
 * enquanto Nacional::cancelar() lançar BadMethodCallException.
 * Só implementar T027 após confirmar que este arquivo falha.
 */
class CancelarTest extends TestCase
{
    private const CHAVE_ACESSO  = '35260512345678000195000123000000001000000001';
    private const CODIGO_MOTIVO = '1';

    private ConfiguracaoNacional $config;

    /** @var array<string, mixed> */
    private array $fixtureCancelamento200;

    protected function setUp(): void
    {
        // ConfiguracaoNacional com strings vazias — NacionalClient não é instanciado
        // pois injetamos um mock diretamente no construtor de Nacional.
        $this->config = new ConfiguracaoNacional(
            certificadoP12:   '',
            senhaCertificado: '',
            ambiente:         ConfiguracaoNacional::HOMOLOGACAO,
        );

        $fixtureFile = __DIR__ . '/../Fixtures/response-cancelamento-200.json';
        $raw         = file_get_contents($fixtureFile);
        $this->assertNotFalse($raw, 'Fixture response-cancelamento-200.json não encontrada');
        $this->fixtureCancelamento200 = json_decode($raw, true);
    }

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    /**
     * Cria Nacional com NacionalClient mockado que devolve $returnValue quando
     * post() é chamado com o endpoint e payload esperados.
     *
     * @param array<string, mixed> $returnValue
     */
    private function makeNacionalWithMockPost(
        string $expectedEndpoint,
        array  $returnValue
    ): Nacional {
        /** @var NacionalClient&\PHPUnit\Framework\MockObject\MockObject $clientMock */
        $clientMock = $this->createMock(NacionalClient::class);
        $clientMock
            ->expects($this->once())
            ->method('post')
            ->with($expectedEndpoint, $this->isType('array'))
            ->willReturn($returnValue);

        return new Nacional($this->config, $clientMock);
    }

    // -----------------------------------------------------------------------
    // Tests: happy path
    // -----------------------------------------------------------------------

    public function testCancelarRetornaRespostaCancelamento(): void
    {
        $expectedEndpoint = '/api/v1/nfse/' . self::CHAVE_ACESSO . '/cancelamento';
        $nacional         = $this->makeNacionalWithMockPost(
            $expectedEndpoint,
            $this->fixtureCancelamento200
        );

        $resultado = $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);

        $this->assertInstanceOf(RespostaCancelamento::class, $resultado);
    }

    public function testCancelarFoiAceito(): void
    {
        $expectedEndpoint = '/api/v1/nfse/' . self::CHAVE_ACESSO . '/cancelamento';
        $nacional         = $this->makeNacionalWithMockPost(
            $expectedEndpoint,
            $this->fixtureCancelamento200
        );

        $resultado = $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);

        $this->assertTrue($resultado->foiAceito());
    }

    public function testCancelarRetornaProtocoloCorreto(): void
    {
        $expectedEndpoint = '/api/v1/nfse/' . self::CHAVE_ACESSO . '/cancelamento';
        $nacional         = $this->makeNacionalWithMockPost(
            $expectedEndpoint,
            $this->fixtureCancelamento200
        );

        $resultado = $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);

        // Fixture: "protocolo": "2620260523150000001"
        $this->assertSame('2620260523150000001', $resultado->protocolo);
    }

    public function testCancelarRetornaDataEventoComoDateTimeImmutable(): void
    {
        $expectedEndpoint = '/api/v1/nfse/' . self::CHAVE_ACESSO . '/cancelamento';
        $nacional         = $this->makeNacionalWithMockPost(
            $expectedEndpoint,
            $this->fixtureCancelamento200
        );

        $resultado = $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);

        $this->assertInstanceOf(\DateTimeImmutable::class, $resultado->dataEvento);
    }

    public function testCancelarRetornaStatusAceito(): void
    {
        $expectedEndpoint = '/api/v1/nfse/' . self::CHAVE_ACESSO . '/cancelamento';
        $nacional         = $this->makeNacionalWithMockPost(
            $expectedEndpoint,
            $this->fixtureCancelamento200
        );

        $resultado = $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);

        $this->assertSame('Aceito', $resultado->status);
    }

    public function testCancelarEnviaPayloadComInfEvento(): void
    {
        /** @var NacionalClient&\PHPUnit\Framework\MockObject\MockObject $clientMock */
        $clientMock = $this->createMock(NacionalClient::class);
        $clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                '/api/v1/nfse/' . self::CHAVE_ACESSO . '/cancelamento',
                $this->callback(function (array $payload): bool {
                    return isset($payload['infEvento'])
                        && isset($payload['infEvento']['chNFSe'])
                        && $payload['infEvento']['chNFSe'] === self::CHAVE_ACESSO
                        && isset($payload['infEvento']['detEvento']['cMotivo'])
                        && $payload['infEvento']['detEvento']['cMotivo'] === self::CODIGO_MOTIVO
                        && isset($payload['infEvento']['tpEvento'])
                        && $payload['infEvento']['tpEvento'] === '010100';
                })
            )
            ->willReturn($this->fixtureCancelamento200);

        $nacional  = new Nacional($this->config, $clientMock);
        $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);
    }

    // -----------------------------------------------------------------------
    // Tests: mapa de motivos de cancelamento
    // -----------------------------------------------------------------------

    public function testCancelarMotivo1TemDescricaoErroNaEmissao(): void
    {
        /** @var NacionalClient&\PHPUnit\Framework\MockObject\MockObject $clientMock */
        $clientMock = $this->createMock(NacionalClient::class);
        $clientMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->stringContains('/cancelamento'),
                $this->callback(function (array $payload): bool {
                    return isset($payload['infEvento']['detEvento']['xMotivo'])
                        && str_contains(
                            strtolower($payload['infEvento']['detEvento']['xMotivo']),
                            'erro'
                        );
                })
            )
            ->willReturn($this->fixtureCancelamento200);

        $nacional = new Nacional($this->config, $clientMock);
        $nacional->cancelar(self::CHAVE_ACESSO, '1');
    }

    // -----------------------------------------------------------------------
    // Tests: erro HTTP 400 lança ValidationException
    // -----------------------------------------------------------------------

    public function testCancelarComErroLancaValidationException(): void
    {
        $this->expectException(ValidationException::class);

        /** @var NacionalClient&\PHPUnit\Framework\MockObject\MockObject $clientMock */
        $clientMock = $this->createMock(NacionalClient::class);
        $clientMock
            ->expects($this->once())
            ->method('post')
            ->willThrowException(
                new ValidationException(
                    [['codigo' => 'E200', 'mensagem' => 'Prazo para cancelamento expirado']],
                    'Prazo para cancelamento expirado. HTTP 400',
                    400
                )
            );

        $nacional = new Nacional($this->config, $clientMock);
        $nacional->cancelar(self::CHAVE_ACESSO, self::CODIGO_MOTIVO);
    }
}
