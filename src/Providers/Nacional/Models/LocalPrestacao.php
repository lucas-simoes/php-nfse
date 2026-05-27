<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Local onde o serviço foi prestado.
 */
class LocalPrestacao
{
    public function __construct(
        /** Código IBGE do município de prestação (7 dígitos) */
        public readonly string $codigoLocalPrestacao,
        /** "1058" para Brasil */
        public readonly string $codigoPais,
    ) {
    }
}
