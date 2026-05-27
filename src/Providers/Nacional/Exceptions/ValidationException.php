<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Exceptions;

/**
 * HTTP 400, 422 — erros de validação da DPS retornados pelo ADN.
 */
class ValidationException extends NacionalException
{
    /** @var array<int, array{codigo: string, mensagem: string, campo?: string}> */
    private array $erros;

    /**
     * @param array<int, array{codigo: string, mensagem: string, campo?: string}> $erros
     */
    public function __construct(array $erros, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->erros = $erros;
        if ($message === '') {
            $message = 'Erros de validação: ' . implode('; ', array_column($erros, 'mensagem'));
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<int, array{codigo: string, mensagem: string, campo?: string}>
     */
    public function getErros(): array
    {
        return $this->erros;
    }
}
