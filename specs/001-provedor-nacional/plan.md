# Implementation Plan: Provedor Nacional NFS-e (Padrão ADN REST/JSON)

**Branch**: `001-provedor-nacional` | **Date**: 2026-05-23 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/001-provedor-nacional/spec.md`

## Summary

Implementar a classe `Nacional` (provider para o Padrão Nacional NFS-e / ADN)
e sua infraestrutura de suporte — cliente HTTP com mTLS, transformer DPS→JSON,
hierarquia de exceções tipadas e atualização da Factory — integrados ao fork
PHP do `sped-nfse`, respeitando os contratos de interface dos provedores
municipais existentes documentados em `ARCHITECTURE.md`.

O ADN usa REST/JSON (não SOAP/XML), autenticação via certificado ICP-Brasil A1
no canal mTLS (não assinatura XML-DSig), e o modelo de documento DPS em vez
do RPS XML. A adição é puramente aditiva: nenhum código legado existente em
`src/Common/`, `src/Models/` ou `src/Counties/` é alterado.

## Technical Context

**Language/Version**: PHP 8.1+ (obrigatório — constituição Technology Constraints).
Propriedades `readonly` disponíveis; `readonly class` (PHP 8.2) NÃO DEVE ser
usado no core público para não elevar o requisito sem discussão.

**Namespace**: `NFePHP\NFSe\Providers\Nacional` — segue o PSR-4 autoload do
`composer.json` (`NFePHP\\NFSe\\` → `src/`). Código em `src/Providers/Nacional/`.

**Primary Dependencies**:
- `guzzlehttp/guzzle` ^7.0 — cliente HTTP com suporte a opções cURL para mTLS
  (única dependência nova; justificada porque o transporte SOAP legado não suporta mTLS)
- `nfephp-org/sped-common` — infraestrutura de certificado A1 e OpenSSL já presente;
  `NFePHP\Common\Certificate` fornece extração do P12 (chave + cert PEM)
- `phpunit/phpunit` ^10 — testes (dev)
- `phpstan/phpstan` ^1 — análise estática nível 8 (dev)
- `friendsofphp/php-cs-fixer` ^3 — PSR-12 (dev)

**Storage**: N/A — biblioteca stateless; não persiste dados.
Arquivos PEM temporários (`tempnam()`, `chmod 0600`) são removidos no `__destruct()`.

**Testing**: PHPUnit 10+; testes unitários (mock Guzzle) + integração contra
ambiente de homologação do ADN (`hom.nfse.gov.br`).

**Target Platform**: Servidor Linux/PHP com extensões `openssl` e `curl` ativas.
Compatível com qualquer SAPI (FPM, CLI, Apache). Sem dependência de framework.

**Project Type**: Biblioteca PHP (Composer package) — extensão additive ao fork
`sped-nfse`. Segue o mesmo padrão dos provedores municipais em `src/Models/`.

**Performance Goals**: Latência do `emitir()` determinada pelo ADN (não pela lib).
A biblioteca não deve adicionar overhead >5ms acima da latência de rede.

**Constraints**:
- Nenhuma alteração em `src/Common/`, `src/Models/`, `src/Counties/` — zero
  breaking changes (ver `ARCHITECTURE.md` §Evolução para o Padrão Nacional).
- Nenhuma escrita em disco de longa duração (arquivos PEM são temporários).
- Nenhuma dependência de framework web como requisito.
- `readonly class` proibido no core (exige PHP 8.2); usar readonly properties
  em classes regulares (PHP 8.1 compatível).

**Scale/Scope**: Biblioteca de uso por integradores (ERPs, sistemas de gestão).
Volume de requisições determinado pelo cliente; a lib não implementa pooling
nem rate limiting.

## Constitution Check

*GATE: Deve passar antes da implementação. Re-verificado após design.*

| Princípio | Status | Observação |
|-----------|--------|------------|
| I. Compliance-First | ✅ PASS | `NacionalTransformer` valida contra JSON Schema DPS oficial; exceções tipadas por categoria de erro ADN; schema DPS nacional único — sem variação municipal |
| II. Library-First | ✅ PASS | Nenhuma dependência de framework; `Nacional` instanciável standalone; interfaces definem contrato público; Guzzle justificado (mTLS não suportado pelo SOAP legado) |
| III. Test-First | ✅ PASS | Tarefas de teste precedem implementação em cada fase; fixtures para payloads válidos e inválidos; Red-Green-Refactor enforced por tasks.md |
| IV. Simplicity | ✅ PASS | Apenas Guzzle adicionado; DPS é modelo direto sem bridge XML; sem abstrações para casos de uso hipotéticos; zero alterações no core legado |
| V. SemVer | ✅ PASS | Adição aditiva → MINOR bump; CHANGELOG obrigatório; `verAplic` configurável para futuras revisões do schema DPS |

**Resultado**: APROVADO — prosseguir para implementação.

**Nota arquitetural**: A constituição (Technology Constraints) define PHP 8.1+ como
mínimo. O plano anterior indicava "PHP 7.4 mínimo" por conservadorismo — corrigido
aqui. PHP 8.1 permite `readonly` properties, `enums`, e `named arguments` que
simplificam os value objects do DPS sem custos de compatibilidade dentro deste projeto.

## Project Structure

### Documentation (this feature)

```text
specs/001-provedor-nacional/
├── plan.md          ← este arquivo
├── spec.md          ✅ gerado
├── research.md      ✅ gerado
├── data-model.md    ✅ gerado
├── quickstart.md    ✅ gerado
├── contracts/
│   ├── api-nacional.md     ✅ Contrato REST da API do ADN
│   └── php-interface.md    ✅ Interfaces PHP públicas da biblioteca
└── tasks.md                ✅ gerado por /speckit-tasks
```

### Source Code (repository root)

O provider Nacional segue o princípio da constituição: "Municipality-specific
adapters are placed under `src/Providers/{Municipality}/`".

```text
src/
├── NFSe.php                          # Entry point público (NÃO ALTERADO)
├── NFSeStatic.php                    # Factory estática — ATUALIZAR para reconhecer
│                                     # cmun='0000000' ou isPadraoNacional()
├── Common/                           # (NÃO ALTERADO — infraestrutura SOAP legada)
├── Models/                           # (NÃO ALTERADO — padrões SOAP municipais)
├── Counties/                         # (NÃO ALTERADO — sobreposições municipais)
└── Providers/
    └── Nacional/
        ├── Nacional.php              # Provider principal (implements NacionalProviderInterface)
        ├── NacionalClient.php        # Cliente HTTP Guzzle + mTLS
        ├── NacionalTransformer.php   # Serialização Dps → JSON array
        ├── ConfiguracaoNacional.php  # Value object de configuração (PRODUCAO=1/HOMOLOGACAO=2)
        ├── Models/
        │   ├── Dps.php               # Documento DPS com builder estático
        │   ├── Emitente.php
        │   ├── RegimeTributario.php
        │   ├── Tomador.php
        │   ├── Endereco.php
        │   ├── Servico.php
        │   ├── CodigoServico.php
        │   ├── ComplementoServico.php
        │   ├── LocalPrestacao.php
        │   ├── Valores.php
        │   ├── ValorServico.php
        │   ├── TributacaoMunicipal.php
        │   ├── TributacaoFederal.php
        │   └── TotalTributos.php
        ├── Responses/
        │   ├── RespostaEmissao.php
        │   ├── RespostaConsulta.php
        │   └── RespostaCancelamento.php
        ├── Exceptions/
        │   ├── NacionalException.php      # Base
        │   ├── ValidationException.php    # HTTP 400, 422
        │   ├── AuthException.php          # HTTP 401, 403
        │   ├── NotFoundException.php      # HTTP 404
        │   ├── AdnException.php           # HTTP 500, 503
        │   └── TimeoutException.php       # cURL timeout
        └── Interfaces/
            ├── NacionalProviderInterface.php
            └── NacionalTransformerInterface.php

tests/
└── Providers/
    └── Nacional/
        ├── Unit/
        │   ├── DpsBuilderTest.php
        │   ├── NacionalTransformerTest.php
        │   ├── ExceptionMappingTest.php
        │   └── ConfiguracaoNacionalTest.php
        ├── Integration/
        │   └── NacionalProviderIntegrationTest.php  # Contra ADN homologação
        └── Fixtures/
            ├── dps-valida.json
            ├── dps-invalida-cnpj.json
            ├── response-201-emitida.json
            ├── response-202-processando.json
            ├── response-400-validacao.json
            ├── response-consulta-200.json
            └── response-cancelamento-200.json
```

**Structure Decision**: Single project (Opção 1) — extensão aditiva ao fork.
O namespace `NFePHP\NFSe\Providers\Nacional` segue o autoload PSR-4 existente
(`"NFePHP\\NFSe\\": "src/"`). O diretório `src/Providers/Nacional/` é o local
mandatado pela constituição para adapters de provedores.

### Integração na Factory existente (NFSeStatic.php)

```php
// Em NFSeStatic::tools() — ÚNICO PONTO DE ALTERAÇÃO no core legado:
public static function tools(stdClass $config, Certificate $certificate = null)
{
    // ← Novo: detectar Padrão Nacional antes de resolver Counties/M{cmun}
    if (!empty($config->padraoNacional) || ($config->cmun ?? '') === '0000000') {
        return new \NFePHP\NFSe\Providers\Nacional\Nacional(
            new \NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional(
                certificadoP12: $certificate->getCertificate() . $certificate->getPrivateKey(),
                senhaCertificado: '',  // chave já extraída pelo Certificate
                ambiente: $config->tpAmb ?? 2,
                timeout: $config->timeout ?? 30,
            )
        );
    }
    // ← Fluxo SOAP legado inalterado:
    $className = static::getClassName($config, 'Tools');
    // ...
}
```

## Complexity Tracking

> Nenhuma violação identificada no Constitution Check.

| Decisão | Justificativa | Alternativa Rejeitada |
|---------|--------------|----------------------|
| Namespace `NFePHP\NFSe\Providers\Nacional` | Segue PSR-4 do composer.json; não cria namespace raiz novo | `NFSe\Nacional` exigiria segunda entrada autoload e complicaria o pacote |
| Guzzle como nova dependência | mTLS via `CURLOPT_SSLCERT` + `CURLOPT_SSLKEY` não é suportado pelo `SoapInterface` legado | Implementar cliente HTTP puro com stream_socket_client aumentaria complexidade de manutenção |
| PHP 8.1+ (corrigido de 7.4) | Constituição Technology Constraints define 8.1+; permite readonly properties | Manter 7.4 obrigaria sintaxe mais verbosa sem benefício real neste projeto |
| Zero alterações em Common/Models/Counties | ARCHITECTURE.md §Evolução documenta estratégia aditiva; qualquer alteração no core poderia quebrar 64 municípios | Herdar de `Common\Tools` adicionaria acoplamento desnecessário ao SOAP |

## Artifacts gerados

| Artifact | Caminho | Descrição |
|----------|---------|-----------|
| research.md | `specs/001-provedor-nacional/research.md` | Decisões de tecnologia e justificativas |
| data-model.md | `specs/001-provedor-nacional/data-model.md` | Entidades, campos, relações, exceções |
| api-nacional.md | `specs/001-provedor-nacional/contracts/api-nacional.md` | Contrato REST do ADN com exemplos de payload |
| php-interface.md | `specs/001-provedor-nacional/contracts/php-interface.md` | Interfaces PHP públicas e exemplos de uso |
| quickstart.md | `specs/001-provedor-nacional/quickstart.md` | Guia de início rápido para integradores |
| ARCHITECTURE.md | `ARCHITECTURE.md` | Visão arquitetural do projeto (Factory, Strategy, fluxos, coexistência) |

## Mudanças em relação ao plano anterior

| Dimensão | Plano Anterior | Plano Atual (corrigido) |
|----------|---------------|------------------------|
| Namespace | `NFSe\Nacional` (namespace isolado) | `NFePHP\NFSe\Providers\Nacional` (segue PSR-4 do composer.json) |
| PHP mínimo | PHP 7.4 (por conservadorismo) | PHP 8.1+ (per constituição Technology Constraints) |
| `readonly class` | Usado em contratos | Proibido no core (PHP 8.2+); usar `readonly` properties (PHP 8.1) |
| Fixture US2 | `response-201-emitida.json` em T022 | `response-consulta-200.json` (shape HTTP 200 com `nfse.status`) |
| Fixtures | 6 arquivos | 7 arquivos (inclui `response-consulta-200.json` separado) |
| Integração factory | Referência genérica | Ponto exato: `NFSeStatic::tools()` com snippet de integração |
| Testes unitários | 4 arquivos em `tests/Unit/` | 4 arquivos em `tests/Providers/Nacional/Unit/` (alinhado com constituição) |
