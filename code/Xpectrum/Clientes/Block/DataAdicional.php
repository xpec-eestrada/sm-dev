<?php

namespace Xpectrum\Clientes\Block;

use Magento\Catalog\Block\Product\Context;

class DataAdicional extends \Magento\Framework\View\Element\Template{
    protected $registry;
    protected $product;

    public function __construct(
            Context $context,
            array $data = []){
        $this->registry = $context->getRegistry();
        parent::__construct($context, $data);
    }
    public function getInfo(){
        $data          = array();
        $idgenero      = $this->getIdAttribute('genero_sm');
        $idmarca       = $this->getIdAttribute('marca_sm');
        $data[]        = array('code'=>'genero_sm','titulo'=>'Genero','id'=>$idgenero);
        $data[]        = array('code'=>'marca_sm','titulo'=>'Marca','id'=>$idmarca);
        return $data;

        //return 'id genero: '.$idgenero.' ---  id marca: '.$idmarca;
    }
    public function getCurrentProduct(){
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');
            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }
        return $this->product;
    }
    public function getIdAttribute($attribute_code){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource      = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection    = $resource->getConnection();
        $tableName     = $resource->getTableName('eav_attribute');
        $sql           = 'SELECT attribute_id FROM '.$tableName.' WHERE attribute_code=\''.$attribute_code.'\' ';
        $id            = $connection->fetchOne($sql);
        return $id;
    }
    public function getAttributesCustom(){
        $product       = $this->getCurrentProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource      = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection    = $resource->getConnection();
        $tableName     = $resource->getTableName('catalog_product_relation');
        $sql           = 'SELECT child_id FROM streetmachine_prod.smcatalog_product_relation WHERE parent_id='.$product->getId();
        $ids           = $connection->fetchAll($sql);
        $toption       = $resource->getTableName('eav_attribute_option_value');
        foreach($ids as $id){
            $childProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
            if($childProduct->isInStock() && $childProduct->isAvailable() && $childProduct->isSaleable() ){
                echo '<pre>'.$childProduct->getData('talla_sm').'</pre>';
                $sql = 'SELECT value FROM '.$toption.' WHERE option_id='.$childProduct->getData('talla_sm');

            }

            
        }
    }
    //SELECT value FROM streetmachine_prod.smeav_attribute_option_value ov WHERE option_id=13138;

}