<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf\InternalType\IndirectObjectReference;

use ZendPdf\PdfParser;
use ZendPdf\PdfParser\DataParser;

/**
 * PDF reference object context
 * Reference context is defined by PDF parser and PDF Refernce table
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class Context
{
    /**
     * PDF parser object.
     *
     * @var DataParser
     */
    private $_stringParser;

    /**
     * Reference table
     *
     * @var ReferenceTable
     */
    private $_refTable;

    /**
     * Object constructor
     *
     * @param DataParser $parser
     * @param ReferenceTable $refTable
     */
    public function __construct(DataParser $parser, ReferenceTable $refTable)
    {
        $this->_stringParser = $parser;
        $this->_refTable = $refTable;
    }


    /**
     * Context parser
     *
     * @return DataParser
     */
    public function getParser()
    {
        return $this->_stringParser;
    }


    /**
     * Context reference table
     *
     * @return ReferenceTable
     */
    public function getRefTable()
    {
        return $this->_refTable;
    }
}
