# Data Model: Provedor Nacional NFS-e

**Feature**: 001-provedor-nacional
**Date**: 2026-05-23

---

## Entidades Principais

### 1. `Dps` — Declaração de Prestação de Serviço

Value object imutável. Entrada do método `emitir()`.

```
Dps
├── id: string                    # Id único da DPS: "DPS{CNPJ}{IM}{ANO}{MES}{SEQ}"
├── ambiente: Ambiente            # Enum: PRODUCAO=1, HOMOLOGACAO=2
├── dataEmissao: DateTimeImmutable
├── competencia: string           # "YYYY-MM" (mês de prestação)
├── versaoAplicacao: string       # default "1.00"
├── substituicao: ?Substituicao
├── emitente: Emitente
├── tomador: Tomador
├── servico: Servico
└── valores: Valores
```

**Validações da DPS**:
- `id` MUST seguir padrão: `DPS` + CNPJ (14d) + IM + ANO (4d) + MES (2d) + SEQ (10d)
- `competencia` MUST ser `YYYY-MM` no passado ou mês atual
- `dataEmissao` MUST ser >= primeiro dia de `competencia`

---

### 2. `Emitente`

```
Emitente
├── cnpj: string               # 14 dígitos, apenas números
├── inscricaoMunicipal: string # IM no município de emissão
├── regimeTributario: RegimeTributario
└── codigoRegimeTributario: int # CRT: 1=Simples, 2=Lucro Presumido, 3=Lucro Real
```

```
RegimeTributario
├── opcaoSimplesNacional: int  # 1=Não optante, 2=Optante sem IRPJ/CSLL, 3=Optante com IRPJ/CSLL
├── cnae: string               # CNAE principal do emitente (7 dígitos)
└── codigoLocalEmissao: string # Código IBGE do município de emissão (7 dígitos)
```

---

### 3. `Tomador`

```
Tomador
├── cnpj: ?string              # 14 dígitos (exclusivo com cpf)
├── cpf: ?string               # 11 dígitos (exclusivo com cnpj)
├── nifEstrangeiro: ?string    # Para tomadores estrangeiros (exclusivo com cnpj/cpf)
├── inscricaoMunicipal: ?string
└── endereco: Endereco
```

**Invariante**: exatamente um de `cnpj`, `cpf`, `nifEstrangeiro` MUST ser não-nulo.

```
Endereco
├── logradouro: string
├── numero: string
├── complemento: ?string
├── bairro: string
├── codigoMunicipio: string    # Código IBGE 7 dígitos
├── uf: string                 # Sigla estado (2 letras)
├── cep: string                # 8 dígitos
├── nomePais: string           # ex: "BRASIL"
└── codigoPais: string         # ex: "1058" (Brasil)
```

---

### 4. `Servico`

```
Servico
├── codigoServico: CodigoServico
├── complemento: ?ComplementoServico
└── localPrestacao: LocalPrestacao
```

```
CodigoServico
├── codigoTributacaoNacional: string  # cTribNac — 6 dígitos (LC116)
├── codigoTributacaoMunicipal: string # cTribMun — código local do município
├── cnae: string                      # CNAE da atividade (7 dígitos)
└── descricaoServico: string          # Texto livre, máx 2000 chars
```

```
ComplementoServico
├── textoComplemento: ?string   # Detalhamento livre
└── codigoIncentivoBeneficio: ?string
```

```
LocalPrestacao
├── codigoLocalPrestacao: string   # Código IBGE do município onde o serviço foi prestado
└── codigoPais: string             # "1058" para Brasil
```

---

### 5. `Valores`

```
Valores
├── valorServicoPrestado: ValorServico
├── deducoesReducoes: ?DeducaoReducao
├── totalMaior: TotalMaior
└── tributacao: Tributacao
```

```
ValorServico
├── valorRecebido: string   # Decimal como string: "1000.00"
└── valorDesconto: string   # Decimal como string: "0.00"
```

```
TotalMaior
├── valorLiquido: string         # = valorRecebido - valorDesconto - deduções
├── valorCargaTributaria: string # Total de tributos
└── percentualCargaTributaria: string # pCargaTrib em decimal: "0.1500"
```

```
Tributacao
├── tributacaoMunicipal: TributacaoMunicipal
├── tributacaoFederal: ?TributacaoFederal
└── totalTributos: TotalTributos
```

```
TributacaoMunicipal
├── tributacaoIssqn: int        # 1=Normal, 2=Imune, 3=Isento, 4=Exportação, 5=Suspensão
├── codigoLocalIncidencia: string
├── aliquota: string            # Ex: "0.0500" (5%)
└── tipoRetencaoBM: int         # 1=Sem retenção, 2=Retido pelo tomador
```

```
TributacaoFederal          # Opcional — apenas se aplicável
├── pisCofins: ?PisCofins
├── irpj: ?Irpj
└── csll: ?Csll
```

```
TotalTributos
├── percentualTotalTributos: string  # Ex: "0.1500"
├── valorTotalTributos: string
└── indicadorTotalTributos: int      # 1=Calculado pela lib, 2=Informado pelo contribuinte
```

---

### 6. `NacionalTransformer`

Serviço sem estado. Converte `Dps` → `array` PHP (serializado depois via `json_encode`).

```
NacionalTransformer
└── transform(Dps $dps): array  # Retorna array pronto para json_encode
    # Throws: TransformException se campo obrigatório ausente
```

**Regra**: O array gerado DEVE satisfazer o JSON Schema oficial da DPS (versão
configurada). O `NacionalTransformer` DEVE ser testado contra o schema via
`justinrainbow/json-schema` ou equivalente nos testes unitários.

---

### 7. Hierarquia de Exceções

```
NacionalException (base, extends \RuntimeException)
├── ValidationException       # HTTP 400, 422 — lista de erros de validação da DPS
│   └── $erros: array<string> # Mensagens do ADN
├── AuthException             # HTTP 401, 403 — falha de mTLS ou permissão
├── NotFoundException         # HTTP 404 — chave de acesso não encontrada
├── AdnException              # HTTP 500, 503 — erro interno / indisponibilidade
│   └── $statusCode: int
└── TimeoutException          # Timeout de rede (cURL timeout)
```

---

### 8. Objetos de Resposta

```
RespostaEmissao
├── protocolo: string           # Protocolo de processamento (HTTP 202)
├── chaveAcesso: ?string        # Preenchido se emissão síncrona (HTTP 201)
├── numeroNfse: ?string
├── status: StatusEmissao       # Enum: ACEITA, EMITIDA, REJEITADA
└── erros: array<string>        # Preenchido se REJEITADA
```

```
RespostaConsulta
├── chaveAcesso: string
├── numeroNfse: string
├── status: StatusNfse          # Enum: ATIVA, CANCELADA, SUBSTITUIDA
├── dpsOriginal: array          # JSON da DPS tal como registrado no ADN
└── dataEmissao: DateTimeImmutable
```

```
RespostaCancelamento
├── protocolo: string
├── dataEvento: DateTimeImmutable
└── status: StatusCancelamento  # Enum: ACEITO, REJEITADO
```

---

### 9. `NacionalClient`

Wrapper do Guzzle. Gerencia ciclo de vida dos arquivos PEM temporários.

```
NacionalClient
├── __construct(ConfiguracaoNacional $config, Certificado $certificado)
├── post(string $endpoint, array $payload): array   # Retorna JSON decodificado
├── get(string $endpoint): array
└── __destruct()  # Remove arquivos PEM temporários
```

---

### 10. `ConfiguracaoNacional`

```
ConfiguracaoNacional
├── ambiente: Ambiente          # PRODUCAO | HOMOLOGACAO
├── urlBase: string             # Derivado de ambiente, mas sobrescrevível
├── timeout: int                # Segundos, default 30
├── versaoSchema: string        # Default "1.00"
└── getUrlAdn(): string         # Retorna urlBase com trailing slash
```

---

### Diagrama de Dependências

```
ConfiguracaoNacional ←── Nacional ──→ NacionalClient
                                  └──→ NacionalTransformer
                                  └──→ [Exceções]

Dps ──────────────────────────────→ NacionalTransformer ──→ array (JSON payload)

NacionalClient ──→ GuzzleHttp\Client
               └──→ Certificado (sped-common)
```

---

### Transições de Estado da NFS-e Nacional

```
DPS enviada
    │
    ├──(HTTP 201)──→ [EMITIDA] ──→ [CANCELADA]
    │
    ├──(HTTP 202)──→ [AGUARDANDO_PROCESSAMENTO]
    │                   │
    │                   └──(consulta)──→ [EMITIDA] ou [REJEITADA]
    │
    └──(HTTP 400/422)──→ [REJEITADA] (não registrada no ADN)
```
