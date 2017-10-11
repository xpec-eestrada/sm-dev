<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Insync\ExtendedApi\Model\Order;

use Insync\ExtendedApi\Api\InvoiceRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
//use Insync\ExtendedApi\Api\Data\InvoiceSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Api\Data\InvoiceSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Model\Service\InvoiceService;
//use Magento\Framework\Registry;
use Magento\Sales\Model\Order;



/**
 * Class InvoiceRepository
 */
class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;
    
    private $invoiceService;
    
    protected $registry = [];

    /**
     * Repository constructor
     *
     * @param Metadata $invoiceMetadata
     * @param SearchResultFactory $searchResultFactory
     */
     public function __construct(
        SearchResultFactory $searchResultFactory,
     	InvoiceService $invoiceService,
     	\Magento\Framework\ObjectManagerInterface $objectManager
     	//Registry $registry
    ) {
     	$this->_objectManager = $objectManager;
        $this->searchResultFactory = $searchResultFactory;
        $this->invoiceService = $invoiceService;
        //$this->registry = $registry;
    } 
    
    
    /**
     * Load entity
     *
     * @param int $id
     * @return mixed
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($id)
    {
    	if (!$id) {
    		throw new \Magento\Framework\Exception\InputException(__('ID required'));
    	}
    	if (!isset($this->registry[$id])) {
    		/** @var \Magento\Sales\Api\Data\InvoiceInterface $entity */
    		//$entity = $this->metadata->getNewInstance()->load($id);
    		$entity = $this->_objectManager->get('Magento\Sales\Model\Order\Invoice')->load($id);
    		if (!$entity->getEntityId()) {
    			throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
    		}
    		$this->registry[$id] = $entity;
    	}
    	return $this->registry[$id];
    }
	
	
	public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
	{
		/** @var \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection $collection */
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
    
    public function save(\Magento\Sales\Api\Data\InvoiceInterface $entity)
    
    {
		$invoiceItems=array();

    	foreach ($entity->getItems() as $val){
    		$quantity=$val->getQty();
    		$orderItemId=$val->getOrderItemId();
    		$invoiceItems[$orderItemId]=$quantity;
    	}    	
    	$orderId=$entity->getOrderId();
    	
    	$order = $this->_objectManager->get('Magento\Sales\Model\Order')->load($orderId);
    	
	
    	if (!$order->getId()) {
    		throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
    	}
    	
    	if (!$order->canInvoice()) {
    		throw new \Magento\Framework\Exception\LocalizedException(
    				__('The order does not allow an invoice to be created.')
    		);
    	}
    	
		$invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);
    	
    	if (!$invoice) {
    		throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the invoice right now.'));
    	}
    	
    	if (!$invoice->getTotalQty()) {
    		throw new \Magento\Framework\Exception\LocalizedException(
    				__('You can\'t create an invoice without products.')
    		);
    	}
    	
    	
    	//$this->registry->register('current_invoice', $invoice);
    	
    /* 	if (!empty($data['comment_text'])) {
    		$invoice->addComment(
    				$data['comment_text'],
    				isset($data['comment_customer_notify']),
    				isset($data['is_visible_on_front'])
    		);
    	
    	}
    	
    	$invoice->setCustomerNote($data['comment_text']); */
    	
    	$invoice->register();
    	

    	//$invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
    	$invoice->getOrder()->setIsInProcess(true);
    	
    	$transactionSave = $this->_objectManager->create(
    			'Magento\Framework\DB\Transaction'
    	)->addObject(
    			$invoice
    	)->addObject(
    			$invoice->getOrder()
    	);
    	
    	$transactionSave->save();
		
		$incrementId = $invoice->getIncrementId();
    	
		$invoiceId = $invoice->getEntityId();
    	
 		 return $this->get($invoiceId);
    		
    }
}
