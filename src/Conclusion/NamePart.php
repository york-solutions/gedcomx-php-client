<?php
/**
 * Generated by <a href="http://enunciate.codehaus.org">Enunciate</a>.
 */

namespace Gedcomx\Conclusion;

use Gedcomx\Common\ExtensibleData;
use Gedcomx\Common\Qualifier;
use Gedcomx\Records\Field;
use Gedcomx\Rt\GedcomxModelVisitor;

/**
 * A part of a name.
 */
class NamePart extends ExtensibleData
{
    /**
     * The value of the name part.
     *
     * @var string
     */
    private $value;

    /**
     * The type of the name part.
     *
     * @var string
     */
    private $type;

    /**
     * The references to the record fields being used as evidence.
     *
     * @var \Gedcomx\Records\Field[]
     */
    private $fields;

    /**
     * The qualifiers associated with this name part.
     *
     * @var \Gedcomx\Common\Qualifier[]
     */
    private $qualifiers;

    /**
     * Constructs a NamePart from a (parsed) JSON hash
     *
     * @param mixed $o Either an array (JSON) or an XMLReader.
     *
     * @throws \Exception
     */
    public function __construct($o = null)
    {
        if (is_array($o)) {
            $this->initFromArray($o);
        } else {
            if ($o instanceof \XMLReader) {
                $success = true;
                while ($success && $o->nodeType != \XMLReader::ELEMENT) {
                    $success = $o->read();
                }
                if ($o->nodeType != \XMLReader::ELEMENT) {
                    throw new \Exception("Unable to read XML: no start element found.");
                }

                $this->initFromReader($o);
            }
        }
    }

    /**
     * The value of the name part.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * The value of the name part.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * The type of the name part.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The type of the name part.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * The references to the record fields being used as evidence.
     *
     * @return \Gedcomx\Records\Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * The references to the record fields being used as evidence.
     *
     * @param \Gedcomx\Records\Field[] $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * The qualifiers associated with this name part.
     *
     * @return \Gedcomx\Common\Qualifier[]
     */
    public function getQualifiers()
    {
        return $this->qualifiers;
    }

    /**
     * The qualifiers associated with this name part.
     *
     * @param \Gedcomx\Common\Qualifier[] $qualifiers
     */
    public function setQualifiers(array $qualifiers)
    {
        $this->qualifiers = $qualifiers;
    }

    /**
     * Returns the associative array for this NamePart
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->value) {
            $a["value"] = $this->value;
        }
        if ($this->type) {
            $a["type"] = $this->type;
        }
        if ($this->fields) {
            $ab = array();
            foreach ($this->fields as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['fields'] = $ab;
        }
        if ($this->qualifiers) {
            $ab = array();
            foreach ($this->qualifiers as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['qualifiers'] = $ab;
        }

        return $a;
    }

    /**
     * Initializes this NamePart from an associative array
     *
     * @param array $o
     */
    public function initFromArray(array $o)
    {
        if (isset($o['value'])) {
            $this->value = $o["value"];
            unset($o['value']);
        }
        if (isset($o['type'])) {
            $this->type = $o["type"];
            unset($o['type']);
        }
        $this->fields = array();
        if (isset($o['fields'])) {
            foreach ($o['fields'] as $i => $x) {
                $this->fields[$i] = new Field($x);
            }
            unset($o['fields']);
        }
        $this->qualifiers = array();
        if (isset($o['qualifiers'])) {
            foreach ($o['qualifiers'] as $i => $x) {
                $this->qualifiers[$i] = new Qualifier($x);
            }
            unset($o['qualifiers']);
        }
        parent::initFromArray($o);
    }

    /**
     * @param \Gedcomx\Rt\GedcomxModelVisitor $visitor
     */
    public function accept(GedcomxModelVisitor $visitor)
    {
        $visitor->visitNamePart($this);
    }

    /**
     * Sets a known child element of NamePart from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement(\XMLReader $xml)
    {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
            return true;
        } else {
            if (($xml->localName == 'field') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
                $child = new Field($xml);
                if (!isset($this->fields)) {
                    $this->fields = array();
                }
                array_push($this->fields, $child);
                $happened = true;
            } else {
                if (($xml->localName == 'qualifier') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
                    $child = new Qualifier($xml);
                    if (!isset($this->qualifiers)) {
                        $this->qualifiers = array();
                    }
                    array_push($this->qualifiers, $child);
                    $happened = true;
                }
            }
        }

        return $happened;
    }

    /**
     * Sets a known attribute of NamePart from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute(\XMLReader $xml)
    {
        if (parent::setKnownAttribute($xml)) {
            return true;
        } else {
            if (($xml->localName == 'value') && (empty($xml->namespaceURI))) {
                $this->value = $xml->value;

                return true;
            } else {
                if (($xml->localName == 'type') && (empty($xml->namespaceURI))) {
                    $this->type = $xml->value;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Writes the contents of this NamePart to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents(\XMLWriter $writer)
    {
        if ($this->value) {
            $writer->writeAttribute('value', $this->value);
        }
        if ($this->type) {
            $writer->writeAttribute('type', $this->type);
        }
        parent::writeXmlContents($writer);
        if ($this->fields) {
            foreach ($this->fields as $i => $x) {
                $writer->startElementNs('gx', 'field', null);
                $x->writeXmlContents($writer);
                $writer->endElement();
            }
        }
        if ($this->qualifiers) {
            foreach ($this->qualifiers as $i => $x) {
                $writer->startElementNs('gx', 'qualifier', null);
                $x->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
