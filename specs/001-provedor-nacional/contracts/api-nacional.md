# API Contract: ADN (Ambiente de Dados Nacional)

**Tipo**: REST/JSON — API externa consumida pela biblioteca
**Versão do schema DPS**: 1.00
**Date**: 2026-05-23

---

## Autenticação

Todas as requisições DEVEM usar mTLS com certificado ICP-Brasil A1.
O certificado é injetado via `CURLOPT_SSLCERT` / `CURLOPT_SSLKEY`.

Headers obrigatórios em todas as requisições:

```
Content-Type: application/json
Accept: application/json
```

---

## Endpoints

### POST /api/v1/nfse — Emissão de DPS

**Request Body**:

```json
{
  "infDPS": {
    "Id": "DPS12345678000195000123202605230000000001",
    "tpAmb": 2,
    "dhEmi": "2026-05-23T10:00:00-03:00",
    "verAplic": "1.00",
    "dCompet": "2026-05",
    "subst": null,
    "emit": {
      "CNPJ": "12345678000195",
      "IM": "000123",
      "CRT": 1,
      "regTrib": {
        "opSimpNac": 1,
        "CNAE": "6201501",
        "cLocEmi": "3550308"
      }
    },
    "tomador": {
      "CNPJ": "98765432000100",
      "enderTomador": {
        "xLgr": "Rua das Flores",
        "nro": "123",
        "xBairro": "Centro",
        "cMun": "3550308",
        "UF": "SP",
        "CEP": "01310100",
        "xPais": "BRASIL",
        "cPais": "1058"
      }
    },
    "serv": {
      "cServ": {
        "cTribNac": "010700",
        "cTribMun": "12.03",
        "CNAE": "6201501",
        "xDescServ": "Desenvolvimento de sistemas de informação"
      },
      "compl": {
        "xCompl": "Sistema de gestão empresarial"
      },
      "loc": {
        "cLocPrestacao": "3550308",
        "cPaisPrestacao": "1058"
      }
    },
    "valores": {
      "vServPrest": {
        "vReceb": "1000.00",
        "vDesc": "0.00"
      },
      "pTotMaior": {
        "vLiq": "1000.00",
        "vCarga": "50.00",
        "pCargaTrib": "0.0500"
      },
      "trib": {
        "tribMun": {
          "tribISSQN": 1,
          "cLocIncid": "3550308",
          "pAliq": "0.0500",
          "tpRetBM": 1
        },
        "totTrib": {
          "pTotTrib": "0.0500",
          "vTotTrib": "50.00",
          "indTotTrib": 1
        }
      }
    }
  }
}
```

**Response 201** (emissão síncrona):

```json
{
  "nfse": {
    "chaveAcesso": "35260512345678000195000123000000001000000001",
    "numero": "000000001",
    "dataEmissao": "2026-05-23T10:00:05-03:00",
    "protocolo": "2620260523100000001"
  }
}
```

**Response 202** (processamento assíncrono):

```json
{
  "protocolo": "2620260523100000001",
  "mensagem": "DPS recebida e em processamento"
}
```

**Response 400** (erros de validação):

```json
{
  "erros": [
    {
      "codigo": "E001",
      "mensagem": "CNPJ do emitente inválido",
      "campo": "infDPS.emit.CNPJ"
    },
    {
      "codigo": "E045",
      "mensagem": "Código de tributação nacional não encontrado",
      "campo": "infDPS.serv.cServ.cTribNac"
    }
  ]
}
```

---

### GET /api/v1/nfse/{chaveAcesso} — Consulta de NFS-e

**Path Param**: `chaveAcesso` — chave de 44 dígitos da NFS-e

**Response 200**:

```json
{
  "nfse": {
    "chaveAcesso": "35260512345678000195000123000000001000000001",
    "numero": "000000001",
    "status": "Ativa",
    "dataEmissao": "2026-05-23T10:00:05-03:00",
    "dps": { /* objeto DPS original como enviado */ }
  }
}
```

**Response 404**:

```json
{
  "erro": "NFS-e não encontrada para a chave informada"
}
```

---

### POST /api/v1/nfse/{chaveAcesso}/cancelamento — Cancelamento

**Path Param**: `chaveAcesso` — chave de 44 dígitos

**Request Body**:

```json
{
  "infEvento": {
    "cOrgao": "SP",
    "tpAmb": 2,
    "CNPJ": "12345678000195",
    "chNFSe": "35260512345678000195000123000000001000000001",
    "dhEvento": "2026-05-23T15:00:00-03:00",
    "nSeqEvento": 1,
    "tpEvento": "010100",
    "verEvento": "1.00",
    "detEvento": {
      "cMotivo": "1",
      "xMotivo": "Erro na emissão"
    }
  }
}
```

**Response 200** (cancelamento aceito):

```json
{
  "evento": {
    "protocolo": "2620260523150000001",
    "dataEvento": "2026-05-23T15:00:08-03:00",
    "status": "Aceito"
  }
}
```

**Response 400** (cancelamento rejeitado):

```json
{
  "erros": [
    {
      "codigo": "E200",
      "mensagem": "Prazo para cancelamento expirado"
    }
  ]
}
```

---

## Chave de Acesso — Estrutura

A chave de acesso da NFS-e Nacional tem 44 dígitos:

```
[cUF 2d][AAAA 4d][MM 2d][CNPJ 14d][IM 6-15d][nNFSe 10d][cNF 8d][cDV 1d]
```

Exemplo: `35 2026 05 12345678000195 000123 0000000001 00000001 5`

---

## Códigos de Motivo de Cancelamento

| Código | Descrição                        |
|--------|----------------------------------|
| 1      | Erro na emissão                  |
| 2      | Serviço não prestado             |
| 3      | Duplicidade de nota              |
| 4      | Erro de tributação               |
