<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Bloco de valores da DPS (valores).
 * Possui builder estático para fluência na criação.
 */
class Valores
{
    public function __construct(
        public readonly ValorServico    $valorServicoPrestado,
        public readonly TotalMaior      $totalMaior,
        public readonly Tributacao      $tributacao,
    ) {
    }

    public static function builder(): ValoresBuilder
    {
        return new ValoresBuilder();
    }
}
