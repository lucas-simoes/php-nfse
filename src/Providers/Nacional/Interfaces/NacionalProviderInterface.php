<?php

declare(strict_types=1);

namespace NFePHP\NFSe\Providers\Nacional\Interfaces;

use NFePHP\NFSe\Providers\Nacional\Models\Dps;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaEmissao;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaConsulta;
use NFePHP\NFSe\Providers\Nacional\Responses\RespostaCancelamento;
use NFePHP\NFSe\Providers\Nacional\Exceptions\NacionalException;

interface NacionalProviderInterface
{
    /**
     * Emite uma NFS-e pelo Padrão Nacional (ADN).
     *
     * @throws NacionalException subclasses: ValidationException, AuthException, AdnException
     */
    public function emitir(Dps $dps): RespostaEmissao;

    /**
     * Consulta uma NFS-e pelo padrão nacional.
     *
     * @throws NacionalException subclasses: NotFoundException, AuthException, AdnException
     */
    public function consultar(string $chaveAcesso): RespostaConsulta;

    /**
     * Cancela uma NFS-e emitida pelo padrão nacional.
     *
     * @throws NacionalException subclasses: ValidationException, NotFoundException, AuthException, AdnException
     */
    public function cancelar(string $chaveAcesso, string $codigoMotivo): RespostaCancelamento;
}
