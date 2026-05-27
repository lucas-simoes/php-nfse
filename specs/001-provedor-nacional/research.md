# Research: Provedor Nacional NFS-e (Padrão ADN REST/JSON)

**Feature**: 001-provedor-nacional
**Date**: 2026-05-23

---

## 1. API do ADN (Ambiente de Dados Nacional)

### Decision
Usar a API REST do ADN com base URL configurável para suportar produção
e homologação. Todas as requisições são autenticadas via mTLS (certificado
ICP-Brasil A1).

### Rationale
O governo federal disponibiliza a API em dois ambientes distintos com URLs
fixas. A troca de ambiente é feita por configuração, nunca por código.

### Endpoints confirmados

| Método | Endpoint                                   | Operação            |
|--------|--------------------------------------------|---------------------|
| POST   | `/api/v1/nfse`                             | Emissão de DPS      |
| GET    | `/api/v1/nfse/{chaveAcesso}`               | Consulta de NFS-e   |
| POST   | `/api/v1/nfse/{chaveAcesso}/cancelamento`  | Cancelamento        |
| GET    | `/api/v1/nfse/{chaveAcesso}/pdf`           | Download PDF (futuro) |

### URLs base

- **Produção**: `https://www.nfse.gov.br`
- **Homologação**: `https://hom.nfse.gov.br`

### HTTP Status Codes mapeados

| Status | Significado no ADN                          | Exceção PHP             |
|--------|---------------------------------------------|-------------------------|
| 200    | Sucesso (consulta/cancelamento)             | — (retorno normal)      |
| 201    | NFS-e emitida sincronamente                 | — (retorno normal)      |
| 202    | DPS aceita, processamento assíncrono        | — (retorno com protocolo)|
| 400    | Erros de validação da DPS                   | `ValidationException`   |
| 401    | Falha de autenticação mTLS / token inválido | `AuthException`         |
| 403    | Certificado válido mas sem permissão        | `AuthException`         |
| 404    | NFS-e não encontrada pela chave             | `NotFoundException`     |
| 422    | DPS bem formada mas com inconsistências     | `ValidationException`   |
| 500    | Erro interno do ADN                         | `AdnException`          |
| 503    | ADN indisponível                            | `AdnException`          |

### Alternativas consideradas
- OAuth2 + Bearer token: descartado — o ADN não usa OAuth2; usa mTLS diretamente.
- SOAP envelope com WS-Security: descartado — exclusivo do padrão municipal antigo.

---

## 2. Autenticação mTLS com PHP

### Decision
Usar Guzzle 7.x com opções `curl` para injetar o certificado A1 diretamente
no contexto de cada requisição, reutilizando o certificado já carregado pelo
`sped-common`.

### Rationale
O `sped-common` já extrai chave privada e cadeia de certificados do PFX/P12
em memória. Guzzle aceita o certificado como arquivo temporário (via
`sys_get_temp_dir()`) ou como stream — sem necessidade de escrever
em disco em produção, embora a opção de arquivo temporário seja mais simples
e compatível com a extensão cURL do PHP.

### Implementação

```php
// Configuração Guzzle com mTLS
$client = new \GuzzleHttp\Client([
    'base_uri' => $this->config->getUrlAdn(),
    'timeout'  => $this->config->getTimeout(),
    'curl'     => [
        CURLOPT_SSLCERT    => $certPath,      // PEM com certificado público
        CURLOPT_SSLKEY     => $keyPath,       // PEM com chave privada
        CURLOPT_CAINFO     => $caPath,        // Cadeia ICP-Brasil (opcional)
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ],
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    ],
]);
```

### Gestão de arquivos temporários de certificado
O certificado P12 é extraído para arquivos PEM temporários no início de
cada instância de `NacionalClient`, e os arquivos são removidos no destrutor
(`__destruct`). Arquivos são criados com `tempnam()` e permissões `0600`.

### Alternativas consideradas
- Passar PEM como string diretamente: cURL no PHP requer caminhos de arquivo
  para `CURLOPT_SSLCERT`/`CURLOPT_SSLKEY` (não suporta in-memory em PHP puro);
  arquivos temporários são o padrão estabelecido pelo próprio `sped-common`.
- symfony/http-client: descartado — aumenta dependência sem ganho, Guzzle já
  é usado pelo ecossistema `nfephp-org`.

---

## 3. Padrão do Provedor (sped-nfse Provider Contract)

### Decision
A classe `Nacional` estende uma classe base abstrata (`ToolsBase` ou equivalente)
e implementa a interface de comunicação dos provedores. A Factory existente é
estendida com um case para `"Nacional"` usando o IBGE code `0000000` (código
reservado) ou uma flag explícita de configuração.

### Rationale
O padrão do `sped-nfse` usa classes `Tools.php` por namespace de provedor
(ex: `NFSe\Ginfes\Tools`, `NFSe\Abrasf\Tools`). O novo provedor segue o
mesmo namespace pattern: `NFSe\Nacional\Tools` (ou `NFSe\Nacional\Nacional`
para distinguir da superclasse `Tools`).

### Interface mínima dos provedores existentes

```php
interface ProviderInterface {
    public function emitir(/* DPS ou RPS */): RespostaInterface;
    public function consultar(string $identificador): RespostaInterface;
    public function cancelar(string $identificador, string $motivo): RespostaInterface;
}
```

### Factory pattern
```php
// Adição no Factory existente:
case 'Nacional':
case 0: // código IBGE especial para "nacional"
    return new Nacional($config, $certificado);
```

### Alternativas consideradas
- Criar interface separada `NacionalInterface`: descartado — viola Princípio II
  (Library-First Modularity) e quebra compatibilidade com código legado que
  usa a interface existente.

---

## 4. DPS — Declaração de Prestação de Serviço (JSON Schema)

### Decision
Implementar a classe `Dps` como value object imutável com builder, e o
`NacionalTransformer` como serviço de serialização pura (sem estado) que
converte `Dps` → array PHP → `json_encode()`.

### Rationale
O schema DPS é definido no Manual de Orientação ao Contribuinte (MOC) da
NFS-e Nacional, versionado pelo governo federal. A separação entre o modelo
de dados (`Dps`) e o serializador (`NacionalTransformer`) garante que
alterações de schema (novas versões do MOC) não afetem a API pública da
biblioteca (Princípio I + Princípio II).

### Estrutura raiz do JSON DPS

```json
{
  "infDPS": {
    "Id": "DPS[CNPJ][IM][ANO][MES][NRO_DPS]",
    "tpAmb": 2,
    "dhEmi": "2026-05-23T10:00:00-03:00",
    "verAplic": "1.00",
    "dCompet": "2026-05",
    "subst": null,
    "emit": { ... },
    "tomador": { ... },
    "serv": { ... },
    "valores": { ... }
  }
}
```

### Campos obrigatórios mapeados (ver data-model.md para detalhe)

| Bloco    | Campos chave obrigatórios                                  |
|----------|------------------------------------------------------------|
| `emit`   | CNPJ, IM, CRT, regTrib.opSimpNac, regTrib.CNAE, regTrib.cLocEmi |
| `tomador`| CNPJ ou CPF, enderTomador completo                         |
| `serv`   | cServ.cTribNac, cServ.cTribMun, cServ.CNAE, xDescServ, loc |
| `valores`| vServPrest.vReceb, trib.tribMun.tribISSQN, trib.tribMun.pAliq |

### Assinatura digital
Para endpoints que exigem payload assinado (eventos de cancelamento e DPS de
alto risco), a assinatura é gerada com `openssl_sign()` sobre o JSON canônico
serializado, codificada em Base64, e adicionada como campo `signature` no
body ou como header `X-Signature`. O `sped-common` provê as primitivas
OpenSSL necessárias.

### Alternativas consideradas
- XML → JSON bridge: descartar completamente o modelo RPS em XML e usar DPS
  JSON nativo desde o início. Decidido: DPS é um novo modelo, sem conversão
  de XML legado (Princípio IV — YAGNI).
- Usar `spatie/data-transfer-object` ou similar: descartado — adiciona
  dependência sem necessidade; PHP 8.1 readonly properties + named arguments
  são suficientes.

---

## 5. Versionamento e Evolução do Schema DPS

### Decision
A versão do schema DPS (`verAplic`) é configurável e padrão `"1.00"`.
Quando o governo federal publicar novos schemas, uma nova versão MINOR da
biblioteca acompanha, com CHANGELOG obrigatório.

### Rationale
Alinha com Princípio V (Semantic Versioning). Mudanças obrigatórias de schema
são MINOR (nova funcionalidade backward-compatible). Remoção de campos seria
MAJOR.

---

## Resumo de Decisões

| Área | Decisão |
|------|---------|
| Transporte HTTP | Guzzle 7.x com CURLOPT_SSLCERT/SSLKEY |
| Autenticação | mTLS com certificado ICP-Brasil A1 (P12 → PEM temporário) |
| Serialização DPS | `NacionalTransformer` stateless, DPS value object imutável |
| Provider pattern | Namespace `NFSe\Nacional`, mesma interface dos provedores existentes |
| Exceções | Hierarquia tipada: `NacionalException` → 5 subclasses por categoria HTTP |
| Assinatura digital | `openssl_sign()` do sped-common quando exigido pelo endpoint |
| Versionamento schema | Campo `verAplic` configurável, MINOR bump no SemVer por nova versão MOC |
