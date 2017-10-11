<?php

namespace Xpectrum\Clientes\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Xpectrum\Clientes\Model\Resource\RegionesFactory;
use Xpectrum\Clientes\Model\Resource\ProvinciasFactory;

class AjaxProvincia extends \Magento\Framework\App\Action\Action{

    protected $regiones;
    protected $_resultPageFactory;
    protected $_provicia;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RegionesFactory $regiones,
        ProvinciasFactory $provincia,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->regiones = $regiones;
        $this->_provicia = $provincia;
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
        $obj = $this->_provicia->create();
        $idregion=(isset($post) && isset($post['id_region']) && is_numeric($post['id_region']))?$post['id_region']:0;
        $datos=$obj->getProvinciaRegion($idregion);
        
        $result=$this->_resultPageFactory->create();
        $valor=array('result'=>$datos);
        return $result->setData($valor);
    }
}