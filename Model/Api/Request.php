<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Api;

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Kevwis\Atos\Model\Api;

/**
 * Request object
 */
class Request extends DataObject
{


    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $options;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;


    /**
     * Request constructor.
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param array $options
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        array $options = [],
        array $data = []
    ) {
        parent::__construct($data);

        $this->storeManager = $storeManager;
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getType() {
        return  ($this->options['api'] instanceof Api ) ? $this->options['api']->getType() : null;
    }


    /**
     * @param array $params
     * @return array
     */
    public function getArgs(array $params = [])
    {
        $data = [];

        try {

            foreach (array_merge($this->getData('args'), $params) as $key => $value) {
                $data[] = "{$key}={$value}";
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getMessage() {

        return \implode(' ', $this->getArgs());
    }

    /**
     * @param $bin
     * @param $pathfile
     * @return string
     */
    public function execute($bin, $pathfile) {

        try {

            $this->logger->debug(__CLASS__, [escapeshellcmd("{$bin}"), $this->getMessage(), $pathfile]);

            return (string) exec(escapeshellcmd("{$bin} pathfile={$pathfile} {$this->getMessage()}"));

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}
