<?php
namespace Xpectrum\ProductNotSalable\Plugin;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\DataObject;


class ValidateProducts{
    protected $customerSession;

    protected $logger;

    protected $resultJsonFactory;
    /**
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * 
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository     
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        //$this->logger = $logger;
        $this->customerSession=$customerSession;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/xpec_ValidateProductsPriceInvailid.log'  );
        $loggerxpec = new \Zend\Log\Logger();
        $loggerxpec->addWriter($writer);

        $quote = $this->quoteRepository->getActive($cartId);
        $items = $quote->getItems();
        foreach($items as $item){
            if($item->getPrice()<=0){
                $temp='';
                $temp=$temp.'ID: '.$item->getProductId().'*';
                $temp=$temp.'Name: '.$item->getName().'*';
                $temp=$temp.'Sku: '.$item->getSku().'*';
                $temp=$temp.'Quantity: '.$item->getQty().'*';
                $temp=$temp.'Price: '.$item->getPrice().'*';
                $mensaje='Uno de los productos tiene precio invalido.';
                $loggerxpec->info($temp);
                throw new InputException(__($mensaje));
            }
        }
    }
}