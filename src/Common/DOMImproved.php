<?php

namespace NFePHP\NFSe\Common;

/**
 * Extends DOMDocument
 * @category   NFePHP
 * @package    NFePHP\Common\DOMImproved
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use DOMNode;
use DOMElement;
use DOMDocument;
use NFePHP\Common\DOMImproved as DOMImprovedBase;

class DOMImproved extends DOMImprovedBase
{
    /**
     * addChild
     * Adiciona um elemento ao node xml passado como referencia
     * Serão inclusos erros na array $erros[] sempre que a tag for obrigatória e
     * nenhum parâmetro for passado na variável $content e $force for false
     * @param \DOMElement $parent
     * @param string|null $name
     * @param string|float|null $content
     * @param boolean $obrigatorio
     * @param string $descricao
     * @param boolean $force força a criação do elemento mesmo sem dados e não considera como erro
     * @return void
     */
    public function addChild(
        DOMElement &$parent,
        $name,
        $content,
        $obrigatorio = false,
        $descricao = '',
        $force = false,
        $attrs = []
    ) {
        if (empty($name)) {
            $this->errors[] = "O nome da TAG é Obrigatório!";
            return;
        }
        if (!$obrigatorio && $content === null) {
            return;
        } elseif ($obrigatorio && ($content === null || $content === '')) {
            $this->errors[] = "Preenchimento Obrigatório! [$name] $descricao";
        }
        $content = (string) $content;
        $content = trim($content);
        if ($obrigatorio || $content !== '' || $force) {
            $content = htmlspecialchars($content, ENT_QUOTES);
            $temp = $this->createElement($name, $content);
            foreach($attrs as $attr) {
                $temp->setAttribute($attr['attr'], $attr['value']);
            }
            $parent->appendChild($temp);
        }
    }    
}
