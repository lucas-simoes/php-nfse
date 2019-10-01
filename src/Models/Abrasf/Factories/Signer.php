<?php

namespace NFePHP\NFSe\Models\Abrasf\Factories;

/**
 * Class to signner a Xml
 * Meets only for Abrasf
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\Abrasf\Factories\Signer
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use DOMDocument;
use DOMNode;
use DOMElement;
use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\SignerException;
use NFePHP\Common\Signer as SignerCommon;
use NFePHP\Common\Validator;

class Signer extends SignerCommon
{
    private static $canonical = [true,false,null,null];
    /**
     * Make Signature tag
     * @param Certificate $certificate
     * @param string $content xml for signed
     * @param string $tagname
     * @param string $mark for URI (opcional)
     * @param int $algorithm (opcional)
     * @param array $canonical parameters to format node for signature (opcional)
     * @param string $rootname name of tag to insert signature block (opcional)
     * @return string
     * @throws SignerException
     */
    public static function sign(
        Certificate $certificate,
        $content,
        $tagname,
        $mark = 'Id',
        $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = [true, false, null, null],
        $rootname = '',
        $ignoreExistSignature = false,
        $index = 0
    ) {
        if (!empty($canonical)) {
            self::$canonical = $canonical;
        }
        if (empty($content)) {
            throw SignerException::isNotXml();
        }

        if (!Validator::isXML($content)) {
            throw SignerException::isNotXml();
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($content);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $root = $dom->documentElement;
        if (!empty($rootname)) {
            $root = $dom->getElementsByTagName($rootname)->item(0);
        }
        $node = $dom->getElementsByTagName($tagname)->item($index);
        if (empty($node) || empty($root)) {
            throw SignerException::tagNotFound($tagname);
        }

        if (!self::existsSignature($content) || $ignoreExistSignature) {
            $dom = self::createSignature(
                $certificate,
                $dom,
                $root,
                $node,
                $mark,
                $algorithm,
                $canonical
            );
        };

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
            . $dom->saveXML($dom->documentElement, LIBXML_NOXMLDECL);
    }
    /**
     * Method that provides the signature of xml as standard SEFAZ
     * @param Certificate $certificate
     * @param \DOMDocument $dom
     * @param \DOMNode $root xml root
     * @param \DOMElement $node node to be signed
     * @param string $mark Marker signed attribute
     * @param int $algorithm cryptographic algorithm (opcional)
     * @param array $canonical parameters to format node for signature (opcional)
     * @return \DOMDocument
     */
    private static function createSignature(
        Certificate $certificate,
        DOMDocument $dom,
        DOMNode $root,
        DOMElement $node,
        $mark,
        $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = [true,false,null,null]
    ) {
        $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';
        $digestAlgorithm = 'sha1';
        if ($algorithm == OPENSSL_ALGO_SHA256) {
            $digestAlgorithm = 'sha256';
            $nsSignatureMethod = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
            $nsDigestMethod = 'http://www.w3.org/2001/04/xmlenc#sha256';
        }
        $nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $idSigned = trim($node->getAttribute($mark));
        $digestValue = self::makeDigest($node, $digestAlgorithm, $canonical);
        $signatureNode = $dom->createElementNS($nsDSIG, 'Signature');
        $root->appendChild($signatureNode);
        $signedInfoNode = $dom->createElement('SignedInfo');
        $signatureNode->appendChild($signedInfoNode);
        $canonicalNode = $dom->createElement('CanonicalizationMethod');
        $signedInfoNode->appendChild($canonicalNode);
        $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
        $signatureMethodNode = $dom->createElement('SignatureMethod');
        $signedInfoNode->appendChild($signatureMethodNode);
        $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
        $referenceNode = $dom->createElement('Reference');
        $signedInfoNode->appendChild($referenceNode);
        if (!empty($idSigned)) {
            $idSigned = "#$idSigned";
        }
        $referenceNode->setAttribute('URI', $idSigned);
        $transformsNode = $dom->createElement('Transforms');
        $referenceNode->appendChild($transformsNode);
        $transfNode1 = $dom->createElement('Transform');
        $transformsNode->appendChild($transfNode1);
        $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
        $transfNode2 = $dom->createElement('Transform');
        $transformsNode->appendChild($transfNode2);
        $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
        $digestMethodNode = $dom->createElement('DigestMethod');
        $referenceNode->appendChild($digestMethodNode);
        $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
        $digestValueNode = $dom->createElement('DigestValue', $digestValue);
        $referenceNode->appendChild($digestValueNode);
        $c14n = self::canonize($signedInfoNode, $canonical);
        $signature = $certificate->sign($c14n, $algorithm);
        $signatureValue = base64_encode($signature);
        $signatureValueNode = $dom->createElement('SignatureValue', $signatureValue);
        $signatureNode->appendChild($signatureValueNode);
        $keyInfoNode = $dom->createElement('KeyInfo');
        $signatureNode->appendChild($keyInfoNode);
        $x509DataNode = $dom->createElement('X509Data');
        $keyInfoNode->appendChild($x509DataNode);
        $pubKeyClean = $certificate->publicKey->unFormated();
        $x509CertificateNode = $dom->createElement('X509Certificate', $pubKeyClean);
        $x509DataNode->appendChild($x509CertificateNode);
        return $dom;
    }
    /**
     * Calculate digest value for given node
     * @param DOMNode $node
     * @param string $algorithm
     * @param array $canonical
     * @return string
     */
    private static function makeDigest(
        DOMNode $node,
        $algorithm,
        $canonical = [true,false,null,null]
    ) {
        //calcular o hash dos dados
        $c14n = self::canonize($node, $canonical);
        $hashValue = hash($algorithm, $c14n, true);
        return base64_encode($hashValue);
    }

    /**
     * Reduced to the canonical form
     * @param DOMNode $node
     * @param array $canonical
     * @return string
     */
    private static function canonize(
        DOMNode $node,
        $canonical = [true,false,null,null]
    ) {
        return $node->C14N(
            $canonical[0],
            $canonical[1],
            $canonical[2],
            $canonical[3]
        );
    }
}
