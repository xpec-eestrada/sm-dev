<?php
namespace Transbank\Webpay\Block\Checkout\Onepage\Success;

class PaymentData extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ){
        $this->checkoutSession = $checkoutSession;
        
        parent::__construct($context, $data);
    }
    
    protected function _toHtml()
    {
        $result = $this->checkoutSession->getHasPaidResult();

        if (isset($result)) {
            
            $this->checkoutSession->unsHasPaidResult();
            
        } else {
            
            $result = 0;
        }
        
        $this->addData(
            [
                'payment_details' => $result
            ]
        );
        
        return parent::_toHtml();
    }
}