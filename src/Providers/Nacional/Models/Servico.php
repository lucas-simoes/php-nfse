<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Bloco de serviço da DPS (serv).
 */
class Servico
{
    public function __construct(
        public readonly CodigoServico      $codigoServico,
        public readonly ?ComplementoServico $complemento,
        public readonly LocalPrestacao     $localPrestacao,
    ) {
    }
}
