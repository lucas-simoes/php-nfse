<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Informações de substituição de NFS-e (campo opcional na DPS).
 */
class Substituicao
{
    public function __construct(
        public readonly string $chaveAcessoSubstituida,
        public readonly string $codigoMotivo,
    ) {
    }
}
