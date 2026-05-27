# PHP Interface Contract: Provedor Nacional NFS-e

**Tipo**: Contratos PHP públicos expostos pela biblioteca
**Date**: 2026-05-23

---

## Interface Principal do Provedor

```php
<?php

namespace NFSe\Nacional;

use NFSe\Nacional\Models\Dps;
use NFSe\Nacional\Responses\RespostaEmissao;
use NFSe\Nacional\Responses\RespostaConsulta;
use NFSe\Nacional\Responses\RespostaCancelamento;
use NFSe\Nacional\Exceptions\NacionalException;

interface NacionalProviderInterface
{
    /**
     * Emite uma NFS-e pelo Padrão Nacional (ADN).
     *
     * @throws NacionalException subclasses: ValidationException, AuthException, AdnException
     */
    public function emitir(Dps $dps): RespostaEmissao;

    /**
     * Consulta uma NFS-e pelo padrão nacional.
     *
     * @throws NacionalException subclasses: NotFoundException, AuthException, AdnException
     */
    public function consultar(string $chaveAcesso): RespostaConsulta;

    /**
     * Cancela uma NFS-e emitida pelo padrão nacional.
     *
     * @throws NacionalException subclasses: ValidationException, NotFoundException, AuthException, AdnException
     */
    public function cancelar(string $chaveAcesso, string $codigoMotivo): RespostaCancelamento;
}
```

---

## NacionalTransformerInterface

```php
<?php

namespace NFSe\Nacional;

use NFSe\Nacional\Models\Dps;

interface NacionalTransformerInterface
{
    /**
     * Converte Dps para array PHP pronto para json_encode().
     * O array resultante DEVE satisfazer o JSON Schema DPS nacional.
     *
     * @return array<string, mixed>
     * @throws \NFSe\Nacional\Exceptions\TransformException
     */
    public function transform(Dps $dps): array;
}
```

---

## Hierarquia de Exceções Pública

```php
<?php namespace NFSe\Nacional\Exceptions;

// Base — captura todos os erros da integração nacional
class NacionalException extends \RuntimeException {}

// HTTP 400, 422 — erros de validação da DPS retornados pelo ADN
class ValidationException extends NacionalException
{
    /** @return array<int, array{codigo: string, mensagem: string, campo?: string}> */
    public function getErros(): array;
}

// HTTP 401, 403 — falha de autenticação ou permissão
class AuthException extends NacionalException {}

// HTTP 404 — NFS-e não encontrada pela chave de acesso
class NotFoundException extends NacionalException {}

// HTTP 500, 503 — erro interno do ADN ou indisponibilidade
class AdnException extends NacionalException
{
    public function getStatusCode(): int;
}

// Timeout de rede (cURL timeout excedido)
class TimeoutException extends NacionalException {}
```

---

## Objetos de Resposta (Value Objects, somente leitura)

### RespostaEmissao

```php
<?php namespace NFSe\Nacional\Responses;

readonly class RespostaEmissao
{
    public function __construct(
        public readonly string  $protocolo,
        public readonly string  $status,          // 'EMITIDA' | 'ACEITA' | 'REJEITADA'
        public readonly ?string $chaveAcesso,
        public readonly ?string $numeroNfse,
        /** @var array<string> */
        public readonly array   $erros,
    ) {}

    public function foiEmitida(): bool;
    public function estaEmProcessamento(): bool;
}
```

### RespostaConsulta

```php
<?php namespace NFSe\Nacional\Responses;

readonly class RespostaConsulta
{
    public function __construct(
        public readonly string             $chaveAcesso,
        public readonly string             $numeroNfse,
        public readonly string             $status,       // 'Ativa' | 'Cancelada' | 'Substituida'
        public readonly \DateTimeImmutable $dataEmissao,
        /** @var array<string, mixed> */
        public readonly array              $dpsOriginal,
    ) {}
}
```

### RespostaCancelamento

```php
<?php namespace NFSe\Nacional\Responses;

readonly class RespostaCancelamento
{
    public function __construct(
        public readonly string             $protocolo,
        public readonly \DateTimeImmutable $dataEvento,
        public readonly string             $status,    // 'Aceito' | 'Rejeitado'
    ) {}

    public function foiAceito(): bool;
}
```

---

## ConfiguracaoNacional — API pública

```php
<?php namespace NFSe\Nacional;

class ConfiguracaoNacional
{
    public function __construct(
        private readonly string $certificadoP12,   // Conteúdo binário do P12
        private readonly string $senhaCertificado,
        private readonly int    $ambiente = 2,     // 1=Producao, 2=Homologacao
        private readonly int    $timeout  = 30,
        private readonly string $versaoSchema = '1.00',
    ) {}

    public function getUrlBase(): string;
    public function getAmbiente(): int;
    public function getTimeout(): int;
    public function getVersaoSchema(): string;
    public function getCertificadoP12(): string;
    public function getSenhaCertificado(): string;
}
```

---

## Uso mínimo (contrato de integração)

```php
<?php

use NFSe\Nacional\Nacional;
use NFSe\Nacional\ConfiguracaoNacional;
use NFSe\Nacional\Models\Dps;
use NFSe\Nacional\Exceptions\ValidationException;
use NFSe\Nacional\Exceptions\NacionalException;

// 1. Configurar
$config = new ConfiguracaoNacional(
    certificadoP12: file_get_contents('/caminho/certificado.pfx'),
    senhaCertificado: 'senha-secreta',
    ambiente: 2, // homologação
);

// 2. Instanciar o provedor
$provider = new Nacional($config);

// 3. Montar a DPS (ver data-model.md para estrutura completa)
$dps = Dps::builder()
    ->emitente($emitente)
    ->tomador($tomador)
    ->servico($servico)
    ->valores($valores)
    ->competencia('2026-05')
    ->build();

// 4. Emitir
try {
    $resposta = $provider->emitir($dps);

    if ($resposta->foiEmitida()) {
        echo "NFS-e: " . $resposta->numeroNfse;
    } else {
        echo "Em processamento: " . $resposta->protocolo;
    }

} catch (ValidationException $e) {
    foreach ($e->getErros() as $erro) {
        echo $erro['mensagem'] . PHP_EOL;
    }
} catch (NacionalException $e) {
    echo "Erro: " . $e->getMessage();
}
```

---

## Compatibilidade com Factory existente

A Factory do projeto deve reconhecer o código IBGE `0000000` (reservado) ou
a flag de configuração `'padraoNacional' => true` para retornar `Nacional`:

```php
// No Factory existente:
if ($config->isPadraoNacional() || $config->getCodigoIbge() === '0000000') {
    return new Nacional($config->getNacionalConfig());
}
```
