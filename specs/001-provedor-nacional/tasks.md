---
description: "Task list for Provedor Nacional NFS-e (Padrão ADN REST/JSON)"
---

# Tasks: Provedor Nacional NFS-e (Padrão ADN REST/JSON)

**Input**: Design documents from `specs/001-provedor-nacional/`

**Prerequisites**: plan.md ✅ | spec.md ✅ | data-model.md ✅ | contracts/ ✅ | research.md ✅

**Tests**: Incluídos — constituição exige Test-First (NON-NEGOTIABLE, Princípio III).
Marcar testes como FAILING antes de qualquer implementação.

**Organization**: Tarefas agrupadas por User Story para permitir entrega incremental e
testagem independente de cada história.

## Format: `[ID] [P?] [Story?] Description`

- **[P]**: Pode rodar em paralelo (arquivos diferentes, sem dependências bloqueantes)
- **[Story]**: User Story à qual a tarefa pertence (US1, US2, US3)
- Caminhos relativos à raiz do repositório

## Path Conventions

- Source: `src/Providers/Nacional/` (namespace `NFePHP\NFSe\Providers\Nacional`)
- Tests: `tests/Providers/Nacional/`
- Fixtures: `tests/Providers/Nacional/Fixtures/`

---

## Phase 1: Setup (Infraestrutura inicial)

**Purpose**: Preparar estrutura de diretórios e dependências antes de qualquer código.

- [x] T001 Criar estrutura de diretórios: `src/Providers/Nacional/{Models,Responses,Exceptions,Interfaces}/` e `tests/Providers/Nacional/{Unit,Integration,Fixtures}/`
- [x] T002 Adicionar `"guzzlehttp/guzzle": "^7.0"` em `composer.json` e rodar `composer update`
- [x] T003 [P] Criar `phpstan.neon` na raiz com `level: 8` e `paths: [src/Providers/Nacional]`
- [x] T004 [P] Criar `.php-cs-fixer.php` na raiz com regras PSR-12 apontando para `src/` e `tests/`
- [x] T005 [P] Criar `CHANGELOG.md` na raiz com entrada inicial `## [Unreleased]` (Princípio V — SemVer obrigatório)

**Checkpoint**: `composer install` e `vendor/bin/phpstan analyse` executam sem erro de configuração.

---

## Phase 2: Fundacional (Pré-requisitos bloqueantes para todas as User Stories)

**Purpose**: Interfaces, modelos de dados, cliente HTTP e exceções que TODAS as
histórias dependem. NENHUMA User Story pode começar antes desta fase estar completa.

**⚠️ CRÍTICO**: Nenhuma implementação de US1/US2/US3 pode começar enquanto esta fase não estiver concluída.

- [x] T006 [P] Criar `NacionalProviderInterface` em `src/Providers/Nacional/Interfaces/NacionalProviderInterface.php` com namespace `NFePHP\NFSe\Providers\Nacional\Interfaces` — métodos: `emitir(Dps $dps): RespostaEmissao`, `consultar(string $chaveAcesso): RespostaConsulta`, `cancelar(string $chaveAcesso, string $codigoMotivo): RespostaCancelamento` (conforme `contracts/php-interface.md`)
- [x] T007 [P] Criar `NacionalTransformerInterface` em `src/Providers/Nacional/Interfaces/NacionalTransformerInterface.php` com método `transform(Dps $dps): array` que retorna array pronto para `json_encode()` (conforme `contracts/php-interface.md`)
- [x] T008 [P] Criar hierarquia de exceções em `src/Providers/Nacional/Exceptions/`: `NacionalException.php` (extends `\RuntimeException`), `ValidationException.php` (com `getErros(): array`), `AuthException.php`, `NotFoundException.php`, `AdnException.php` (com `getStatusCode(): int`), `TimeoutException.php` — namespace `NFePHP\NFSe\Providers\Nacional\Exceptions` (conforme `data-model.md` §7)

> **⚠️ Princípio III — RED FIRST**: Escrever T029 e confirmar que FALHA antes de implementar T009.

- [x] T029 [P] Criar `ConfiguracaoNacionalTest.php` em `tests/Providers/Nacional/Unit/ConfiguracaoNacionalTest.php`: testar `getUrlBase()` retorna `https://hom.nfse.gov.br` para HOMOLOGACAO e `https://www.nfse.gov.br` para PRODUCAO; testar que constantes `PRODUCAO = 1` e `HOMOLOGACAO = 2` existem; confirmar que o teste FALHA (classe não existe ainda)
- [x] T009 Criar `ConfiguracaoNacional.php` em `src/Providers/Nacional/ConfiguracaoNacional.php` com namespace `NFePHP\NFSe\Providers\Nacional` — constantes `PRODUCAO = 1` e `HOMOLOGACAO = 2`, métodos `getUrlBase(): string`, `getAmbiente(): int`, `getTimeout(): int`, `getVersaoSchema(): string`, `getCertificadoP12(): string`, `getSenhaCertificado(): string`; URL produção `https://www.nfse.gov.br`, URL homologação `https://hom.nfse.gov.br` (conforme `research.md` §1)
- [x] T010 Criar `Dps.php` em `src/Providers/Nacional/Models/Dps.php` com builder estático — campos: id, ambiente, dataEmissao, competencia, versaoAplicacao, substituicao, emitente, tomador, servico, valores; sem validações ainda (T021 implementa validações)
- [x] T011 [P] Criar `Emitente.php` e `RegimeTributario.php` em `src/Providers/Nacional/Models/` com propriedades `readonly` (PHP 8.1) conforme `data-model.md` §2 — campos: cnpj, inscricaoMunicipal, codigoRegimeTributario, regimeTributario (Emitente); opcaoSimplesNacional, cnae, codigoLocalEmissao (RegimeTributario)
- [x] T012 [P] Criar `Tomador.php` e `Endereco.php` em `src/Providers/Nacional/Models/` — `Tomador` com campos: cnpj (nullable), cpf (nullable), nifEstrangeiro (nullable), inscricaoMunicipal (nullable), endereco; invariante no construtor: exatamente um de cnpj/cpf/nifEstrangeiro deve ser não-nulo, lançar `\InvalidArgumentException` caso contrário (conforme `data-model.md` §3)
- [x] T013 [P] Criar `Servico.php`, `CodigoServico.php`, `ComplementoServico.php` e `LocalPrestacao.php` em `src/Providers/Nacional/Models/` com propriedades `readonly` conforme `data-model.md` §4
- [x] T014 [P] Criar `Valores.php`, `ValorServico.php`, `TributacaoMunicipal.php`, `TributacaoFederal.php` e `TotalTributos.php` em `src/Providers/Nacional/Models/` com propriedades `readonly` conforme `data-model.md` §5; `Valores` deve ter builder estático para fluency
- [x] T015 [P] Criar objetos de resposta em `src/Providers/Nacional/Responses/` com propriedades `readonly` (PHP 8.1, NÃO usar `readonly class` que requer PHP 8.2): `RespostaEmissao.php` (campos: protocolo, status, chaveAcesso nullable, numeroNfse nullable, erros array; métodos `foiEmitida(): bool` e `estaEmProcessamento(): bool`), `RespostaConsulta.php` (campos: chaveAcesso, numeroNfse, status, dataEmissao DateTimeImmutable, dpsOriginal array), `RespostaCancelamento.php` (campos: protocolo, dataEvento DateTimeImmutable, status; método `foiAceito(): bool`) — conforme `contracts/php-interface.md`
- [x] T016 [P] Criar 7 fixtures JSON em `tests/Providers/Nacional/Fixtures/`: `dps-valida.json` (payload completo `infDPS` conforme `contracts/api-nacional.md`), `dps-invalida-cnpj.json` (CNPJ malformado), `response-201-emitida.json` (body HTTP 201 com `nfse.chaveAcesso` e `nfse.numero`), `response-202-processando.json` (body HTTP 202 com `protocolo` e `mensagem`), `response-400-validacao.json` (body HTTP 400 com array `erros`), `response-consulta-200.json` (body HTTP 200 de consulta com `nfse.status`, `nfse.chaveAcesso`, `nfse.numero`, `nfse.dataEmissao`, `nfse.dps`), `response-cancelamento-200.json` (body HTTP 200 de cancelamento com `evento.protocolo`, `evento.dataEvento`, `evento.status`)

> **⚠️ Princípio III — RED FIRST**: Escrever T028 e confirmar que FALHA antes de implementar T017.

- [x] T028 [P] Criar `ExceptionMappingTest.php` em `tests/Providers/Nacional/Unit/ExceptionMappingTest.php`: testar que `NacionalClient` lança `ValidationException` para HTTP 400 e 422, `AuthException` para 401 e 403, `NotFoundException` para 404, `AdnException` para 500 e 503, `TimeoutException` para timeout cURL — usar Guzzle `MockHandler` para simular cada resposta; confirmar que o teste FALHA (classe não existe ainda)
- [x] T017 Criar `NacionalClient.php` em `src/Providers/Nacional/NacionalClient.php` com namespace `NFePHP\NFSe\Providers\Nacional`: construtor recebe `ConfiguracaoNacional`, escreve arquivos PEM temporários via `tempnam()` + `chmod($path, 0600)`, métodos `post(string $endpoint, array $payload): array` e `get(string $endpoint): array` via Guzzle 7.x com `CURLOPT_SSLCERT`/`CURLOPT_SSLKEY`, mapeamento de HTTP status → exceções tipadas (400/422→ValidationException, 401/403→AuthException, 404→NotFoundException, 500/503→AdnException, timeout→TimeoutException), `__destruct()` removendo arquivos PEM (conforme `research.md` §2)
- [x] T018 Criar esqueleto de `Nacional.php` em `src/Providers/Nacional/Nacional.php` com namespace `NFePHP\NFSe\Providers\Nacional`, implementando `NacionalProviderInterface`: construtor recebe `ConfiguracaoNacional`, instancia `NacionalClient` e `NacionalTransformer` internamente; os três métodos (`emitir`, `consultar`, `cancelar`) lançam `\BadMethodCallException('not implemented')` até as fases de implementação

**Checkpoint**: Fundação pronta — `vendor/bin/phpstan analyse src/Providers/Nacional/ --level=8` passa sem erros.

---

## Phase 3: User Story 1 — Emissão de NFS-e (Priority: P1) 🎯 MVP

**Goal**: Enviar uma DPS ao ADN via `POST /api/v1/nfse` com mTLS e receber a
resposta (`RespostaEmissao`) com chave de acesso ou protocolo de processamento.

**Independent Test**: Instanciar `Nacional` com certificado de homologação, chamar
`emitir($dps)` com uma DPS válida e verificar que `RespostaEmissao::foiEmitida()` ou
`RespostaEmissao::estaEmProcessamento()` retorna `true` — sem chamar `consultar` ou `cancelar`.

### ⚠️ Testes para US1 — ESCREVER PRIMEIRO, CONFIRMAR QUE FALHAM

> **REGRA (Princípio III — Constituição)**: Criar os testes abaixo, verificar que FALHAM
> (RED), só então implementar.

- [x] T019 [P] [US1] Criar `DpsBuilderTest.php` em `tests/Providers/Nacional/Unit/DpsBuilderTest.php` com namespace `NFePHP\NFSe\Tests\Providers\Nacional\Unit`: testar que o builder monta `Dps` com todos os campos obrigatórios (usar fixture `dps-valida.json` como referência), que campos inválidos (CNPJ < 14 dígitos, competencia formato errado) lançam `\InvalidArgumentException`, e que `Tomador` com dois identificadores simultâneos lança `\InvalidArgumentException`; verificar que o teste FALHA antes da implementação
- [x] T020 [P] [US1] Criar `NacionalTransformerTest.php` em `tests/Providers/Nacional/Unit/NacionalTransformerTest.php`: testar que `NacionalTransformer::transform(Dps $dps)` gera array com estrutura `infDPS` contendo todos os blocos (`emit`, `tomador`, `serv`, `valores`) conforme `contracts/api-nacional.md`; comparar output com `dps-valida.json` como expected; verificar que o teste FALHA antes da implementação

### Implementação para User Story 1

- [x] T021 [US1] Implementar validações no construtor/builder de `Dps` em `src/Providers/Nacional/Models/Dps.php`: validar que id não está vazio, competencia é `YYYY-MM` e dataEmissao >= primeiro dia da competência; lançar `\InvalidArgumentException` com mensagem descritiva em cada violação
- [x] T022 [US1] Implementar `NacionalTransformer.php` em `src/Providers/Nacional/NacionalTransformer.php` implementando `NacionalTransformerInterface`: método `transform(Dps $dps): array` serializa todos os blocos (`infDPS`, `emit` com `regTrib`, `tomador` com `enderTomador`, `serv` com `cServ`/`compl`/`loc`, `valores` com `vServPrest`/`pTotMaior`/`trib`) conforme schema DPS 1.00 de `contracts/api-nacional.md`; omitir campos `null` do output
- [x] T023 [US1] Implementar `Nacional::emitir(Dps $dps): RespostaEmissao` em `src/Providers/Nacional/Nacional.php`: chamar `$this->transformer->transform($dps)`, invocar `$this->client->post('/api/v1/nfse', $payload)`, mapear resposta HTTP 201 → `new RespostaEmissao(protocolo: $data['nfse']['protocolo'] ?? '', status: 'EMITIDA', chaveAcesso: $data['nfse']['chaveAcesso'], numeroNfse: $data['nfse']['numero'], erros: [])` e HTTP 202 → `new RespostaEmissao(protocolo: $data['protocolo'], status: 'ACEITA', chaveAcesso: null, numeroNfse: null, erros: [])`

**Checkpoint**: `vendor/bin/phpunit tests/Providers/Nacional/Unit/DpsBuilderTest.php tests/Providers/Nacional/Unit/NacionalTransformerTest.php` passa — US1 implementada e testada de forma independente.

---

## Phase 4: User Story 2 — Consulta de NFS-e (Priority: P2)

**Goal**: Recuperar dados de uma NFS-e existente no ADN por chave de acesso
via `GET /api/v1/nfse/{chaveAcesso}`.

**Independent Test**: Chamar `consultar($chaveAcesso)` com uma chave válida de
homologação e verificar que `RespostaConsulta::$status === 'Ativa'` — sem depender
da emissão feita no mesmo test run.

### ⚠️ Testes para US2 — ESCREVER PRIMEIRO, CONFIRMAR QUE FALHAM

- [x] T024 [P] [US2] Criar `ConsultarTest.php` em `tests/Providers/Nacional/Unit/ConsultarTest.php`: usar PHPUnit mock de `NacionalClient` para que `get('/api/v1/nfse/35260512345678000195000123000000001000000001')` retorne o conteúdo de `response-consulta-200.json`; verificar que `Nacional::consultar($chave)` retorna `RespostaConsulta` com `$resultado->status === 'Ativa'`, `$resultado->chaveAcesso === '35260512345678000195000123000000001000000001'` e `$resultado->dataEmissao instanceof \DateTimeImmutable`; verificar que o teste FALHA antes da implementação

### Implementação para User Story 2

- [x] T025 [US2] Implementar `Nacional::consultar(string $chaveAcesso): RespostaConsulta` em `src/Providers/Nacional/Nacional.php`: invocar `$this->client->get('/api/v1/nfse/' . $chaveAcesso)`, mapear resposta HTTP 200 → `new RespostaConsulta(chaveAcesso: $data['nfse']['chaveAcesso'], numeroNfse: $data['nfse']['numero'], status: $data['nfse']['status'], dataEmissao: new \DateTimeImmutable($data['nfse']['dataEmissao']), dpsOriginal: $data['nfse']['dps'])`, HTTP 404 → lançar `NotFoundException`

**Checkpoint**: `vendor/bin/phpunit tests/Providers/Nacional/Unit/ConsultarTest.php` passa — US2 independentemente testável.

---

## Phase 5: User Story 3 — Cancelamento de NFS-e (Priority: P3)

**Goal**: Enviar evento de cancelamento ao ADN para uma NFS-e existente via
`POST /api/v1/nfse/{chaveAcesso}/cancelamento`.

**Independent Test**: Chamar `cancelar($chaveAcesso, '1')` com dados de homologação
e verificar que `RespostaCancelamento::foiAceito()` retorna `true` — sem depender
do fluxo de emissão desta mesma execução de teste.

### ⚠️ Testes para US3 — ESCREVER PRIMEIRO, CONFIRMAR QUE FALHAM

- [x] T026 [P] [US3] Criar `CancelarTest.php` em `tests/Providers/Nacional/Unit/CancelarTest.php`: usar PHPUnit mock de `NacionalClient` para que `post('/api/v1/nfse/35260512345678000195000123000000001000000001/cancelamento', ...)` retorne o conteúdo de `response-cancelamento-200.json`; verificar que `Nacional::cancelar($chave, '1')` retorna `RespostaCancelamento` com `$resultado->foiAceito() === true` e `$resultado->protocolo === '2620260523150000001'`; verificar que o teste FALHA antes da implementação

### Implementação para User Story 3

- [x] T027 [US3] Implementar `Nacional::cancelar(string $chaveAcesso, string $codigoMotivo): RespostaCancelamento` em `src/Providers/Nacional/Nacional.php`: montar payload `infEvento` com campos `cOrgao`, `tpAmb`, `CNPJ`, `chNFSe`, `dhEvento` (now UTC-3), `nSeqEvento: 1`, `tpEvento: '010100'`, `verEvento: '1.00'`, `detEvento.cMotivo: $codigoMotivo`, `detEvento.xMotivo` (conforme mapa de codes 1-4 de `contracts/api-nacional.md`); invocar `$this->client->post('/api/v1/nfse/' . $chaveAcesso . '/cancelamento', $payload)`, mapear HTTP 200 → `new RespostaCancelamento(protocolo: $data['evento']['protocolo'], dataEvento: new \DateTimeImmutable($data['evento']['dataEvento']), status: $data['evento']['status'])`, HTTP 400 → lançar `ValidationException`

**Checkpoint**: Todas as três User Stories funcionam independentemente. Suite completa de unit tests passa.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Testes de exceção/configuração, integração na Factory, qualidade de código.

> T028 e T029 foram movidos para Phase 2 (antes de T017 e T009 respectivamente) para cumprir Princípio III — Red-Green-Refactor.

- [x] T030 Atualizar `src/NFSeStatic.php` (método `tools()`) para reconhecer Padrão Nacional: antes da resolução `Counties/M{cmun}`, verificar se `$config->padraoNacional ?? false` é `true` ou se `($config->cmun ?? '') === '0000000'`, e se sim retornar `new \NFePHP\NFSe\Providers\Nacional\Nacional(new ConfiguracaoNacional(...))` — sem alterar nenhum outro comportamento existente (conforme `plan.md` §Integração na Factory)
- [ ] T031 [P] Rodar `vendor/bin/phpstan analyse src/Providers/Nacional/ --level=8` e corrigir todos os erros de tipo apontados
- [ ] T032 [P] Rodar `vendor/bin/php-cs-fixer fix src/Providers/Nacional/ tests/Providers/Nacional/` e verificar conformidade PSR-12
- [ ] T033 Criar `NacionalProviderIntegrationTest.php` em `tests/Providers/Nacional/Integration/NacionalProviderIntegrationTest.php`: testes de emissão, consulta e cancelamento contra ADN homologação (`hom.nfse.gov.br`); marcar classe com `@group nacional-integration` para execução separada da CI principal; testes requerem variável de ambiente `CERT_PATH` e `CERT_PASSWORD`
- [ ] T034 [P] Validar fluxo do `quickstart.md`: executar os snippets de código do quickstart em ambiente de homologação e confirmar que todos os passos funcionam conforme documentado; atualizar quickstart se necessário

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — pode iniciar imediatamente
- **Fundacional (Phase 2)**: Depende da conclusão do Setup — BLOQUEIA todas as User Stories
- **US1 (Phase 3)**: Depende do Fundacional — MVP entregável
- **US2 (Phase 4)**: Depende do Fundacional — independente de US1
- **US3 (Phase 5)**: Depende do Fundacional — independente de US1 e US2
- **Polish (Phase 6)**: Depende de US1 + US2 + US3 completas

### User Story Dependencies

- **US1 (P1)**: Pode iniciar após Phase 2 — Nenhuma dependência de outras User Stories
- **US2 (P2)**: Pode iniciar após Phase 2 — Nenhuma dependência de US1
- **US3 (P3)**: Pode iniciar após Phase 2 — Nenhuma dependência de US1 ou US2

### Dentro de cada User Story

1. Testes escritos e confirmados FAILING antes de qualquer implementação (Princípio III)
2. Value objects e models antes de services
3. Transformer antes do método no provider principal
4. Core antes de integração na Factory

### Parallel Opportunities

- T003, T004, T005 podem rodar juntos (Setup)
- T006, T007 podem rodar juntos (Fundacional — interfaces e exceções independentes)
- T011, T012, T013, T014 podem rodar juntos (Models independentes por arquivo)
- T015, T016 podem rodar juntos com T011-T014 (Fixtures e Responses — sem dependência dos models)
- T019, T020 podem rodar juntos (Unit tests US1 — arquivos diferentes)
- T024 pode iniciar assim que Phase 2 estiver completa (independente de US1)
- T026 pode iniciar assim que Phase 2 estiver completa (independente de US1 e US2)
- T028, T029 podem rodar juntos (Polish — testes independentes)
- T031, T032 podem rodar juntos (PHPStan e CS-Fixer independentes)

---

## Parallel Example: Fundacional (Phase 2)

```bash
# Grupo A — pode rodar em paralelo:
Task T006: Criar NacionalProviderInterface + NacionalTransformerInterface
Task T007: Criar NacionalTransformerInterface (separado em T007 — arquivo diferente)
Task T008: Criar hierarquia de exceções (6 arquivos)

# Grupo B — depois de T009 (Dps), rodar em paralelo:
Task T011: Criar Emitente + RegimeTributario
Task T012: Criar Tomador + Endereco (com invariante cnpj/cpf/nifEstrangeiro)
Task T013: Criar Servico + CodigoServico + ComplementoServico + LocalPrestacao
Task T014: Criar Valores + ValorServico + TributacaoMunicipal + TributacaoFederal + TotalTributos
Task T015: Criar objetos de resposta (3 arquivos)
Task T016: Criar 7 fixtures JSON de teste

# Grupo C — após Grupos A e B:
Task T017: Criar NacionalClient (requer ConfiguracaoNacional e Exceptions)
Task T018: Criar Nacional.php skeleton (requer NacionalProviderInterface)
```

---

## Implementation Strategy

### MVP First (User Story 1 Apenas)

1. Completar Phase 1: Setup
2. Completar Phase 2: Fundacional (CRÍTICO — bloqueia tudo)
3. Completar Phase 3: US1 — Emissão
4. **PARAR e VALIDAR**: Testar `emitir()` de forma independente contra ADN homologação
5. Deploy/demo de MVP se validado

### Incremental Delivery

1. Setup + Fundacional → Base pronta
2. US1 (Emissão) → Testar independentemente → Deploy/Demo (MVP!)
3. US2 (Consulta) → Testar independentemente → Deploy/Demo
4. US3 (Cancelamento) → Testar independentemente → Deploy/Demo
5. Polish → Integração na Factory, PHPStan, CS-Fixer, integration tests

---

## Notes

- Testes são **obrigatórios** neste projeto (Constituição, Princípio III — Test-First)
- Testes DEVEM falhar antes da implementação — não pular este passo
- [P] = arquivos diferentes, sem dependência entre si
- [Story] vincula a tarefa à User Story para rastreabilidade
- Cada User Story é completamente implementável e testável de forma independente
- PHPStan nível 8 é gate obrigatório antes do merge (Constituição — Technology Constraints)
- Namespace: `NFePHP\NFSe\Providers\Nacional` (segue PSR-4 do composer.json)
- PHP 8.1+ — usar `readonly` properties; NÃO usar `readonly class` (requer PHP 8.2)
- Fixtures JSON extraídas de `contracts/api-nacional.md` — usar payloads exatos documentados
- `response-consulta-200.json` tem shape distinto de `response-201-emitida.json` — não confundir
- Certificado de homologação necessário para Integration Test (T033) — não bloqueia unit tests
