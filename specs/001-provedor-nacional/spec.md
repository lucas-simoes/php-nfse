# Feature Specification: Provedor Nacional NFS-e (Padrão ADN REST/JSON)

**Feature Branch**: `001-provedor-nacional`

**Created**: 2026-05-23

**Status**: Draft

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Emissão de NFS-e pelo Padrão Nacional (Priority: P1)

O sistema integrador (ERP, software de gestão) envia os dados de uma prestação
de serviço ao `Nacional` provider e recebe o resultado da emissão — seja o
número da NFS-e gerada ou o status de processamento assíncrono — sem precisar
conhecer os detalhes do protocolo REST/JSON do ADN.

**Why this priority**: É o fluxo primário de valor: sem emissão, nenhum outro
cenário faz sentido. Representa o MVP mínimo aceitável para produção.

**Independent Test**: Pode ser validado isoladamente passando um objeto `Dps`
populado com dados válidos ao método `emitir()` e verificando se a resposta
contém o identificador de NFS-e ou o status de enfileiramento do ADN — sem
necessidade de consulta ou cancelamento.

**Acceptance Scenarios**:

1. **Given** um objeto `Dps` com todos os campos obrigatórios preenchidos
   corretamente e um certificado A1 válido configurado,
   **When** `emitir($dps)` é chamado,
   **Then** a biblioteca envia um `POST /api/v1/nfse` com payload JSON
   conforme o schema DPS nacional, autentica via mTLS e retorna um objeto de
   resposta contendo o identificador da NFS-e ou o número de protocolo de
   processamento.

2. **Given** um `Dps` com campos inválidos (ex: CNPJ mal formatado, código de
   serviço inexistente),
   **When** `emitir($dps)` é chamado,
   **Then** a biblioteca lança uma exceção tipada com a lista de erros de
   validação retornados pela API (HTTP 400), legíveis pela aplicação consumidora.

3. **Given** um certificado digital expirado ou revogado,
   **When** `emitir($dps)` é chamado,
   **Then** a biblioteca lança uma exceção de autenticação (HTTP 401/403) com
   mensagem clara distinguindo falha de mTLS de credencial inválida.

---

### User Story 2 - Consulta de NFS-e pelo Padrão Nacional (Priority: P2)

O sistema integrador consulta a situação ou faz o download de uma NFS-e já
emitida, informando a chave de acesso, e recebe os dados estruturados da nota.

**Why this priority**: Necessário para fechar o ciclo de emissão (confirmação
de que a nota foi aceita pelo ADN) e para relatórios e reemissão de DANFSE.

**Independent Test**: Pode ser testado de forma independente chamando
`consultar($chaveAcesso)` com uma chave fictícia válida em ambiente de
homologação e verificando o objeto de resposta — sem depender do fluxo de
emissão desta mesma execução de teste.

**Acceptance Scenarios**:

1. **Given** uma chave de acesso válida de uma NFS-e existente no ADN,
   **When** `consultar($chaveAcesso)` é chamado,
   **Then** a biblioteca realiza `GET /api/v1/nfse/{chave}` via mTLS e retorna
   um objeto com os dados completos da nota (DPS + metadados do ADN).

2. **Given** uma chave de acesso inexistente no ADN,
   **When** `consultar($chaveAcesso)` é chamado,
   **Then** a biblioteca lança uma exceção tipada de "nota não encontrada"
   (HTTP 404) sem retornar dados parciais.

---

### User Story 3 - Cancelamento de NFS-e pelo Padrão Nacional (Priority: P3)

O sistema integrador solicita o cancelamento de uma NFS-e emitida, informando a
chave de acesso e o código do motivo, e recebe a confirmação do evento de
cancelamento.

**Why this priority**: Completa o ciclo de vida da NFS-e. Menor urgência que
emissão e consulta, mas necessário para conformidade fiscal.

**Independent Test**: Pode ser validado chamando `cancelar($chave, $motivo)`
com dados de homologação e verificando que a biblioteca envia
`POST /api/v1/nfse/{chave}/cancelamento` com o payload de evento correto e
recebe confirmação — independentemente dos outros métodos.

**Acceptance Scenarios**:

1. **Given** uma NFS-e existente e cancelável e um código de motivo válido,
   **When** `cancelar($chaveAcesso, $codigoMotivo)` é chamado,
   **Then** a biblioteca envia o evento de cancelamento via POST e retorna um
   objeto de confirmação com protocolo de cancelamento do ADN.

2. **Given** uma NFS-e que já foi cancelada anteriormente,
   **When** `cancelar($chaveAcesso, $codigoMotivo)` é chamado,
   **Then** a biblioteca lança uma exceção tipada indicando que a operação não
   é permitida no estado atual da nota.

---

### Edge Cases

- O que acontece quando a API do ADN retorna HTTP 500 ou está indisponível?
  A biblioteca DEVE lançar exceção distinguível de erros de negócio.
- O que acontece quando o JSON de resposta não segue o schema esperado?
  A biblioteca DEVE lançar exceção de parse/contrato, não expor array bruto.
- O que acontece quando o certificado A1 está correto mas o CN não pertence
  ao CNPJ do emitente? Deve ser detectado via resposta 401/403 do ADN.
- Timeout de rede: a biblioteca DEVE respeitar um timeout configurável e lançar
  exceção de tempo esgotado distinguível de outros erros HTTP.
- Emissão assíncrona: quando o ADN retorna HTTP 202 (accepted), a biblioteca
  DEVE retornar o protocolo de processamento sem tentar fazer polling automático
  (isso é responsabilidade da aplicação consumidora).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: A biblioteca MUST fornecer uma classe `Nacional` que implemente a
  mesma interface/contrato de comunicação dos provedores municipais existentes,
  para integração com a Factory/Configuração já estabelecida.
- **FR-002**: A classe `Nacional` MUST implementar os métodos `emitir(Dps $dps)`,
  `consultar(string $chaveAcesso)` e `cancelar(string $chaveAcesso, string $codigoMotivo)`.
- **FR-003**: O cliente HTTP MUST autenticar todas as requisições via mTLS,
  injetando o certificado digital A1 no contexto cURL, reutilizando a
  infraestrutura de certificado já provida pelo `sped-common`.
- **FR-004**: A biblioteca MUST serializar os dados da DPS para JSON conforme
  o schema oficial da NFS-e Nacional (ADN), sem campos extras ou faltantes
  obrigatórios.
- **FR-005**: A biblioteca MUST mapear todos os HTTP Status Codes relevantes da
  API do ADN para exceções PHP tipadas e distintas, com mensagens legíveis:
  `400/422` → `ValidationException`, `401/403` → `AuthException`,
  `404` → `NotFoundException`, `500/503` → `AdnException`,
  timeout de rede → `TimeoutException`.
- **FR-006**: A Factory/Configuração existente MUST ser atualizada para retornar
  uma instância de `Nacional` quando o município requisitado estiver registrado
  como "Padrão Nacional" ou quando o uso do padrão nacional for explicitamente
  configurado.
- **FR-007**: A biblioteca MUST suportar configuração de URL base do ADN
  (produção vs. homologação) sem alterar o código da classe `Nacional`.
- **FR-008**: A autenticação com o ADN é feita exclusivamente via mTLS
  (certificado ICP-Brasil A1 no canal TLS). Conforme `contracts/api-nacional.md`,
  nenhum endpoint ADN requer assinatura de payload em Base64 no schema v1.00;
  assinatura XML-DSig de payload é característica dos provedores SOAP municipais
  e está fora do escopo deste provider.

### Key Entities

- **Dps** (Declaração de Prestação de Serviço): Representa o documento fiscal
  nacional. Contém dados do prestador, tomador, serviço prestado, valores,
  tributação e identificadores únicos. É a entrada principal do método `emitir`.
- **NacionalTransformer**: Responsável por mapear o objeto `Dps` (ou estrutura
  de dados unificada existente) para o payload JSON exato exigido pelo schema
  do ADN. Sem lógica de negócio — apenas serialização.
- **RespostaEmissao / RespostaConsulta / RespostaCancelamento**: Objetos de
  valor tipados que encapsulam as respostas da API do ADN, evitando que arrays
  brutos cheguem à aplicação consumidora.
- **NacionalException** (hierarquia): Exceções tipadas por categoria de erro
  (validação, autenticação, não encontrado, erro do ADN, timeout).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Um integrador consegue emitir uma NFS-e pelo Padrão Nacional
  usando a mesma sequência de configuração já usada para provedores municipais,
  com no máximo 3 linhas de código adicionais.
- **SC-002**: 100% dos campos obrigatórios do schema DPS nacional são
  mapeados corretamente pelo `NacionalTransformer`, verificados por testes
  contra o XSD/JSON Schema oficial.
- **SC-003**: Erros da API do ADN (400, 422, 401, 403, 404, 500, 503) e
  timeouts de rede resultam em exceções distintas e capturáveis separadamente
  em um bloco `catch`, sem necessidade de inspecionar códigos HTTP diretamente
  na aplicação consumidora.
- **SC-004**: A troca de ambiente (homologação ↔ produção) é feita por
  alteração de configuração, sem recompilação ou alteração de código.
- **SC-005**: A classe `Nacional` é retornada automaticamente pela Factory
  para qualquer município configurado como "Padrão Nacional", sem alteração
  no código da aplicação consumidora.

## Assumptions

- O fork local do `sped-nfse` / `sped-common` já provê acesso ao certificado
  A1 (chave privada + cadeia de certificados) e às funções OpenSSL para
  assinatura — não será necessário reimplementar essa infraestrutura.
- O projeto já possui ou tolerará a adição de Guzzle (ou cliente HTTP
  equivalente compatível com PSR-18) como dependência, dado que os provedores
  SOAP existentes já usam alguma camada de transporte HTTP.
- PHP 8.1+ é o requisito mínimo, conforme a Constituição do projeto
  (Technology Constraints); recursos de PHP 8.1 como `readonly` properties
  são utilizados nos value objects do provider Nacional.
- A estrutura de dados unificada existente (usada pelos outros provedores para
  representar RPS/Nota) será a fonte de entrada para o `NacionalTransformer`;
  uma nova estrutura `Dps` pode ser criada se a unificação for impraticável.
- Os endpoints do ADN nacional são fixos pelo governo federal e não variam por
  município — diferentemente dos endpoints SOAP municipais. Municípios aderentes
  ao padrão nacional são identificados por flag/lista de configuração.
- Ambiente de homologação do ADN está disponível para testes de integração;
  credenciais de teste (certificado de homologação) são responsabilidade do
  time de desenvolvimento.
