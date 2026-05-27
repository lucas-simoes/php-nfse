<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Tests\Providers\Nacional\Unit;

use NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional;
use PHPUnit\Framework\TestCase;

class ConfiguracaoNacionalTest extends TestCase
{
    public function testConstanteProducaoE1(): void
    {
        $this->assertSame(1, ConfiguracaoNacional::PRODUCAO);
    }

    public function testConstanteHomologacaoE2(): void
    {
        $this->assertSame(2, ConfiguracaoNacional::HOMOLOGACAO);
    }

    public function testGetUrlBaseRetornaHomologacao(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'fake-p12',
            senhaCertificado: 'senha',
            ambiente: ConfiguracaoNacional::HOMOLOGACAO,
        );

        $this->assertSame('https://hom.nfse.gov.br', $config->getUrlBase());
    }

    public function testGetUrlBaseRetornaProducao(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'fake-p12',
            senhaCertificado: 'senha',
            ambiente: ConfiguracaoNacional::PRODUCAO,
        );

        $this->assertSame('https://www.nfse.gov.br', $config->getUrlBase());
    }

    public function testGetAmbienteRetornaValorInformado(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'fake-p12',
            senhaCertificado: 'senha',
            ambiente: ConfiguracaoNacional::HOMOLOGACAO,
        );

        $this->assertSame(ConfiguracaoNacional::HOMOLOGACAO, $config->getAmbiente());
    }

    public function testGetTimeoutDefaultE30(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'fake-p12',
            senhaCertificado: 'senha',
        );

        $this->assertSame(30, $config->getTimeout());
    }

    public function testGetVersaoSchemaDefault(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'fake-p12',
            senhaCertificado: 'senha',
        );

        $this->assertSame('1.00', $config->getVersaoSchema());
    }

    public function testGetCertificadoP12(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'conteudo-p12',
            senhaCertificado: 'minha-senha',
        );

        $this->assertSame('conteudo-p12', $config->getCertificadoP12());
    }

    public function testGetSenhaCertificado(): void
    {
        $config = new ConfiguracaoNacional(
            certificadoP12: 'conteudo-p12',
            senhaCertificado: 'minha-senha',
        );

        $this->assertSame('minha-senha', $config->getSenhaCertificado());
    }
}
