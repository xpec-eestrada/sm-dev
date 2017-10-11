<?php
namespace Insync\ExtendedApi\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddressUpdateObserver implements ObserverInterface {
	
	protected $date;
	public function __construct(
			\Magento\Framework\Stdlib\DateTime\DateTime $date
	) {
		$this->date = $date;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		$address = $observer->getCustomerAddress();
		if(!$address->hasDataChanges())
		{
			return $this;
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerData=$objectManager->create('Magento\Customer\Model\Customer')->load($address->getCustomerId());
		$customerData->setUpdatedAt($this->date->gmtDate());
		$customerData->save();
	}
}