<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Models;

/**
 * Endereço do tomador do serviço.
 */
class Endereco
{
    public function __construct(
        public readonly string  $logradouro,
        public readonly string  $numero,
        public readonly ?string $complemento,
        public readonly string  $bairro,
        /** Código IBGE do município (7 dígitos) */
        public readonly string  $codigoMunicipio,
        /** Sigla do estado (2 letras) */
        public readonly string  $uf,
        /** CEP — 8 dígitos, somente números */
        public readonly string  $cep,
        /** Ex: "BRASIL" */
        public readonly string  $nomePais,
        /** Ex: "1058" (Brasil) */
        public readonly string  $codigoPais,
    ) {
    }
}
