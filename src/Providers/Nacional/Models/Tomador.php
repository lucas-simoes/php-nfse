<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Dados do tomador do serviço.
 *
 * Invariante: exatamente um de cnpj, cpf ou nifEstrangeiro deve ser não-nulo.
 * Lança \InvalidArgumentException caso contrário.
 */
class Tomador
{
    public function __construct(
        /** CNPJ do tomador — 14 dígitos (exclusivo com cpf e nifEstrangeiro) */
        public readonly ?string  $cnpj,
        /** CPF do tomador — 11 dígitos (exclusivo com cnpj e nifEstrangeiro) */
        public readonly ?string  $cpf,
        /** NIF para tomadores estrangeiros (exclusivo com cnpj e cpf) */
        public readonly ?string  $nifEstrangeiro,
        public readonly ?string  $inscricaoMunicipal,
        public readonly Endereco $endereco,
    ) {
        $identificadores = array_filter([$cnpj, $cpf, $nifEstrangeiro], fn ($v) => $v !== null);

        if (count($identificadores) !== 1) {
            throw new \InvalidArgumentException(
                'Tomador exige exatamente um identificador: cnpj, cpf ou nifEstrangeiro. '
                . count($identificadores) . ' informado(s).'
            );
        }
    }
}
