<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Api;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
/**
 * Response object
 */
class Response extends DataObject
{


    /* @desc Accepted authorization' */
    const ACCEPTED_AUTHORIZATION = '00';

    /* @desc Authorization request by telephone at the bank because of the ceiling of authorization on the card is exceeded' */
    const PHONE_REQUEST_AUTHORIZATION = '02';

    /* @desc Field merchant_id is invalid, verify the value in the request or non-existent remote sale contract, contact your bank' */
    const INVALID_MERCHANTI_ID = '03';

    /* @desc Refused authorization' */
    const REFUSED_AUTHORIZATION = '05';

    /* @desc Invalid transaction, verify the parameters transferred in the request' */
    const INVALID_TRANSACTION = '12';

    /* @desc Canceled by user' */
    const CANCELED_TRANSACTION = '17';

    /* @desc Format error' */
    const FORMAT_ERROR = '30';

    /* @desc Fraud suspicion' */
    const FRAUD_SUSPICION = '34';

    /* @desc Number of attempts of card's number seizure is exceeded' */
    const ATTEMPTS_EXCEEDED = '75';

    /* @desc Service temporarily unavailable' */
    const SERVICE_TEMPORARY_UNAVAILABLE = '90';

    /* @desc Transaction already saved' */
    const TRANSACTION_ALREADY_SAVED = '94';


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
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

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
        DataObjectFactory $dataObjectFactory,
        array $options = [],
        array $data = []) {

        parent::__construct($data);

        $this->storeManager = $storeManager;
        $this->options = $options;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        switch ((string) $this->getResponseCode()) {
            case self::ACCEPTED_AUTHORIZATION :
                return true;
            default:
                return false;

        }
    }

    /**
     * @return mixed
     */
    public function getResponseCode() {
        return (string) $this->getTransactionData()->getResponseCode();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getResponseMessage() {
        switch ($this->getResponseCode()) {
            case self::ACCEPTED_AUTHORIZATION :
                return __('Accepted authorization');
            case self::PHONE_REQUEST_AUTHORIZATION:
                return __('Authorization request by telephone at the bank because of the ceiling of authorization on the card is exceeded');
            case self::INVALID_MERCHANTI_ID:
                return __('Field merchant_id is invalid, verify the value in the request or non-existent remote sale contract, contact your bank');
            case self::REFUSED_AUTHORIZATION:
                return __('Refused authorization');
            case self::INVALID_TRANSACTION:
                return __('Invalid transaction, verify the parameters transferred in the request');
            case self::CANCELED_TRANSACTION:
                return __('Canceled by user');
            case self::FORMAT_ERROR:
                return __('Format error');
            case self::FRAUD_SUSPICION:
                return __('Fraud suspicion');
            case self::ATTEMPTS_EXCEEDED:
                return __('Number of attempts of card\'s number seizure is exceeded');
            case self::SERVICE_TEMPORARY_UNAVAILABLE:
                return __('Service temporarily unavailable');
            case self::TRANSACTION_ALREADY_SAVED:
                return __('Transaction already saved');
            default:
                return __('Rejected ATOS Transaction - invalid code %s', $this->getResponseCode());
        }
    }

    /**
     * @return mixed
     */
    public function getBankResponseCode() {
        return (string) $this->getTransactionData()->getBankResponseCode();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getBankResponseMessage() {
        switch ($this->getBankResponseCode()) {
            case '00':
                return __('Transaction approved or treated with success');
            case '02':
                return __('Contact card issuer');
            case '03':
                return __('Invalid acceptor');
            case '04':
                return __('Keep the card');
            case '05':
                return __('Do not honor');
            case '07':
                return __('Keep the card, special conditions');
            case '08':
                return __('Approve after identification');
            case '12':
                return __('Invalid transaction');
            case '13':
                return __('Invalid amount');
            case '14':
                return __('Invalid carrier number');
            case '15':
                return __('Unknown card issuer');
            case '30':
                return __('Format error');
            case '31':
                return __('Unknown buyer body identifier');
            case '33':
            case '54':
                return __('Card validity date exceeded');
            case '34':
            case '59':
                return __('Fraud suspicion');
            case '41':
                return __('Lost card');
            case '43':
                return __('Stolen card');
            case '51':
                return __('Insufficient reserve or exceeded credit');
            case '56':
                return __('Card absent in the file');
            case '57':
                return __('Transaction not allowed to this carrier');
            case '58':
                return __('Transaction forbidden to terminal');
            case '60':
                return __('The card acceptor has to contact the buyer');
            case '61':
                return __('Exceed the limit of the retreat amount');
            case '63':
                return __('Safety rules not respected');
            case '68':
                return __('Response not reached or received too late');
            case '90':
                return __('System Temporary Stoppage');
            case '91':
                return __("Inaccessible card issuer");
            case '96':
                return __('System malfunction');
            case '97':
                return __('Term of the time-lag of global surveillance');
            case '98':
                return __('Server unavailable, network routing asked again');
            case '99':
                return __('Initiator domain incident');
        }
    }

    /**
     * @return mixed
     */
    public function getComplementaryCode() {
        return (string) $this->getTransactionData()->getComplementaryCode();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getComplementaryMessage() {
        switch ($this->getComplementaryCode()) {
            case '00':
                return __('All the controls to which you subscribed were made successfully');
            case '02':
                return __('The used card exceeded the authorized outstanding');
            case '03':
                return __('The used card belongs to the merchant\'s grey list');
            case '05':
                return __('The BIN of the used card belongs to a range not referenced in the table of BIN of the MERCANET platform');
            case '06':
                return __('The card number is not in a range of the same nationality as that of the merchant');
            case '99':
                return __('The MERCANET server encountered a problem during the processing of one of the additional local controls');
        }
    }
    
    
    /**
     * @return mixed
     */
    public function getTransactionData() {

        $data = array();

        list (,
            $data['code'],
            $data['debug'],
            $data['merchant_id'],
            $data['merchant_country'],
            $data['amount'],
            $data['transaction_id'],
            $data['payment_means'],
            $data['transmission_date'],
            $data['payment_time'],
            $data['payment_date'],
            $data['response_code'],
            $data['payment_certificate'],
            $data['authorisation_id'],
            $data['currency_code'],
            $data['card_number'],
            $data['cvv_flag'],
            $data['cvv_response_code'],
            $data['bank_response_code'],
            $data['complementary_code'],
            $data['complementary_info'],
            $data['return_context'],
            $data['caddie'],
            $data['receipt_complement'],
            $data['merchant_language'],
            $data['language'],
            $data['customer_id'],
            $data['order_id'],
            $data['customer_email'],
            $data['customer_ip_address'],
            $data['capture_day'],
            $data['capture_mode'],
            $data['data'],
            $data['order_validity'],
            $data['transaction_condition'],
            $data['statement_reference'],
            $data['card_validity'],
            $data['score_value'],
            $data['score_color'],
            $data['score_info'],
            $data['score_threshold'],
            $data['score_profile']
        ) = $this->getSips();

        return $this->dataObjectFactory->create([
            'data' => $data
        ]);
    }
}