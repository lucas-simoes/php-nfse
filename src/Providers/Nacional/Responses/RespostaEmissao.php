<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Responses;

/**
 * Resposta da operação de emissão de NFS-e via ADN.
 *
 * status possíveis: 'EMITIDA' (HTTP 201), 'ACEITA' (HTTP 202), 'REJEITADA' (HTTP 400/422)
 *
 * NÃO usa readonly class (PHP 8.2+) — usa readonly properties (PHP 8.1).
 */
class RespostaEmissao
{
    public function __construct(
        /** Protocolo de processamento (preenchido em HTTP 202) */
        public readonly string  $protocolo,
        /** 'EMITIDA' | 'ACEITA' | 'REJEITADA' */
        public readonly string  $status,
        /** Chave de acesso da NFS-e (preenchida em HTTP 201) */
        public readonly ?string $chaveAcesso,
        /** Número da NFS-e (preenchido em HTTP 201) */
        public readonly ?string $numeroNfse,
        /** @var array<string> Lista de erros (preenchida em REJEITADA) */
        public readonly array   $erros,
    ) {
    }

    /**
     * Retorna true se a NFS-e foi emitida sincronamente (HTTP 201).
     */
    public function foiEmitida(): bool
    {
        return $this->status === 'EMITIDA';
    }

    /**
     * Retorna true se a DPS foi aceita para processamento assíncrono (HTTP 202).
     */
    public function estaEmProcessamento(): bool
    {
        return $this->status === 'ACEITA';
    }
}
