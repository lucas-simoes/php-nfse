<?php

namespace NFePHP\NFSe\Models\Prodam;

/**
 * Classe para a conversão do TXT dos RPS
 * para o Objeto RPS no modelo PRODAM
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Prodam\Convert
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use InvalidArgumentException;
use NFePHP\Common\Strings;

class Convert
{
    protected static $aRps = array();
    protected static $tipo = 0;
    protected static $item = 0;
    protected static $contTipos = [1 => 0, 2 => 0, 3 => 0, 5 => 0, 6 => 0, 9 => 0];
    protected static $numRps = 0;
    protected static $f1 = [];
    protected static $f2 = [];
    protected static $f3 = [];
    protected static $f5 = [];
    protected static $f6 = [];
    protected static $f9 = [];

    protected static $bF = [
        ['tipo', 1, 'N', 0],//1
        ['tpRPS', 5, 'C', ''],//2
        ['serie', 5, 'N', 0],//3
        ['numero', 12, 'N', 0],//4
        ['dtEmi', 8, 'D', 'Y-m-d'],//5
        ['situacao', 1, 'C', ''],//6
        ['valor', 15, 'N', 2],//7
        ['deducoes', 15, 'N', 2],//8
        ['codigo', 5, 'N', 0],//9
        ['aliquota', 4, 'N', 4],//10
        ['issRetido', 1, 'C', ''],//11
        ['indTomador', 1, 'N', 0],//12
        ['cnpjcpfTomador', 14, 'N', 0]
    ];

    protected static $f1Fields = [
        ['tipo', 1, 'N', 0],
        ['versao', 3, 'N', 0],
        ['prestadorIM', 8, 'N', 0],
        ['dtIni', 8, 'D', 'Y-m-d'],
        ['dtFim', 8, 'D', 'Y-m-d']
    ];

    protected static $f2FieldsPart = [
        ['imTomador', 8, 'N', 0],//14
        ['ieTomador', 12, 'N', 0],//15
        ['razaoTomador', 75, 'C', ''],//16
        ['tpEndTomador', 3, 'C', ''],//17
        ['logradouroTomador', 50, 'C', ''],//18
        ['numTomador', 10, 'C', ''],//19
        ['cplTomador', 30, 'C', ''],//20
        ['bairroTomador', 30, 'C', ''],//21
        ['cidadeTomador', 50, 'C', ''],//22
        ['ufTomador', 2, 'C', ''],//23
        ['cepTomador', 8, 'N', 0],//24
        ['emailTomador', 75, 'C', ''],//25
        ['discriminacao', 1000, 'C', '']//26
    ];

    protected static $f3FieldsPart = ['discriminacao', 1000, 'C', ''];

    protected static $f5Fields = [
        ['tipo', 1, 'N', 0],
        ['indicador', 1, 'N', 0],
        ['intermediarioCNPJ', 14, 'N', 0],
        ['intermediarioIM', 8, 'N', 0],
        ['intermediarioEmail', 75, 'C', '']
    ];

    protected static $f6FieldsPart = [
        ['imTomador', 8, 'N', 0],//14
        ['ieTomador', 12, 'N', 0],//15
        ['razaoTomador', 75, 'C', ''],//16
        ['tpEndTomador', 3, 'C', ''],//17
        ['logradouroTomador', 50, 'C', ''],//18
        ['numTomador', 10, 'C', ''],//19
        ['cplTomador', 30, 'C', ''],//20
        ['bairroTomador', 30, 'C', ''],//21
        ['cidadeTomador', 50, 'C', ''],//22
        ['ufTomador', 2, 'C', ''],//23
        ['cepTomador', 8, 'N', 0],//24
        ['emailTomador', 75, 'C', ''],//25
        ['pis', 15, 'N', 2],//26
        ['cofins', 15, 'N', 2],//27
        ['inss', 15, 'N', 2],//28
        ['ir', 15, 'N', 2],//29
        ['cssl', 15, 'N', 2],//30
        ['cargaTribValor', 15, 'N', 2],//31
        ['cargaTribPerc', 5, 'N', 4],//32
        ['cargaTribFonte', 10, 'C', ''],//33
        ['cei', 12, 'N', 0],//34
        ['matriculaObra', 12, 'N', 0],//35
        ['cMunPrestacao', 7, 'N', 0],//36
        ['reservado', 200, 'C', ''],//37
        ['discriminacao', 1000, 'C', '']//38
    ];

    protected static $f9Fields = [
        ['tipo', 1, 'N', 0],
        ['num', 7, 'N', 0],
        ['valorTotalServicos', 15, 'N', 2],
        ['valorTotalDeducoes', 15, 'N', 2]
    ];

    /**
     * Converte para Objetos RPS
     * @param string $txt lote de RPS em TXT formatado ou path para o arquivo
     * @return array
     * @throws InvalidArgumentException
     */
    public static function toRps($txt = '')
    {
        if (empty($txt)) {
            throw new InvalidArgumentException('Algum dado deve ser passado para converter.');
        }
        $aRps = array();
        if (is_file($txt)) {
            //extrai cada linha do arquivo em um campo de matriz
            $aDados = file($txt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES | FILE_TEXT);
        } elseif (is_array($txt)) {
            //carrega a matriz
            $aDados = $txt;
        } else {
            if (strlen($txt) > 0) {
                //carrega a matriz com as linha do arquivo
                $aDados = explode("\n", $txt);
            } else {
                return $aRps;
            }
        }
        $total = count($aDados);
        for ($x = 0; $x < $total; $x++) {
            $aDados[$x] = str_replace("\r", '', $aDados[$x]);
            $aDados[$x] = Strings::replaceSpecialsChars($aDados[$x]);
            $tipo = substr($aDados[$x], 0, 1);
            self::$contTipos[$tipo] += 1;
        }
        self::validTipos();
        //o numero de notas criadas será a quantidade de tipo 2 ou 3 ou 6
        self::$numRps = self::$contTipos['2'] + self::$contTipos['3'] + self::$contTipos['6'];
        for ($x = 0; $x < self::$numRps; $x++) {
            self::$aRps[] = new Rps();
        }
        self::zArray2Rps($aDados);
        self::loadRPS();
        return self::$aRps;
    }

    /**
     * valida os dados do TXT com base na quantidade de cada tipo de informação
     * @throws InvalidArgumentException
     */
    protected static function validTipos()
    {
        $msg = '';
        if ((self::$contTipos['1'] == 0 || self::$contTipos['1'] > 1) ||
            (self::$contTipos['9'] == 0 || self::$contTipos['9'] > 1)
        ) {
            $msg = "No lote deve haver um e apenas um elemento do tipo 1 e do tipo 9.";
        }
        if ((self::$contTipos['2'] > 0 && self::$contTipos['3'] > 0) ||
            (self::$contTipos['6'] > 0 && self::$contTipos['3'] > 0) ||
            (self::$contTipos['6'] > 0 && self::$contTipos['2'] > 0)
        ) {
            $msg = "No mesmo lote não podem haver elementos do tipo 2 e 3."
                . "\nNem elementos do tipo 3 e 6 simultâneamente."
                . "\nNem elementos do tipo 2 e 6 simultâneamente."
                . "\nMonte um lote para cada tipo separadamente.";
        }
        if (!empty($msg)) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * zArray2xml
     * Converte um lote de RPS em um array de txt em um ou mais RPS
     *
     * @param  array $aDados
     * @return string
     * @throws Exception\RuntimeException
     */
    protected static function zArray2Rps($aDados = array())
    {
        foreach ($aDados as $dado) {
            $metodo = 'f' . substr($dado, 0, 1) . 'Entity';
            if (!method_exists(__CLASS__, $metodo)) {
                $msg = "O txt tem um metodo não definido!! $dado";
                throw new Exception\RuntimeException($msg);
            }
            self::$metodo($dado);
        }
    }

    /**
     * Carrega os RPS contidos no txt
     */
    protected static function loadRPS()
    {
        if (count(self::$f2) > 0) {
            $fData = self::$f2;
            $tipo = 2;
        } elseif (count(self::$f3) > 0) {
            $fData = self::$f3;
            $tipo = 3;
        } else {
            $fData = self::$f6;
            $tipo = 6;
        }
        $x = 0;
        foreach (self::$aRps as $rps) {
            $rps->versao(self::$f1['versao']);
            $rps->prestador(self::$f1['prestadorIM']);
            $rps->tipo($fData[$x]['tpRPS']);
            $rps->serie($fData[$x]['serie']);
            $rps->numero($fData[$x]['numero']);
            $rps->data($fData[$x]['dtEmi']);
            if ($fData[$x]['situacao'] != 'C') {
                $rps->status('N');
                $rps->tributacao($fData[$x]['situacao']);
            } else {
                $rps->status('C');
            }
            $rps->valorServicos($fData[$x]['valor']);
            $rps->valorDeducoes($fData[$x]['deducoes']);
            $rps->codigoServico($fData[$x]['codigo']);
            $rps->aliquotaServico($fData[$x]['aliquota']);
            $rps->issRetido($fData[$x]['issRetido']);
            $rps->discriminacao($fData[$x]['discriminacao']);
            $func = 'loadTipo' . $tipo;
            self::$func($rps, $fData[$x]);
            //verifica se possue intermediario tipo 5
            if (!empty($f5[$x])) {
                $rps->intemediario(
                    $f5[$x]['indicador'],
                    $f5[$x]['intermediarioCNPJ'],
                    $f5[$x]['intermediarioIM'],
                    $f5[$x]['intermediarioEmail']
                );
            }
            $x++;
        }
    }

    /**
     * Carrega a parte do RPS relativa apenas a
     * registro tipo 3 - CUPONS (versão 001 e 002 do layout)
     * @param Rps $rps
     * @param array $fData
     */
    protected static function loadTipo3(Rps &$rps, $fData)
    {
        $rps->tomador(
            '',
            $fData['indTomador'],
            $fData['cnpjcpfTomador'],
            '',
            '',
            ''
        );
    }

    /**
     * Carrega a parte do RPS relativa apenas a
     * registro tipo 6 (versão 002 do layout)
     * @param Rps $rps
     * @param array $fData
     */
    protected static function loadTipo6(Rps &$rps, $fData)
    {
        self::loadTipo2($rps, $fData);
        //campos especificos que só existem no tipo 6
        $rps->valorPIS($fData['pis']);
        $rps->valorCOFINS($fData['cofins']);
        $rps->valorIR($fData['ir']);
        $rps->valorCSLL($fData['csll']);
        $rps->valorINSS($fData['inss']);
        $rps->codigoCEI($fData['cei']);
        $rps->matriculaObra($fData['matriculaObra']);
        $rps->municipioPrestacao($fData['cMunPrestacao']);
        $rps->cargaTributaria(
            $fData['cargaTribValor'],
            $fData['cargaTribPerc'],
            $fData['cargaTribFonte']
        );
    }

    /**
     * Carrega a parte do RPS relativa apenas a
     * registro tipo 2 (versão 001 do layout)
     * @param Rps $rps
     * @param type $fData
     */
    protected static function loadTipo2(Rps &$rps, $fData)
    {
        $rps->tomador(
            $fData['razaoTomador'],
            $fData['indTomador'],
            $fData['cnpjcpfTomador'],
            $fData['ieTomador'],
            $fData['imTomador'],
            $fData['emailTomador']
        );
        $rps->tomadorEndereco(
            $fData['tpEndTomador'],
            $fData['logradouroTomador'],
            $fData['numTomador'],
            $fData['cplTomador'],
            $fData['bairroTomador'],
            $fData['cidadeTomador'],
            $fData['ufTomador'],
            $fData['cepTomador']
        );
    }

    /**
     * REGISTRO TIPO 1 – CABEÇALHO
     * Versão 001 e 002
     * @param string $dado
     */
    protected static function f1Entity($dado)
    {
        self::$f1 = self::extract($dado, self::$f1Fields);
    }

    /**
     * Extrai os dados da string em campos de array
     * @param string $dado
     * @param array $aFields
     * @return array
     */
    private static function extract($dado, $aFields)
    {
        $x = 0;
        $pos = 0;
        $aData = [];
        $len = strlen($dado);
        foreach ($aFields as $field) {
            if ($pos >= $len) {
                $aData[$field[0]] = '';
            } else {
                $tipo = $field[2];
                $df = substr($dado, $pos, $field[1]);
                if ($tipo == 'N') {
                    //converter representação
                    $df = $df / (10 ** $field[3]);
                    //formatar dado numerico
                    $df = number_format($df, $field[3], '.', '');
                } elseif ($tipo == 'C') {
                    //formatar dado string
                    if ($field[3] != '') {
                        $df = preg_replace($field[3], '', $df);
                    }
                } elseif ($tipo == 'D') {
                    //formatar dado data
                    $df = substr($df, 0, 4) . '-' . substr($df, 4, 2) . '-' . substr($df, 6, 2);
                }
                $aData[$field[0]] = $df;
            }
            $x++;
            $pos += $field[1];
        }
        return $aData;
    }

    /**
     * REGISTRO TIPO 2 – DETALHE
     * Versão 001
     * @param string $dado
     */
    protected static function f2Entity($dado)
    {
        $aFields = array_merge(self::$bF, self::$f2FieldsPart);
        self::$f2[] = self::extract($dado, $aFields);
        self::$item = count(self::$f2) - 1;
    }

    /**
     * REGISTRO TIPO 3 - DETALHE (EXCLUSIVO PARA CUPONS)
     * Versão 001 e 002
     * @param string $dado
     */
    protected static function f3Entity($dado)
    {
        $aFields = array_merge(self::$bF, self::$f3FieldsPart);
        self::$f3[] = self::extract($dado, $aFields);
        self::$item = count(self::$f3) - 1;
    }

    /**
     * REGISTRO TIPO 5 – DETALHE DO INTERMEDIÁRIO DO SERVIÇO
     * Este registro está vinculado ao registro anterior que pode
     * ser to tipo 2 ou do tipo 6, ou seja pertence a um RPS especifico
     * Versão 001 e 002
     * @param string $dado
     */
    protected static function f5Entity($dado)
    {
        self::$f5[self::$item] = self::extract($dado, self::$f5Fields);
    }

    /**
     * REGISTRO TIPO 6 – DETALHE
     * Versão 002
     * @param string $dado
     */
    protected static function f6Entity($dado)
    {
        $aFields = array_merge(self::$bF, self::$f6FieldsPart);
        self::$f6[] = self::extract($dado, $aFields);
        self::$item = count(self::$f6) - 1;
    }

    /**
     * REGISTRO TIPO 9 – RODAPÉ
     * Versão 001 e 002
     * @param string $dado
     */
    protected static function f9Entity($dado)
    {
        self::$f9 = self::extract($dado, self::$f9Fields);
    }
}
