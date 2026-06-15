<?php

namespace NFePHP\NFSe;

/**
 * Classe para a instanciação das classes espcificas de cada municipio
 * atendido pela API
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\NFSeStatic
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\NFSe\Counties;
use RuntimeException;
use stdClass;

class NFSeStatic
{
    /**
     * Instancia a classe usada na conversão dos arquivos txt em RPS
     * Modificada por Tiago Franco para possibilitar
     * uso pelos padroes que ainda não utilizam certificados digitais   
     * @param string $config
     * @return \NFePHP\NFSe\Counties\class
     */
    public static function convert(stdClass $config)
    {
        return self::classCheck(self::getClassName($config, 'Convert'), $config);
    }

    /**
     * Instancia e retorna a classe desejada
     *
     * @param string $className
     * @param stdClass $config
     * @param NFePHP\Common\Certificate|null $certificate
     * @return \NFePHP\NFSe\className
     * @throws RuntimeException
     */
    private static function classCheck($className, stdClass $config, $certificate = null)
    {
        if (class_exists($className)) {
            return new $className($config, $certificate);
        }
        $msg = 'Este municipio não é atendido pela API.';
        throw new RuntimeException($msg);
    }

    /**
     * Monta o nome das classes referentes a determinado municipio
     *
     * @param stdClass $config
     * @param string $complement
     * @return string
     */
    private static function getClassName(stdClass $config, $complement)
    {
        return "\NFePHP\NFSe\Counties\\M$config->cmun\\$complement";
    }

    /**
     * Instancia a classe usada na construção do RPS
     * para um municipio em particular
     *
     * @param stdClass $config
     * @return \NFePHP\NFSe\Counties\class
     */
    public static function rps(stdClass $config)
    {
        return self::classCheck(self::getClassName($config, 'Rps'), $config);
    }

    /**
     * Instancia a classe usada na comunicação com o webservice
     * para um municipio em particular.
     *
     * Reconhece o Padrão Nacional NFS-e (ADN) quando:
     *   - $config->padraoNacional === true, OU
     *   - $config->cmun === '0000000' (código sentinela para o Padrão Nacional)
     *
     * Nesse caso retorna um Nacional configurado via ConfiguracaoNacional.
     * Para todos os demais municípios, mantém o fluxo SOAP legado inalterado.
     *
     * Fontes de certificado (em ordem de preferência):
     *   1. $config->certificadoP12 (binário P12) + $config->senhaCertificado
     *   2. $certificate->writePfx() com senha temporária (fallback quando apenas
     *      o objeto Certificate é fornecido)
     *
     * @param stdClass $config
     * @param NFePHP\Common\Certificate|null $certificate
     * @return \NFePHP\NFSe\Providers\Nacional\Nacional|\NFePHP\NFSe\Counties\class
     */
    public static function tools(stdClass $config, Certificate $certificate = null)
    {
        // Detectar Padrão Nacional antes de resolver Counties/M{cmun}
        if (!empty($config->padraoNacional) || ($config->cmun ?? '') === '0000000') {
            // Fonte 1: P12 raw binary já presente no $config (uso mais comum)
            $certificadoP12   = $config->certificadoP12   ?? '';
            $senhaCertificado = $config->senhaCertificado ?? '';

            // Fonte 2: Certificate object (fallback — reconstrói P12 com senha temporária)
            if ($certificadoP12 === '' && $certificate !== null) {
                $tempPassword     = bin2hex(random_bytes(16));
                $certificadoP12   = $certificate->writePfx($tempPassword);
                $senhaCertificado = $tempPassword;
            }

            return new \NFePHP\NFSe\Providers\Nacional\Nacional(
                new \NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional(
                    certificadoP12:   $certificadoP12,
                    senhaCertificado: $senhaCertificado,
                    ambiente:         (int) ($config->tpAmb   ?? \NFePHP\NFSe\Providers\Nacional\ConfiguracaoNacional::HOMOLOGACAO),
                    timeout:          (int) ($config->timeout  ?? 30),
                )
            );
        }

        // Fluxo SOAP legado — nenhuma alteração
        return self::classCheck(self::getClassName($config, 'Tools'), $config, $certificate);
    }

    /**
     * Instancia a classe que converte o xml de resposta em
     * uma stdClass
     * @return \NFePHP\NFSe\Counties\class
     */
    public static function response(stdClass $config)
    {
        return self::classCheck(self::getClassName($config, 'Response'), $config);
    }
}
