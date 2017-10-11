<?php
namespace Xpectrum\CustomAddress\Setup;
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class Uninstall implements UninstallInterface{
    protected $eavSetupFactory;

    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory){
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $setup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "rut");
        $setup->endSetup();

        $setup->startSetup();    
        $eavSetup = $this->eavSetupFactory->create();    
        
        $entityTypeId = 1; // value for eav_entity_type table, here catalog_product value is 4
        $eavSetup->removeAttribute($entityTypeId, 'rut');

        $entityTypeId = 4; // value for eav_entity_type table, here catalog_product value is 4
        $eavSetup->removeAttribute($entityTypeId, 'rut');    
        
        
        $setup->endSetup(); 

    }
}