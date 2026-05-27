<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Dados do emitente da NFS-e.
 */
class Emitente
{
    public function __construct(
        /** CNPJ do emitente — 14 dígitos, somente números */
        public readonly string            $cnpj,
        /** Inscrição municipal no município de emissão */
        public readonly string            $inscricaoMunicipal,
        /** CRT: 1=Simples Nacional, 2=Lucro Presumido, 3=Lucro Real */
        public readonly int               $codigoRegimeTributario,
        public readonly RegimeTributario  $regimeTributario,
    ) {
    }
}
