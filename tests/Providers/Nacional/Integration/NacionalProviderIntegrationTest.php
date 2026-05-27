<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Tests\Providers\Nacional\Integration;

use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;
use NFePHP\NFSe\Providers\Nacional\Exceptions\AuthException;
use NFePHP\NFSe\Providers\Nacional\Exceptions\NacionalException;
use NFePHP\NFSe\Providers\Nacional\Models\CodigoServico;
use NFePHP\NFSe\Providers\Nacional\Models\Dps;
use NFePHP\NFSe\Providers\Nacional\Models\Emitente;
use NFePHP\NFSe\Providers\Nacional\Models\Endereco;
use NFePHP\NFSe\Providers\Nacional\Models\LocalPrestacao;
use NFePHP\NFSe\Providers\Nacional\Models\RegimeTributario;
use NFePHP\NFSe\Providers\Nacional\Models\Servico;
use NFePHP\NFSe\Providers\Nacional\Models\Tomador;
use NFePHP\NFSe\Providers\Nacional\Models\Valores;
use NFePHP\NFSe\Providers\Nacional\Nacional;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaCancelamento;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaConsulta;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaEmissao;
use PHPUnit\Framework\TestCase;

/**
 * T033 — NacionalProviderIntegrationTest
 *
 * Testes de integração contra o ADN de homologação (hom.nfse.gov.br).
 *
 * @group nacional-integration
 *
 * ⚠️  ATENÇÃO: Estes testes NÃO fazem parte da suite de CI principal.
 *    Executar somente com certificado A1 de homologação válido.
 *
 * Variáveis de ambiente obrigatórias:
 *   CERT_PATH     — caminho absoluto para o arquivo .pfx / .p12
 *   CERT_PASSWORD — senha do certificado A1
 *
 * Execução:
 *   CERT_PATH=/caminho/cert.pfx CERT_PASSWORD=senha \
 *     vendor/bin/phpunit --group=nacional-integration \
 *       tests/Providers/Nacional/Integration/NacionalProviderIntegrationTest.php
 *
 * Os testes são projetados para execução independente uns dos outros:
 *   - testEmitirDps      : emite uma DPS e verifica resposta emissão/aceitação
 *   - testConsultarNfse  : consulta uma NFS-e existente por chave de acesso
 *   - testCancelarNfse   : cancela uma NFS-e existente por chave de acesso
 *
 * A ordem de execução (emitir → consultar → cancelar) é natural para um
 * fluxo completo, mas cada teste pode rodar isoladamente passando uma
 * chave de acesso válida de homologação.
 */
class NacionalProviderIntegrationTest extends TestCase
{
    private Nacional $provider;

    /**
     * Chave de acesso de uma NFS-e de homologação já emitida (para consulta/cancelamento).
     * Pode ser sobrescrita via variável de ambiente CHAVE_ACESSO_TESTE.
     */
    private string $chaveAcessoExistente;

    protected function setUp(): void
    {
        $certPath = getenv('CERT_PATH');
        $certPass = getenv('CERT_PASSWORD');

        if ($certPath === false || $certPass === false) {
            $this->markTestSkipped(
                'Testes de integração requerem CERT_PATH e CERT_PASSWORD. ' .
                'Execute: CERT_PATH=/path/cert.pfx CERT_PASSWORD=senha vendor/bin/phpunit --group=nacional-integration'
            );
        }

        $p12Content = file_get_contents($certPath);
        if ($p12Content === false) {
            $this->markTestSkipped("Não foi possível ler o certificado em: {$certPath}");
        }

        $config = new ConfiguracaoNacional(
            certificadoP12:   $p12Content,
            senhaCertificado: $certPass,
            ambiente:         ConfiguracaoNacional::HOMOLOGACAO,
            timeout:          30,
        );

        $this->provider             = new Nacional($config);
        $this->chaveAcessoExistente = (string) (getenv('CHAVE_ACESSO_TESTE') ?: '');
    }

    // -----------------------------------------------------------------------
    // Helper — monta DPS de homologação mínima válida
    // -----------------------------------------------------------------------

    /**
     * Constrói uma DPS mínima válida para o ambiente de homologação.
     * Os dados (CNPJ, IM, etc.) devem ser substituídos pelos dados reais do
     * certificado usado em cada execução de teste.
     */
    private function buildDpsHomologacao(): Dps
    {
        $cnpjEmitente      = (string) (getenv('CNPJ_EMITENTE') ?: '12345678000195');
        $imEmitente        = (string) (getenv('IM_EMITENTE') ?: '000123');
        $cnpjTomador       = (string) (getenv('CNPJ_TOMADOR') ?: '98765432000100');
        $codigoMunicipioSP = '3550308';

        $emitente = new Emitente(
            cnpj:                    $cnpjEmitente,
            inscricaoMunicipal:      $imEmitente,
            codigoRegimeTributario:  1,
            regimeTributario: new RegimeTributario(
                opcaoSimplesNacional: 1,
                cnae:                 '6201501',
                codigoLocalEmissao:   $codigoMunicipioSP,
            ),
        );

        $tomador = new Tomador(
            cnpj:  $cnpjTomador,
            endereco: new Endereco(
                logradouro:       'Rua das Flores',
                numero:           '123',
                bairro:           'Centro',
                codigoMunicipio:  $codigoMunicipioSP,
                uf:               'SP',
                cep:              '01310100',
                nomePais:         'BRASIL',
                codigoPais:       '1058',
            ),
        );

        $servico = new Servico(
            codigoServico: new CodigoServico(
                codigoTributacaoNacional: '010700',
                codigoTributacaoMunicipal: '12.03',
                cnae:                     '6201501',
                descricaoServico:         'Desenvolvimento de sistemas de informação',
            ),
            localPrestacao: new LocalPrestacao(
                codigoLocalPrestacao: $codigoMunicipioSP,
                codigoPais:           '1058',
            ),
        );

        $valores = Valores::builder()
            ->valorRecebido('1000.00')
            ->aliquotaIss('0.0500')
            ->localIncidenciaIss($codigoMunicipioSP)
            ->build();

        return Dps::builder()
            ->emitente($emitente)
            ->tomador($tomador)
            ->servico($servico)
            ->valores($valores)
            ->competencia(date('Y-m'))
            ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
            ->build();
    }

    // -----------------------------------------------------------------------
    // US1 — Emissão
    // -----------------------------------------------------------------------

    /**
     * @group nacional-integration
     */
    public function testEmitirDps(): void
    {
        $dps = $this->buildDpsHomologacao();

        try {
            $resposta = $this->provider->emitir($dps);
        } catch (AuthException $e) {
            $this->markTestSkipped(
                'Certificado inválido ou não autorizado para homologação: ' . $e->getMessage()
            );
        } catch (NacionalException $e) {
            $this->fail('Emissão falhou com exceção ADN: ' . $e->getMessage());
        }

        $this->assertInstanceOf(RespostaEmissao::class, $resposta);
        $this->assertTrue(
            $resposta->foiEmitida() || $resposta->estaEmProcessamento(),
            "Resposta deve indicar emissão síncrona (foiEmitida) ou aceitação assíncrona (estaEmProcessamento). " .
            "Status recebido: {$resposta->status}"
        );

        if ($resposta->foiEmitida()) {
            $this->assertNotEmpty($resposta->chaveAcesso, 'Chave de acesso deve estar presente na resposta 201');
            $this->assertNotEmpty($resposta->numeroNfse, 'Número da NFS-e deve estar presente na resposta 201');
            fwrite(STDOUT, "\n[INTEGRATION] NFS-e emitida! Chave: {$resposta->chaveAcesso}\n");
        } else {
            $this->assertNotEmpty($resposta->protocolo, 'Protocolo deve estar presente na resposta 202');
            fwrite(STDOUT, "\n[INTEGRATION] DPS aceita. Protocolo: {$resposta->protocolo}\n");
        }
    }

    // -----------------------------------------------------------------------
    // US2 — Consulta
    // -----------------------------------------------------------------------

    /**
     * @group nacional-integration
     */
    public function testConsultarNfse(): void
    {
        if ($this->chaveAcessoExistente === '') {
            $this->markTestSkipped(
                'Forneça CHAVE_ACESSO_TESTE com uma chave de homologação válida para testar consulta.'
            );
        }

        try {
            $resultado = $this->provider->consultar($this->chaveAcessoExistente);
        } catch (AuthException $e) {
            $this->markTestSkipped('Certificado inválido: ' . $e->getMessage());
        } catch (NacionalException $e) {
            $this->fail('Consulta falhou com exceção ADN: ' . $e->getMessage());
        }

        $this->assertInstanceOf(RespostaConsulta::class, $resultado);
        $this->assertSame(
            $this->chaveAcessoExistente,
            $resultado->chaveAcesso,
            'Chave de acesso retornada deve ser igual à consultada'
        );
        $this->assertNotEmpty($resultado->status, 'Status da NFS-e não pode estar vazio');
        $this->assertInstanceOf(\DateTimeImmutable::class, $resultado->dataEmissao);
        $this->assertIsArray($resultado->dpsOriginal);

        fwrite(STDOUT, "\n[INTEGRATION] NFS-e {$this->chaveAcessoExistente} — Status: {$resultado->status}\n");
    }

    // -----------------------------------------------------------------------
    // US3 — Cancelamento
    // -----------------------------------------------------------------------

    /**
     * @group nacional-integration
     */
    public function testCancelarNfse(): void
    {
        if ($this->chaveAcessoExistente === '') {
            $this->markTestSkipped(
                'Forneça CHAVE_ACESSO_TESTE com uma chave de homologação válida para testar cancelamento.'
            );
        }

        try {
            $resultado = $this->provider->cancelar($this->chaveAcessoExistente, '1');
        } catch (AuthException $e) {
            $this->markTestSkipped('Certificado inválido: ' . $e->getMessage());
        } catch (NacionalException $e) {
            $this->fail('Cancelamento falhou com exceção ADN: ' . $e->getMessage());
        }

        $this->assertInstanceOf(RespostaCancelamento::class, $resultado);
        $this->assertTrue(
            $resultado->foiAceito(),
            "Cancelamento deveria ter sido aceito. Status recebido: {$resultado->status}"
        );
        $this->assertNotEmpty($resultado->protocolo, 'Protocolo de cancelamento não pode estar vazio');
        $this->assertInstanceOf(\DateTimeImmutable::class, $resultado->dataEvento);

        fwrite(
            STDOUT,
            "\n[INTEGRATION] Cancelamento aceito! Protocolo: {$resultado->protocolo} — " .
            "Status: {$resultado->status}\n"
        );
    }
}
