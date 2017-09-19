<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Cofidis3x;

use Kevwis\Atos\Model\Config;
use Kevwis\Atos\Block\Standard\Form as PaymentForm;

/**
 * Class Form
 * @package Kevwis\Atos\Block\Cofidis3x
 */
class Form extends PaymentForm
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_methodCode = Config::METHOD_COFIDIS3X;
}