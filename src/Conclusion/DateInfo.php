<?php
/**
 *
 * 
 *
 * Generated by <a href="http://enunciate.codehaus.org">Enunciate</a>.
 *
 */

namespace Gedcomx\Conclusion;

use Gedcomx\Common\ExtensibleData;
use Gedcomx\Common\TextValue;
use Gedcomx\Records\Field;
use Gedcomx\Rt\GedcomxModelVisitor;

/**
 * A concluded genealogical date.
 */
class DateInfo extends ExtensibleData
{

    /**
     * The original text as supplied by the user.
     *
     * @var string
     */
    private $original;

    /**
     * The formal value.
     *
     * @var string
     */
    private $formal;

    /**
     * The list of normalized values for the date, provided for display purposes by the application. Normalized values
     * are not specified by GEDCOM X core, but as extension elements by GEDCOM X RS.
     *
     * @var TextValue[]
     */
    private $normalizedExtensions;

    /**
     * The references to the record fields being used as evidence.
     *
     * @var Field[]
     */
    private $fields;

    /**
     * Constructs a DateInfo from a (parsed) JSON hash
     *
     * @param mixed $o Either an array (JSON) or an XMLReader.
     *
     * @throws \Exception
     */
    public function __construct($o = null)
    {
        if (is_array($o)) {
            $this->initFromArray($o);
        }
        else if ($o instanceof \XMLReader) {
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

    /**
     * The original text as supplied by the user.
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * The original text as supplied by the user.
     *
     * @param string $original
     */
    public function setOriginal($original)
    {
        $this->original = $original;
    }
    /**
     * The formal value.
     *
     * @return string
     */
    public function getFormal()
    {
        return $this->formal;
    }

    /**
     * The formal value.
     *
     * @param string $formal
     */
    public function setFormal($formal)
    {
        $this->formal = $formal;
    }

    /**
     * Add a normalized value to this date object
     *
     * @param TextValue $normalized
     */
    public function addNormalizedExtension(TextValue $normalized)
    {
        if( $this->normalizedExtensions == null ){
            $this->normalizedExtensions = array($normalized);
        } else {
            $this->normalizedExtensions[] = $normalized;
        }
    }

    /**
     * The list of normalized values for the date, provided for display purposes by the application. Normalized values
     * are not specified by GEDCOM X core, but as extension elements by GEDCOM X RS.
     *
     * @return TextValue[]
     */
    public function getNormalizedExtensions()
    {
        return $this->normalizedExtensions;
    }

    /**
     * The list of normalized values for the date, provided for display purposes by the application. Normalized values
       * are not specified by GEDCOM X core, but as extension elements by GEDCOM X RS.
     *
     * @param TextValue[] $normalizedExtensions
     */
    public function setNormalizedExtensions($normalizedExtensions)
    {
        $this->normalizedExtensions = $normalizedExtensions;
    }
    /**
     * The references to the record fields being used as evidence.
     *
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * The references to the record fields being used as evidence.
     *
     * @param Field[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime($this->getFormal());
    }
    /**
     * Returns the associative array for this DateInfo
     *
     * @return array
     */
    public function toArray()
    {
        $a = parent::toArray();
        if ($this->original) {
            $a["original"] = $this->original;
        }
        if ($this->formal) {
            $a["formal"] = $this->formal;
        }
        if ($this->normalizedExtensions) {
            $ab = array();
            foreach ($this->normalizedExtensions as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['normalized'] = $ab;
        }
        if ($this->fields) {
            $ab = array();
            foreach ($this->fields as $i => $x) {
                $ab[$i] = $x->toArray();
            }
            $a['fields'] = $ab;
        }
        return $a;
    }


    /**
     * Initializes this DateInfo from an associative array
     *
     * @param array $o
     */
    public function initFromArray(array $o)
    {
        if (isset($o['original'])) {
            $this->original = $o["original"];
            unset($o['original']);
        }
        if (isset($o['formal'])) {
            $this->formal = $o["formal"];
            unset($o['formal']);
        }
        $this->normalizedExtensions = array();
        if (isset($o['normalized'])) {
            foreach ($o['normalized'] as $i => $x) {
                $this->normalizedExtensions[$i] = new TextValue($x);
            }
            unset($o['normalized']);
        }
        $this->fields = array();
        if (isset($o['fields'])) {
            foreach ($o['fields'] as $i => $x) {
                $this->fields[$i] = new Field($x);
            }
            unset($o['fields']);
        }
        parent::initFromArray($o);
    }

    public function accept(GedcomxModelVisitor $visitor)
    {
        $visitor->visitDate($this);
    }

    /**
     * Sets a known child element of DateInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether a child element was set.
     */
    protected function setKnownChildElement(\XMLReader $xml) {
        $happened = parent::setKnownChildElement($xml);
        if ($happened) {
          return true;
        }
        else if (($xml->localName == 'original') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
            $child = '';
            while ($xml->read() && $xml->hasValue) {
                $child = $child . $xml->value;
            }
            $this->original = $child;
            $happened = true;
        }
        else if (($xml->localName == 'formal') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
            $child = '';
            while ($xml->read() && $xml->hasValue) {
                $child = $child . $xml->value;
            }
            $this->formal = $child;
            $happened = true;
        }
        else if (($xml->localName == 'normalized') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
            $child = new TextValue($xml);
            if (!isset($this->normalizedExtensions)) {
                $this->normalizedExtensions = array();
            }
            array_push($this->normalizedExtensions, $child);
            $happened = true;
        }
        else if (($xml->localName == 'field') && ($xml->namespaceURI == 'http://gedcomx.org/v1/')) {
            $child = new Field($xml);
            if (!isset($this->fields)) {
                $this->fields = array();
            }
            array_push($this->fields, $child);
            $happened = true;
        }
        return $happened;
    }

    /**
     * Sets a known attribute of DateInfo from an XML reader.
     *
     * @param \XMLReader $xml The reader.
     *
     * @return bool Whether an attribute was set.
     */
    protected function setKnownAttribute(\XMLReader $xml) {
        if (parent::setKnownAttribute($xml)) {
            return true;
        }

        return false;
    }

    /**
     * Writes the contents of this DateInfo to an XML writer. The startElement is expected to be already provided.
     *
     * @param \XMLWriter $writer The XML writer.
     */
    public function writeXmlContents(\XMLWriter $writer)
    {
        parent::writeXmlContents($writer);
        if ($this->original) {
            $writer->startElementNs('gx', 'original', null);
            $writer->text($this->original);
            $writer->endElement();
        }
        if ($this->formal) {
            $writer->startElementNs('gx', 'formal', null);
            $writer->text($this->formal);
            $writer->endElement();
        }
        if ($this->normalizedExtensions) {
            foreach ($this->normalizedExtensions as $i => $x) {
                $writer->startElementNs('gx', 'normalized', null);
                $x->writeXmlContents($writer);
                $writer->endElement();
            }
        }
        if ($this->fields) {
            foreach ($this->fields as $i => $x) {
                $writer->startElementNs('gx', 'field', null);
                $x->writeXmlContents($writer);
                $writer->endElement();
            }
        }
    }
}
