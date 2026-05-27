<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Valores monetários do serviço prestado (vServPrest).
 * Valores como string decimal: "1000.00"
 */
class ValorServico
{
    public function __construct(
        /** Valor recebido pelo serviço */
        public readonly string $valorRecebido,
        /** Valor de desconto concedido */
        public readonly string $valorDesconto,
    ) {
    }
}
