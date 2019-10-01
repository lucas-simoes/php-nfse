<?php

namespace NFePHP\NFSe\Common;

class DateTime
{
    public static $tzUFlist = [
        'AC' => 'America/Rio_Branco',
        'AL' => 'America/Maceio',
        'AM' => 'America/Manaus',
        'AP' => 'America/Belem',
        'BA' => 'America/Bahia',
        'CE' => 'America/Fortaleza',
        'DF' => 'America/Sao_Paulo',
        'ES' => 'America/Sao_Paulo',
        'GO' => 'America/Sao_Paulo',
        'MA' => 'America/Fortaleza',
        'MG' => 'America/Sao_Paulo',
        'MS' => 'America/Campo_Grande',
        'MT' => 'America/Cuiaba',
        'PA' => 'America/Belem',
        'PB' => 'America/Fortaleza',
        'PE' => 'America/Recife',
        'PI' => 'America/Fortaleza',
        'PR' => 'America/Sao_Paulo',
        'RJ' => 'America/Sao_Paulo',
        'RN' => 'America/Fortaleza',
        'RO' => 'America/Porto_Velho',
        'RR' => 'America/Boa_Vista',
        'RS' => 'America/Sao_Paulo',
        'SC' => 'America/Sao_Paulo',
        'SE' => 'America/Maceio',
        'SP' => 'America/Sao_Paulo',
        'TO' => 'America/Araguaina'
    ];

    public static function tzdBR($siglaUF)
    {
        if ($siglaUF == '' || !isset(self::$tzUFlist[$siglaUF])) {
            return;
        }
        date_default_timezone_set(self::$tzUFlist[$siglaUF]);
        return new \DateTimeZone(self::$tzUFlist[$siglaUF]);
    }
}
