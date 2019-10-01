<?php

namespace NFePHP\NFSe\Tests\Models\Prodam;

use NFePHP\NFSe\Tests\NFSeTestCase;
use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\Prodam\Convert;
use NFePHP\NFSe\NFSe;

class ConvertTest extends NFSeTestCase
{
    public $convert;
    
    public function __construct()
    {
        parent::__construct();
        $nfse = new NFSe(
            $this->configJson,
            Certificate::readPfx($this->contentpfx, $this->passwordpfx)
        );
        $this->convert = $nfse->convert;
    }
    
    /**
     * @covers NFePHP\NFSe\Models\Prodam\Convert::toRps
     * @covers NFePHP\NFSe\Models\Prodam\Convert::validTipos
     * @covers NFePHP\NFSe\Models\Prodam\Convert::loadRPS
     * @covers NFePHP\NFSe\Models\Prodam\Convert::loadTipo2
     * @covers NFePHP\NFSe\Models\Prodam\Convert::f1Entity
     * @covers NFePHP\NFSe\Models\Prodam\Convert::f2Entity
     * @covers NFePHP\NFSe\Models\Prodam\Convert::f9Entity
     * @covers NFePHP\NFSe\Models\Prodam\Convert::zArray2Rps
     * @covers NFePHP\NFSe\Models\Prodam\Convert::extract
     */
    public function testToRps()
    {
        $rpss = $this->convert->toRps($this->fixturesPath . '/Prodam/LoteRPS2.txt');
        $this->assertInstanceOf('\NFePHP\NFSe\Models\Prodam\Rps', $rpss[0]);
    }
    
    /**
     * @covers NFePHP\NFSe\Models\Prodam\Convert::toRps
     * @covers NFePHP\NFSe\Models\Prodam\Convert::validTipos
     * @covers NFePHP\NFSe\Models\Prodam\Convert::loadRPS
     * @covers NFePHP\NFSe\Models\Prodam\Convert::loadTipo2
     * @covers NFePHP\NFSe\Models\Prodam\Convert::f1Entity
     * @covers NFePHP\NFSe\Models\Prodam\Convert::f2Entity
     * @covers NFePHP\NFSe\Models\Prodam\Convert::f9Entity
     * @covers NFePHP\NFSe\Models\Prodam\Convert::zArray2Rps
     * @covers NFePHP\NFSe\Models\Prodam\Convert::extract
     * @expectedException InvalidArgumentException
     */
    public function testToRpsFail2And6Types()
    {
        $rpss = $this->convert->toRps($this->fixturesPath . '/Prodam/LoteRPS26_fail.txt');
    }
}
