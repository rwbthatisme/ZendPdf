<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf;

use ZendPdf\Color\ColorInterface;
use ZendPdf\InternalType\NumericObject;
use ZendPdf\Resource\Font\AbstractFont;

/**
 * Style object.
 * Style object doesn't directly correspond to any PDF file object.
 * It's utility class, used as a container for style information.
 * It's used by \ZendPdf\Page class for draw operations.
 *
 * @package    Zend_PDF
 */
class Style
{
    /**
     * Fill color.
     * Used to fill geometric shapes or text.
     *
     * @var ColorInterface|null
     */
    private $_fillColor = null;

    /**
     * Line color.
     * Current color, used for lines and font outlines.
     *
     * @var ColorInterface|null
     */

    private $_color;

    /**
     * Line width.
     *
     * @var NumericObject
     */
    private $_lineWidth;

    /**
     * Array which describes line dashing pattern.
     * It's array of numeric:
     * array($on_length, $off_length, $on_length, $off_length, ...)
     *
     * @var array
     */
    private $_lineDashingPattern;

    /**
     * Line dashing phase
     *
     * @var float
     */
    private $_lineDashingPhase;

    /**
     * Current font
     *
     * @var AbstractFont
     */
    private $_font;

    /**
     * Font size
     *
     * @var float
     */
    private $_fontSize;


    /**
     * Create style.
     *
     * @param Style $anotherStyle
     */
    public function __construct($anotherStyle = null)
    {
        if ($anotherStyle !== null) {
            $this->_fillColor = $anotherStyle->_fillColor;
            $this->_color = $anotherStyle->_color;
            $this->_lineWidth = $anotherStyle->_lineWidth;
            $this->_lineDashingPattern = $anotherStyle->_lineDashingPattern;
            $this->_lineDashingPhase = $anotherStyle->_lineDashingPhase;
            $this->_font = $anotherStyle->_font;
            $this->_fontSize = $anotherStyle->_fontSize;
        }
    }

    /**
     * Set line color.
     *
     * @param ColorInterface $color
     */
    public function setLineColor(Color\ColorInterface $color)
    {
        $this->_color = $color;
    }

    /**
     * Get fill color.
     *
     * @return ColorInterface|null
     */
    public function getFillColor()
    {
        return $this->_fillColor;
    }

    /**
     * Set fill color.
     *
     * @param ColorInterface $color
     */
    public function setFillColor(Color\ColorInterface $color)
    {
        $this->_fillColor = $color;
    }

    /**
     * Get line color.
     *
     * @return ColorInterface|null
     */
    public function getLineColor()
    {
        return $this->_color;
    }

    /**
     * Get line width.
     *
     * @return float
     */
    public function getLineWidth()
    {
        return $this->_lineWidth->value;
    }

    /**
     * Set line width.
     *
     * @param float $width
     */
    public function setLineWidth($width)
    {
        $this->_lineWidth = new InternalType\NumericObject($width);
    }

    /**
     * Get line dashing pattern
     *
     * @return array
     */
    public function getLineDashingPattern()
    {
        return $this->_lineDashingPattern;
    }

    /**
     * Set line dashing pattern
     *
     * @param array $pattern
     * @param float $phase
     */
    public function setLineDashingPattern($pattern, $phase = 0)
    {
        if ($pattern === Page::LINE_DASHING_SOLID) {
            $pattern = [];
            $phase = 0;
        }

        $this->_lineDashingPattern = $pattern;
        $this->_lineDashingPhase = new InternalType\NumericObject($phase);
    }

    /**
     * Get current font.
     *
     * @return AbstractFont $font
     */
    public function getFont()
    {
        return $this->_font;
    }

    /**
     * Set current font.
     *
     * @param AbstractFont $font
     * @param float $fontSize
     */
    public function setFont(Resource\Font\AbstractFont $font, $fontSize)
    {
        $this->_font = $font;
        $this->_fontSize = $fontSize;
    }

    /**
     * Get current font size
     *
     * @return float $fontSize
     */
    public function getFontSize()
    {
        return $this->_fontSize;
    }

    /**
     * Modify current font size
     *
     * @param float $fontSize
     */
    public function setFontSize($fontSize)
    {
        $this->_fontSize = $fontSize;
    }

    /**
     * Get line dashing phase
     *
     * @return float
     */
    public function getLineDashingPhase()
    {
        return $this->_lineDashingPhase->value;
    }


    /**
     * Dump style to a string, which can be directly inserted into content stream
     *
     * @return string
     */
    public function instructions()
    {
        $instructions = '';

        if ($this->_fillColor !== null) {
            $instructions .= $this->_fillColor->instructions(false);
        }

        if ($this->_color !== null) {
            $instructions .= $this->_color->instructions(true);
        }

        if ($this->_lineWidth !== null) {
            $instructions .= $this->_lineWidth->toString() . " w\n";
        }

        if ($this->_lineDashingPattern !== null) {
            $dashPattern = new InternalType\ArrayObject();

            foreach ($this->_lineDashingPattern as $dashItem) {
                $dashElement = new InternalType\NumericObject($dashItem);
                $dashPattern->items[] = $dashElement;
            }

            $instructions .= $dashPattern->toString() . ' '
                . $this->_lineDashingPhase->toString() . " d\n";
        }

        return $instructions;
    }
}
