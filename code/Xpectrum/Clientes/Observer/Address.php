<?php
namespace Xpectrum\Clientes\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\ObjectManager;

class Address implements ObserverInterface
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
        // $address1 = $observer->getEvent()->getCustomerAddress();
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $address = $objectManager->create('Magento\Customer\Model\Address')->load($address1->getId());

        // $this->logger->info("Incio transaccion: ".$address1->getId());
        // //$address = ObjectManager::getInstance()->create("\Magento\Customer\Model\Address")->load($address1->getId() );
        // $this->logger->info("Obtener valor: ".$address->getCustomAttribute('cmbregion'));
        // $address->setCustomAttribute('cmbregion',22);
        // //$address->save();
        // $this->logger->info("Grabo region Observer new");
    }

}