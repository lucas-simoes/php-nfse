<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional;

use NFePHP\NFSe\Providers\Nacional\Interfaces\NacionalTransformerInterface;
use NFePHP\NFSe\Providers\Nacional\Models\Dps;

/**
 * Serializa Dps → array PHP pronto para json_encode() no formato ADN.
 *
 * Implementação completa será feita em T022 (US1).
 * Este stub permite que Nacional.php compile sem erros.
 */
class NacionalTransformer implements NacionalTransformerInterface
{
    /**
     * Converte DPS em array com estrutura infDPS conforme contracts/api-nacional.md.
     *
     * @return array<string, mixed>
     * @throws \BadMethodCallException até T022 ser implementado
     */
    public function transform(Dps $dps): array
    {
        throw new \BadMethodCallException('NacionalTransformer::transform() not implemented — aguardando T022');
    }
}
