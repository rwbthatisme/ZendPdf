<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf\Destination;

use ZendPdf\Exception;
use ZendPdf\Exception\ExceptionInterface;
use ZendPdf\InternalType;
use ZendPdf\InternalType\AbstractTypeObject;
use ZendPdf\InternalType\NameObject;
use ZendPdf\InternalType\StringObject;

/**
 * Destination array: [page /Fit]
 *
 * Display the page designated by page, with its contents magnified just enough
 * to fit the entire page within the window both horizontally and vertically. If
 * the required horizontal and vertical magnification factors are different, use
 * the smaller of the two, centering the page within the window in the other
 * dimension.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 */
class Named extends AbstractDestination
{
    /**
     * Destination name
     *
     * @var NameObject|StringObject
     */
    protected $_nameElement;

    /**
     * Named destination object constructor
     *
     * @param $resource
     * @throws ExceptionInterface
     */
    public function __construct(AbstractTypeObject $resource)
    {
        if ($resource->getType() != AbstractTypeObject::TYPE_NAME && $resource->getType() != AbstractTypeObject::TYPE_STRING) {
            throw new Exception\CorruptedPdfException('Named destination resource must be a PDF name or a PDF string.');
        }

        $this->_nameElement = $resource;
    }

    /**
     * Create named destination object
     *
     * @param string $name
     * @return Named
     */
    public static function create($name)
    {
        return new self(new StringObject($name));
    }

    /**
     * Get name
     *
     * @return AbstractTypeObject
     */
    public function getName()
    {
        return $this->_nameElement->value;
    }

    /**
     * Get resource
     *
     * @return AbstractTypeObject
     * @internal
     */
    public function getResource()
    {
        return $this->_nameElement;
    }
}
