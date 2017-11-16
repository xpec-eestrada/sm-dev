<?php

namespace Xpectrum\Clientes\Block;

use Magento\Catalog\Block\Product\Context;

class DataAdicional extends \Magento\Framework\View\Element\Template{

    public function __construct(
            Context $context,
            array $data = []){
        // $this->_collectionFactory = $collectionFactory;
        // $this->_productRepository=$productRepository;
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        // $this->_baseUrl=$storeManager->getStore()->getBaseUrl();
        // $storeManagerInterface = $context->getStoreManager();
        // $this->_marca=new Marca($storeManagerInterface->getStore()->getId());

        parent::__construct($context, $data);
    }
    public function getInfo(){
        $data          = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource      = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection    = $resource->getConnection();
        $tableName     = $resource->getTableName('eav_attribute');
        $sql           = 'SELECT attribute_id FROM '.$tableName.' WHERE attribute_code=\'genero_sm\' ';
        $idgenero      = $connection->fetchOne($sql);
        $sql           = 'SELECT attribute_id FROM '.$tableName.' WHERE attribute_code=\'marca_sm\' ';
        $idmarca       = $connection->fetchOne($sql);
        $data[]        = array('code'=>'genero_sm','titulo'=>'Genero','id'=>$idgenero);
        $data[]        = array('code'=>'marca_sm','titulo'=>'Marca','id'=>$idmarca);
        return $data;

        //return 'id genero: '.$idgenero.' ---  id marca: '.$idmarca;
    }

}