# Quickstart: Provedor Nacional NFS-e

**Feature**: 001-provedor-nacional
**Date**: 2026-05-23

---

## Pré-requisitos

- PHP 7.4+ ou PHP 8.1+ (recomendado)
- Extensão `openssl` habilitada
- Extensão `curl` habilitada com suporte a SSL/TLS
- Composer instalado
- Certificado digital ICP-Brasil A1 (.pfx / .p12) válido
- Acesso ao ambiente de homologação do ADN (`hom.nfse.gov.br`)

---

## Instalação

```bash
# O pacote já faz parte do fork — nenhuma instalação adicional necessária.
# Para instalar as dependências:
composer install
```

Dependência principal adicionada automaticamente:
```json
{
  "require": {
    "guzzlehttp/guzzle": "^7.0"
  }
}
```

---

## Fluxo mínimo: Emitir uma NFS-e pelo Padrão Nacional

### 1. Configurar o provedor

```php
<?php

require 'vendor/autoload.php';

use NFSe\Nacional\Nacional;
use NFSe\Nacional\ConfiguracaoNacional;

$config = new ConfiguracaoNacional(
    certificadoP12: file_get_contents(__DIR__ . '/certs/empresa.pfx'),
    senhaCertificado: getenv('CERT_PASSWORD'),
    ambiente: ConfiguracaoNacional::HOMOLOGACAO, // trocar para PRODUCAO em produção
    timeout: 30,
);

$provider = new Nacional($config);
```

### 2. Montar a DPS

```php
<?php

use NFSe\Nacional\Models\Dps;
use NFSe\Nacional\Models\Emitente;
use NFSe\Nacional\Models\RegimeTributario;
use NFSe\Nacional\Models\Tomador;
use NFSe\Nacional\Models\Endereco;
use NFSe\Nacional\Models\Servico;
use NFSe\Nacional\Models\CodigoServico;
use NFSe\Nacional\Models\LocalPrestacao;
use NFSe\Nacional\Models\Valores;
use NFSe\Nacional\Models\ValorServico;
use NFSe\Nacional\Models\TributacaoMunicipal;

$emitente = new Emitente(
    cnpj: '12345678000195',
    inscricaoMunicipal: '000123',
    codigoRegimeTributario: 1, // Simples Nacional
    regimeTributario: new RegimeTributario(
        opcaoSimplesNacional: 1,
        cnae: '6201501',
        codigoLocalEmissao: '3550308', // IBGE São Paulo
    ),
);

$tomador = new Tomador(
    cnpj: '98765432000100',
    endereco: new Endereco(
        logradouro: 'Rua das Flores',
        numero: '123',
        bairro: 'Centro',
        codigoMunicipio: '3550308',
        uf: 'SP',
        cep: '01310100',
        nomePais: 'BRASIL',
        codigoPais: '1058',
    ),
);

$servico = new Servico(
    codigoServico: new CodigoServico(
        codigoTributacaoNacional: '010700',
        codigoTributacaoMunicipal: '12.03',
        cnae: '6201501',
        descricaoServico: 'Desenvolvimento de sistemas de informação',
    ),
    localPrestacao: new LocalPrestacao(
        codigoLocalPrestacao: '3550308',
        codigoPais: '1058',
    ),
);

$valores = Valores::builder()
    ->valorRecebido('1000.00')
    ->aliquotaIss('0.0500')            // ISS 5%
    ->localIncidenciaIss('3550308')
    ->build();

$dps = Dps::builder()
    ->emitente($emitente)
    ->tomador($tomador)
    ->servico($servico)
    ->valores($valores)
    ->competencia('2026-05')
    ->ambiente(ConfiguracaoNacional::HOMOLOGACAO)
    ->build();
```

### 3. Emitir

```php
<?php

use NFSe\Nacional\Exceptions\ValidationException;
use NFSe\Nacional\Exceptions\AuthException;
use NFSe\Nacional\Exceptions\NacionalException;

try {
    $resposta = $provider->emitir($dps);

    if ($resposta->foiEmitida()) {
        echo "NFS-e emitida!" . PHP_EOL;
        echo "Número: " . $resposta->numeroNfse . PHP_EOL;
        echo "Chave:  " . $resposta->chaveAcesso . PHP_EOL;
    } else {
        // HTTP 202 — processamento assíncrono
        echo "DPS aceita. Protocolo: " . $resposta->protocolo . PHP_EOL;
        echo "Consulte a situação em alguns instantes." . PHP_EOL;
    }

} catch (ValidationException $e) {
    echo "Erros de validação da DPS:" . PHP_EOL;
    foreach ($e->getErros() as $erro) {
        echo "  [{$erro['codigo']}] {$erro['mensagem']}" . PHP_EOL;
        if (isset($erro['campo'])) {
            echo "  Campo: {$erro['campo']}" . PHP_EOL;
        }
    }

} catch (AuthException $e) {
    echo "Falha de autenticação: " . $e->getMessage() . PHP_EOL;
    echo "Verifique o certificado e sua validade." . PHP_EOL;

} catch (NacionalException $e) {
    echo "Erro na integração com ADN: " . $e->getMessage() . PHP_EOL;
}
```

---

## Consultar uma NFS-e

```php
<?php

$chaveAcesso = '35260512345678000195000123000000001000000001';

$nota = $provider->consultar($chaveAcesso);

echo "Número: " . $nota->numeroNfse . PHP_EOL;
echo "Status: " . $nota->status    . PHP_EOL;
echo "Emitida em: " . $nota->dataEmissao->format('d/m/Y H:i') . PHP_EOL;
```

---

## Cancelar uma NFS-e

```php
<?php

$resposta = $provider->cancelar(
    chaveAcesso: '35260512345678000195000123000000001000000001',
    codigoMotivo: '1', // 1=Erro na emissão
);

if ($resposta->foiAceito()) {
    echo "Cancelamento aceito. Protocolo: " . $resposta->protocolo . PHP_EOL;
} else {
    echo "Cancelamento rejeitado." . PHP_EOL;
}
```

---

## Trocar para produção

```php
$config = new ConfiguracaoNacional(
    certificadoP12: file_get_contents('/secure/cert.pfx'),
    senhaCertificado: getenv('CERT_PASSWORD'),
    ambiente: ConfiguracaoNacional::PRODUCAO, // ← único change necessário
);
```

---

## Validação de saúde antes de produção

```bash
# Executar suite de testes de integração contra homologação
php vendor/bin/phpunit tests/Integration/ --group=nacional

# Validar schema DPS contra JSON Schema oficial
php vendor/bin/phpunit tests/Unit/NacionalTransformerTest.php

# Análise estática
php vendor/bin/phpstan analyse src/Providers/Nacional/ --level=8
```

---

## Solução de problemas comuns

| Sintoma | Causa provável | Ação |
|---------|---------------|------|
| `AuthException` imediato | Certificado expirado ou senha errada | Verificar validade e senha do P12 |
| `TimeoutException` | Rede bloqueada ou ADN indisponível | Verificar firewall (saída 443) e status `hom.nfse.gov.br` |
| `ValidationException` com campo `infDPS.emit.CNPJ` | CNPJ não formatado (14 dígitos sem pontuação) | Remover máscara antes de passar à `Dps` |
| HTTP 202 persistente | ADN em processamento assíncrono | Aguardar 30–60s e chamar `consultar()` |
| `cURL error 60` | Cadeia CA do ADN não reconhecida | Atualizar `cacert.pem` do PHP ou configurar `caInfo` no config |
