<?php
/**
 * Generated by <a href="http://enunciate.codehaus.org">Enunciate</a>.
 */

namespace Gedcomx\Records;

use Gedcomx\Links\HypermediaEnabledData;
use Gedcomx\Rt\GedcomxModelVisitor;

/**
 * A field of a record.
 */
class Field extends HypermediaEnabledData
{
    /**
     * The type of the gender.
     *
     * @var string
     */
    private $type;

    /**
     * The set of values for the field.
     *
     * @var FieldValue[]
     */
    private $values;

    /**
     * Constructs a Field from a (parsed) JSON hash
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
     * The type of the gender.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The type of the gender.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * The set of values for the field.
     *
     * @return \Gedcomx\Records\FieldValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * The set of values for the field.
     *
     * @param \Gedcomx\Records\FieldValue[] $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * Returns the associative array for this Field
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->type) {
            $a["type"] = $this->type;
        }
        if ($this->values) {
            $ab = array();
            foreach ($this->values as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['values'] = $ab;
        }

        return $a;
    }

    /**
     * Initializes this Field from an associative array
     *
     * @param array $o
     */
    public function initFromArray($o)
    {
        if (isset($o['type'])) {
            $this->type = $o["type"];
            unset($o['type']);
        }
        $this->values = array();
        if (isset($o['values'])) {
            foreach ($o['values'] as $i => $x) {
                $this->values[$i] = new FieldValue($x);
            }
            unset($o['values']);
        }
        parent::initFromArray($o);
    }

    public function accept(GedcomxModelVisitor $visitor)
    {
        $visitor->visitField($this);
    }

    /**
     * Sets a known child element of Field from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement($xml)
    {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
            return true;
        } else {
            if (($xml->localName == 'value') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
                $child = new FieldValue($xml);
                if (!isset($this->values)) {
                    $this->values = array();
                }
                array_push($this->values, $child);
                $happened = true;
            }
        }

        return $happened;
    }

    /**
     * Sets a known attribute of Field from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute($xml)
    {
        if (parent::setKnownAttribute($xml)) {
            return true;
        } else {
            if (($xml->localName == 'type') && (empty($xml->namespaceURI))) {
                $this->type = $xml->value;

                return true;
            }
        }

        return false;
    }

    /**
     * Writes the contents of this Field to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents($writer)
    {
        if ($this->type) {
            $writer->writeAttribute('type', $this->type);
        }
        parent::writeXmlContents($writer);
        if ($this->values) {
            foreach ($this->values as $i => $x) {
                $writer->startElementNs('gx', 'value', null);
                $x->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
