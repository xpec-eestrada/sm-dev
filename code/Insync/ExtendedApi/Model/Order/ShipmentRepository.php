<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Insync\ExtendedApi\Model\Order;

use Insync\ExtendedApi\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;




class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;
    
    protected $metadata;
    
    protected $shipmentLoader;
    
    protected $labelGenerator;
    
    protected $registry = [];


    public function __construct(
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
    		\Magento\Framework\ObjectManagerInterface $objectManager,
    		\Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->_objectManager = $objectManager;
        $this->labelGenerator = $labelGenerator;
    }
    
    
    /**
     * Loads a specified shipment.
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
    	if (!$id) {
    		throw new InputException(__('Id required'));
    	}
    
    	if (!isset($this->registry[$id])) {
    		/** @var \Magento\Sales\Api\Data\ShipmentInterface $entity */
    		//$entity = $this->metadata->getNewInstance()->load($id);
    		$entity = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($id);
    
    		if (!$entity->getEntityId()) {
    			throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
    		}
    
    		$this->registry[$id] = $entity;
    	}
    
    	return $this->registry[$id];
    }
    
    
    protected function _saveShipment($shipment)
    {
    	$shipment->getOrder()->setIsInProcess(true);
    	$transaction = $this->_objectManager->create(
    			'Magento\Framework\DB\Transaction'
    	);
    	$transaction->addObject(
    			$shipment
    	)->addObject(
    			$shipment->getOrder()
    	)->save();
    
    	return $this;
    }
	
	public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
	{
		$collection = $this->searchResultFactory->create();
		foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
			foreach ($filterGroup->getFilters() as $filter) {
				$condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
				$collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
			}
		}
		$collection->setSearchCriteria($searchCriteria);
		$collection->setCurPage($searchCriteria->getCurrentPage());
		$collection->setPageSize($searchCriteria->getPageSize());
		return $collection;
	}
    
    public function save(\Magento\Sales\Api\Data\ShipmentInterface $entity)
    
    {
    	
    	try{
	    	$data = array ();
			$tracking=array();
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	
    		$order = $objectManager->get('Magento\Sales\Model\Order')->load($entity->getOrderId());
    		if (!$order->getId()) {
    			throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
    		}
    		/**
    		 * Check shipment create availability
    		 */
    		if (!$order->canShip()) {
    			throw new \Magento\Framework\Exception\LocalizedException(__('Cannot do shipment for order.'));
    		}
    		
    		//item
    		foreach ($entity->getItems() as $val){
    			$quantity=$val->getQty();
    			$orderItemId=$val->getOrderItemId();
    			$data['items'][$orderItemId]=$quantity;
    		}
    		//track
    		if($entity->getTracks()){
    			foreach ($entity->getTracks() as $val){
    				$carrierCode=$val->getCarrierCode();
    				$Title=$val->getTitle();
    				$trackNumber=$val->getTrackNumber();
    				$tracking[]=array(
    						'carrier_code' => $carrierCode,
    						'title' => $Title,
    						'number' => $trackNumber,
    				);
    			}
           		$this->shipmentLoader->setTracking($tracking);	//for tracking
    		}
    		
    		$this->shipmentLoader->setOrderId($entity->getOrderId());
            $this->shipmentLoader->setShipment($data);
            $shipment = $this->shipmentLoader->load();
    
            //comment
           /*  if (!empty($data['comment_text'])) {
            	$shipment->addComment(
            			$data['comment_text'],
            			isset($data['comment_customer_notify']),
            			isset($data['is_visible_on_front'])
            	);
            
            	$shipment->setCustomerNote($data['comment_text']);
            	$shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            } */
            
            
            
		    if ($order->canShip())
		    {
		      $shipment->register();
		      $shipmentData=$this->_saveShipment($shipment);
		      
		      $shipmentId = $shipment->getId();
		      
		      return $this->get($shipmentId);
			  
		    }
      		return 'sorry shipment can not be created';
      
      		}
    	
    	catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save shipment'), $e);
        }
    }
    
}
