<?php

namespace NFePHP\NFSe\Tests\Common;

use NFePHP\NFSe\Common\EntitiesCharacters;
use PHPUnit\Framework\TestCase;

class EntitiesCharactersTest extends TestCase
{
    public function testUnConvert()
    {
        $subject = "Esse é um teste de conversão";
        $actual = EntitiesCharacters::unconvert($subject);
        $expected = 'Esse [0xc3][0xa9] um teste de convers[0xc3][0xa3]o';
        $this->assertEquals($expected, $actual);
    }

    public function testConvert()
    {
        $subject = 'Esse [0xc3][0xa9] um teste de convers[0xc3][0xa3]o';
        $actual = EntitiesCharacters::convert($subject);
        $expected = "Esse é um teste de conversão";
        $this->assertEquals($expected, $actual);
    }
}
