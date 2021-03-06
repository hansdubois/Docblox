<?php
/**
 * DocBlox
 *
 * PHP 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license	   http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that adds support for the @return tag
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_Tag_Property extends
    DocBlox_Transformer_Behaviour_Abstract
{
    /**
     * Find all return tags that contain 'self' or '$this' and replace those
     * terms for the name of the current class' type.
     *
     * @param DOMDocument $xml
     *
     * @return DOMDocument
     */
    public function process(DOMDocument $xml)
    {
        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query(
            '/project/file/class/docblock/tag[@name="property"]'
            .'|/project/file/class/docblock/tag[@name="property-read"]'
            .'|/project/file/class/docblock/tag[@name="property-write"]'
        );

        /** @var DOMElement $node */
        foreach($nodes as $node) {
            $class = $node->parentNode->parentNode;

            $property = new DOMElement('property');
            $class->appendChild($property);

            $property->setAttribute('final', 'false');
            $property->setAttribute('static', 'false');
            $property->setAttribute('visibility', 'public');
            $property->setAttribute('line', $node->getAttribute('line'));
            $property->appendChild(new DOMElement('name', $node->getAttribute('variable')));
            $property->appendChild(new DOMElement('default'));

            $docblock = new DOMElement('docblock');
            $property->appendChild($docblock);
            $docblock->appendChild(new DOMElement('description', $node->getAttribute('description')));
            $docblock->appendChild(new DOMElement('long-description'));

            $var_tag = new DOMElement('tag');
            $docblock->appendChild($var_tag);
            $var_tag->setAttribute('name', 'var');
            $var_tag->setAttribute('description', $node->getAttribute('description'));
            $var_tag->setAttribute('type', $node->getAttribute('type'));
            $var_tag->setAttribute('line', $node->getAttribute('line'));

            $var_tag_type = new DOMElement('type', $node->getAttribute('type'));
            $var_tag->appendChild($var_tag_type);
            $var_tag_type->setAttribute('by_reference', 'false');

            $magic_tag = new DOMElement('tag');
            $docblock->appendChild($magic_tag);
            $magic_tag->setAttribute('name', 'magic');
            $magic_tag->setAttribute('line', $node->getAttribute('line'));

            $node->parentNode->removeChild($node);
            $docblock->appendChild($node);
        }

        return $xml;
    }

}