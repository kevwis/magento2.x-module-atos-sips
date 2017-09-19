<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Model\Config;

/**
 * Authorize.net Payment CC Types Source Model
 */
class Cctype implements ArrayInterface
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Repository
     */
    private $assetRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var array
     */
    private $icons = [];


    /**
     * Cctype constructor.
     * @param Config $paymentConfig
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param Repository $repository
     */
    public function __construct(
        Config $paymentConfig,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        Repository $repository
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->assetRepository = $repository;
        $this->request = $request;
    }


    /**
     * @return array
     */
    public function getCcAvailableTypes()
    {
        if (empty($this->types)) {
            foreach ((array)$this->scopeConfig->getValue('atos/cctypes') as $code => $name) {
                $this->types[] = [
                    'code' => $code,
                    'label' => $name
                ];
            }
        }

        return $this->types;
    }


    /**
     * @return array
     */
    public function getIcons()
    {
        if (empty($this->icons)) {

            $types = $this->getCcAvailableTypes();
            foreach ($types as $type) {
                $code = $type['code'];
                if (!array_key_exists($code, $this->icons)) {
                    $asset =  $this->assetRepository->createAsset("Kevwis_Atos::images/payment/logo/{$code}.gif", [
                        '_secure' => $this->request->isSecure()
                    ]);

                    list($width, $height) = getimagesize($asset->getSourceFile());
                    $this->icons[$code] = [
                        'title' => $type['label'],
                        'url' => $asset->getUrl(),
                        'width' => $width,
                        'height' => $height
                    ];
                }
            }
        }

        return $this->icons;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getCcAvailableTypes() as $type) {
            $options[] = [
                'value'  => $type['code'],
                'label' => $type['label'],
            ];
        }
        return $options;
    }
}

