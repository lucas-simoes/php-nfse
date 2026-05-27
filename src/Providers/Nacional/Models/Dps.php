<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Declaração de Prestação de Serviço (DPS) — documento de entrada para emitir NFS-e.
 *
 * Construído via builder estático: Dps::builder()->...->build()
 * Validações no construtor/builder serão adicionadas em T021 (US1).
 */
class Dps
{
    public function __construct(
        public readonly string             $id,
        public readonly int                $ambiente,
        public readonly \DateTimeImmutable $dataEmissao,
        public readonly string             $competencia,
        public readonly string             $versaoAplicacao,
        public readonly ?Substituicao      $substituicao,
        public readonly Emitente           $emitente,
        public readonly Tomador            $tomador,
        public readonly Servico            $servico,
        public readonly Valores            $valores,
    ) {
    }

    public static function builder(): DpsBuilder
    {
        return new DpsBuilder();
    }
}
