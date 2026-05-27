<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Exceptions;

/**
 * HTTP 500, 503 — erro interno do ADN ou indisponibilidade do serviço.
 */
class AdnException extends NacionalException
{
    private int $statusCode;

    public function __construct(int $statusCode, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        if ($message === '') {
            $message = 'Erro interno do ADN. HTTP ' . $statusCode;
        }
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
