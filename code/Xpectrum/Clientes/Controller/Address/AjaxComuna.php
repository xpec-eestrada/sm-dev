<?php

namespace Xpectrum\Clientes\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Xpectrum\Clientes\Model\Resource\ComunasFactory;

class AjaxComuna extends \Magento\Framework\App\Action\Action{

    protected $_resultPageFactory;
    protected $_comuna;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ComunasFactory $comuna,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_comuna = $comuna;
        $this->_resultPageFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return $this
     */
    public function execute(){  
        $post = $this->getRequest()->getPostValue();
        $obj = $this->_comuna->create();
        $idregion=(isset($post) && isset($post['id_region']) && is_numeric($post['id_region']))?$post['id_region']:0;
        $idprovincia=(isset($post) && isset($post['id_provincia']) && is_numeric($post['id_provincia']))?$post['id_provincia']:0;
        $datos=$obj->getComunasProvinciaRegion($idregion,$idprovincia);
        
        $result=$this->_resultPageFactory->create();
        $valor=array('result'=>$datos);
        return $result->setData($valor);
    }
}