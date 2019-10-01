<?php

namespace NFePHP\NFSe\Tests;

use NFePHP\NFSe\NFSe;
use NFePHP\NFSe\NFSeStatic;
use NFePHP\Common\Certificate;

class NFSeTest extends NFSeTestCase
{
    public function testInstanciarNFSE()
    {
        $nfse = new NFSe(
            $this->configJson,
            Certificate::readPfx($this->contentpfx, $this->passwordpfx)
        );
        $this->assertInstanceOf('NFePHP\NFSe\Counties\M3550308\Tools', $nfse->tools);
        $this->assertInstanceOf('NFePHP\NFSe\Counties\M3550308\Rps', $nfse->rps);
        $this->assertInstanceOf('NFePHP\NFSe\Counties\M3550308\Convert', $nfse->convert);
        $this->assertInstanceOf('NFePHP\NFSe\Counties\M3550308\Response', $nfse->response);
    }
}
