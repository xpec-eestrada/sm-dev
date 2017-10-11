<?php
namespace Xpectrum\Clientes\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\ObjectManager;

class CustomerCustomField implements ObserverInterface
{
    
    /**
    * @var \Magento\Framework\HTTP\ZendClientFactory
    */
    protected $_httpClientFactory;

    public $scopeConfig;

    protected $eav;

    public $logger;

    /**
    * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Xpectrum\Clientes\Logger\Logger $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig){
        $this->scopeConfig = $scopeConfig;
        $this->eav = $eavConfig;
        $this->_httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        //$this->logger->info("Grabo datos user");
    }

}