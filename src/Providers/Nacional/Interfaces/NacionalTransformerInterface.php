<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Interfaces;

use NFePHP\NFSe\Providers\Nacional\Models\Dps;

interface NacionalTransformerInterface
{
    /**
     * Converte Dps para array PHP pronto para json_encode().
     * O array resultante DEVE satisfazer o JSON Schema DPS nacional.
     *
     * @return array<string, mixed>
     * @throws \NFePHP\NFSe\Providers\Nacional\Exceptions\NacionalException
     */
    public function transform(Dps $dps): array;
}
