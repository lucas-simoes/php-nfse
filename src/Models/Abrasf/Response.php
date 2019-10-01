<?php

namespace NFePHP\NFSe\Models\Abrasf;

/**
 * Classe para extração dos dados retornados pelos webservices
 * conforme o modelo ABRASF
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abrasf\Response
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSe\Common\Response as ResponseBase;

class Response extends ResponseBase
{
    const NAME_ATTRIBUTES = '@attributes';
    const NAME_CONTENT = '@content';
    const NAME_ROOT = '@root';

    const NAO_RECEBIDO = 1;
    const NAO_PROCESSADO = 2;
    const PROCESSADO_ERRO = 3;
    const PROCESSADO_SUCESSO = 4;

    /**
     * @param $response
     * @return array|string
     */
    public function read($response)
    {
        $response = str_replace('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>', '', $response);
        $dom = new Dom('1.0', 'utf-8');
        $dom->loadXML($response);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        if (!empty($errors)) {
            $msg = '';
            foreach ($errors as $error) {
                $msg .= "$error->message\n";
            }
            throw new \RuntimeException($msg);
        }
        $reason = self::checkForFault($dom);
        if ($reason != '') {
            throw new \RuntimeException($reason);
        }
        $root = $dom->documentElement;
        $array = $this->Dom2Array($root);

        return $array;
    }

    /**
     * @param $documentElement
     * @return array|string
     */
    protected function Dom2Array($documentElement)
    {
        $return = array();
        switch ($documentElement->nodeType) {

            case XML_CDATA_SECTION_NODE:
                $return = trim($documentElement->textContent);
                break;
            case XML_TEXT_NODE:
                $return = utf8_decode(trim($documentElement->textContent));
                break;
            case XML_ELEMENT_NODE:
                for ($count = 0, $childNodeLength = $documentElement->childNodes->length; $count < $childNodeLength; $count++) {
                    $child = $documentElement->childNodes->item($count);
                    $childValue = $this->Dom2Array($child);
                    if (isset($child->tagName)) {
                        $tagName = $child->tagName;
                        if (!isset($return[$tagName])) {
                            $return[$tagName] = array();
                        }
                        $return[$tagName][] = $childValue;
                    } elseif ($childValue || $childValue === '0') {
                        $return = (string)$childValue;
                    }
                }
                if ($documentElement->attributes->length && !is_array($return)) {
                    $return = array(self::NAME_CONTENT => $return);
                }

                if (is_array($return)) {
                    if ($documentElement->attributes->length) {
                        $attributes = array();
                        foreach ($documentElement->attributes as $attrName => $attrNode) {
                            $attributes[$attrName] = (string)$attrNode->value;
                        }
                        $return[self::NAME_ATTRIBUTES] = $attributes;
                    }
                    foreach ($return as $key => $value) {
                        if (is_array($value) && count($value) == 1 && $key != self::NAME_ATTRIBUTES) {
                            $return[$key] = $value[0];
                        }
                    }
                }
                break;
        }
        return $return;
    }
}
