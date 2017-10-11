<?php
namespace Xpectrum\Clientes\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\AddressSetupFactory
     */
    private $customerSetupFactory;
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\AddressSetupFactory $customerSetupFactory
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
        )
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try{
            $setup->startSetup();

            /* Attributo Text Rut */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "rut",  array(
                "type"     => "varchar",
                "label"    => "Rut",
                "input"    => "text",
                "visible"  => true,
                "required" => true,
                "system"   => 0,
                "position" => 151

            ));
            $attribute = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'rut');
            $used_in_forms[]="adminhtml_customer";
            $used_in_forms[]="checkout_register";
            $used_in_forms[]="customer_account_create";
            $used_in_forms[]="customer_account_edit";
            $attribute->setData("used_in_forms", $used_in_forms);
            $attribute->save();

            /* Attributo Drop Down List Regiones */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer_address', "cmbregion",  array(
                "type"     => "int",
                "label"    => "Region",
                "input"    => "select",
                "visible"  => true,
                "required" => false,
                "system"   => 0,
                'user_defined' => true,
                'sort_order' => 152,
                "position" => 152,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => 'Xpectrum\Clientes\Model\Config\Source\Options',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

            ));
            $dropdownlist = $customerSetup->getEavConfig()->getAttribute('customer_address', 'cmbregion');
            $used_in_forms=array();
            $used_in_forms[]="adminhtml_customer_address";
            $used_in_forms[]="customer_address_edit";
            $used_in_forms[]="customer_register_address";
            $used_in_forms[]="customer_address";

            $dropdownlist->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 152);
            $dropdownlist->save();

            /* Attributo Drop Down List Provincias */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer_address', "cmbprovincias",  array(
                "type"     => "int",
                "label"    => "Provincias",
                "input"    => "select",
                "visible"  => true,
                "required" => false,
                "system"   => 0,
                'user_defined' => true,
                'sort_order' => 153,
                "position" => 153,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => 'Xpectrum\Clientes\Model\Config\Source\OptionsProvincias',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

            ));
            $dropdownlist = $customerSetup->getEavConfig()->getAttribute('customer_address', 'cmbprovincias');
            $used_in_forms=array();
            $used_in_forms[]="adminhtml_customer_address";
            $used_in_forms[]="customer_address_edit";
            $used_in_forms[]="customer_register_address";
            $used_in_forms[]="customer_address";

            $dropdownlist->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 153);
            $dropdownlist->save();

            /* Attributo Drop Down List Comunas */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer_address', "cmbcomunas",  array(
                "type"     => "int",
                "label"    => "Comunas",
                "input"    => "select",
                "visible"  => true,
                "required" => false,
                "system"   => 0,
                'user_defined' => true,
                'sort_order' => 154,
                "position" => 154,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => 'Xpectrum\Clientes\Model\Config\Source\OptionsComunas',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

            ));
            $dropdownlist = $customerSetup->getEavConfig()->getAttribute('customer_address', 'cmbcomunas');
            $used_in_forms=array();
            $used_in_forms[]="adminhtml_customer_address";
            $used_in_forms[]="customer_address_edit";
            $used_in_forms[]="customer_register_address";
            $used_in_forms[]="customer_address";

            $dropdownlist->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 154);
            $dropdownlist->save();

            $setup->endSetup();
        }catch(Exception $err){
            $this->log(date('H:i:s') . ' - Error:: ' . $err->getMessage());
        }
    }
    public function log($msg, $mode = 'a')
    {
        $pathFile =  '/var/www/html/var/log/InstallData-' . date('Y-m-d') . '.txt';
        $handle = fopen($pathFile, $mode);
        fwrite($handle, $msg . "\n");
        fclose($handle);
    }
}
