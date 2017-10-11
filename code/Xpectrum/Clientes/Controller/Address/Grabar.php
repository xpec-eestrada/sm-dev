<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xpectrum\Clientes\Controller\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as HelperData;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grabar extends \Magento\Customer\Controller\Address
{
    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Mapper
     */
    private $customerAddressMapper;

    public $logger;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param FormFactory $formFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressDataFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param DataObjectProcessor $dataProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param RegionFactory $regionFactory
     * @param HelperData $helperData
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        FormFactory $formFactory,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        DataObjectProcessor $dataProcessor,
        DataObjectHelper $dataObjectHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        RegionFactory $regionFactory,
        HelperData $helperData,
        \Xpectrum\Clientes\Logger\Logger $logger
    ) {
        $this->regionFactory = $regionFactory;
        $this->helperData = $helperData;
        $this->logger = $logger;
        parent::__construct(
            $context,
            $customerSession,
            $formKeyValidator,
            $formFactory,
            $addressRepository,
            $addressDataFactory,
            $regionDataFactory,
            $dataProcessor,
            $dataObjectHelper,
            $resultForwardFactory,
            $resultPageFactory
        );
    }

    /**
     * Extract address from request
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    protected function _extractAddress()
    {
        $existingAddressData = $this->getExistingAddressData();

        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create('customer_address', 'customer_address_edit', $existingAddressData);
        $addressData = $addressForm->extractData($this->getRequest());
        $attributeValues = $addressForm->compactData($addressData);

        $this->updateRegionData($attributeValues);

        $addressDataObject = $this->addressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            array_merge($existingAddressData, $attributeValues),
            '\Magento\Customer\Api\Data\AddressInterface'
        );
        $addressDataObject->setCustomerId($this->_getSession()->getCustomerId())
            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));

        return $addressDataObject;
    }

    /**
     * Retrieve existing address data
     *
     * @return array
     * @throws \Exception
     */
    protected function getExistingAddressData()
    {
        $existingAddressData = [];
        if ($addressId = $this->getRequest()->getParam('id')) {
            $existingAddress = $this->_addressRepository->getById($addressId);
            if ($existingAddress->getCustomerId() !== $this->_getSession()->getCustomerId()) {
                throw new \Exception();
            }
            $existingAddressData = $this->getCustomerAddressMapper()->toFlatArray($existingAddress);
        }
        return $existingAddressData;
    }

    /**
     * Update region data
     *
     * @param array $attributeValues
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function updateRegionData(&$attributeValues)
    {
        if (!empty($attributeValues['region_id'])) {
            $newRegion = $this->regionFactory->create()->load($attributeValues['region_id']);
            $attributeValues['region_code'] = $newRegion->getCode();
            $attributeValues['region'] = $newRegion->getDefaultName();
        }

        $regionData = [
            RegionInterface::REGION_ID => !empty($attributeValues['region_id']) ? $attributeValues['region_id'] : null,
            RegionInterface::REGION => !empty($attributeValues['region']) ? $attributeValues['region'] : null,
            RegionInterface::REGION_CODE => !empty($attributeValues['region_code'])
                ? $attributeValues['region_code']
                : null,
        ];

        $region = $this->regionDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $region,
            $regionData,
            '\Magento\Customer\Api\Data\RegionInterface'
        );
        $attributeValues['region'] = $region;
    }

    /**
     * Process address form save
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirectUrl = null;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_buildUrl('*/*/edit'))
            );
        }

        try {
            /*$address = $this->_extractAddress();
            $this->_addressRepository->save($address);*/
            $this->grabardatospersona();
            $this->messageManager->addSuccess(__('You saved the address.'));
            //$url = $this->_buildUrl('*/*/index', ['_secure' => true]);
            $url = $this->_buildUrl('customer/address/index', ['_secure' => true]);
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        } catch (InputException $e) {
            $this->messageManager->addError($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($error->getMessage());
            }
        } catch (\Exception $e) {
            //$redirectUrl = $this->_buildUrl('*/*/index');
            $redirectUrl = $this->_buildUrl('customer/address/index');
            $this->messageManager->addException($e, __('We can\'t save the address.'));
        }

        $url = $redirectUrl;
        if (!$redirectUrl) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            $url = $this->_buildUrl('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($url));
    }
    protected function grabardatospersona(){
        try{
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $connection->beginTransaction();
            $tableName = $resource->getTableName('customer_address_entity');

            $post = $this->getRequest()->getPostValue();
            $customer_id   = (isset($post) && isset($post['entidad_id']) && is_numeric($post['entidad_id']))?$post['entidad_id']:0;
            $direccionid   = (isset($post) && isset($post['address_id']) && is_numeric($post['address_id']))?$post['address_id']:0;
            $firstname     = (isset($post) && isset($post['firstname']))?$post['firstname']:'';
            $lastname      = (isset($post) && isset($post['lastname']))?$post['lastname']:'';
            $street        = (isset($post) && isset($post['street'][0]))?$post['street'][0]:'';
            $street        = (isset($post) && isset($post['street'][1]) && !empty($post['street'][1]) )?$street.' '.$post['street'][1]:$street;
            $telephone     = (isset($post) && isset($post['telephone']))?$post['telephone']:'';
            $comuna        = (isset($post) && isset($post['comuna_id']) && is_numeric($post['comuna_id']))?$post['comuna_id']:0;
            $defa_billin   = (isset($post) && isset($post['default_billing']) && is_numeric($post['default_billing']))?$post['default_billing']:0;
            $defa_shipin   = (isset($post) && isset($post['default_shipping']) && is_numeric($post['default_shipping']))?$post['default_shipping']:0;
            $ciudad        = (isset($post) && isset($post['ciudad']))?$post['ciudad']:'';
            $direccionid   = 0;
            $fcrea=date("Y-m-d h:i:s");

            $newaddresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
            $newaddress = $newaddresss->create();
            $newaddress->setCustomerId($customer_id)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setCountryId('CL')
            ->setRegionId($comuna)
            ->setPostcode('0')
            ->setCity($ciudad)
            ->setTelephone($telephone)
            ->setStreet($street)
            ->setIsDefaultBilling($defa_billin)
            ->setIsDefaultShipping($defa_shipin)
            ->setSaveInAddressBook('1');
            try{
                $newaddress->save();
                $direccionid=$newaddress->getId();
            }
            catch (Exception $e) {
                $this->logger->info("no Grabo region editar. ".$e->getMessage());
                Zend_Debug::dump($e->getMessage());
            }
            
            
            $region     = (isset($post) && isset($post['region_id']) && is_numeric($post['region_id']))?$post['region_id']:0;
            $provincia  = (isset($post) && isset($post['provincia_id']) && is_numeric($post['provincia_id']))?$post['provincia_id']:0;
            
            $attregion  = (isset($post) && isset($post['id_attribute_region']) && is_numeric($post['id_attribute_region']))?$post['id_attribute_region']:0;
            $attprovincia  = (isset($post) && isset($post['id_attribute_provincia']) && is_numeric($post['id_attribute_provincia']))?$post['id_attribute_provincia']:0;
            $attcomuna  = (isset($post) && isset($post['id_attribute_comuna']) && is_numeric($post['id_attribute_comuna']))?$post['id_attribute_comuna']:0;
            
            /* Region */
            $this->guardarUbicacionSelect($resource,$connection,$attregion,$direccionid,$region);
            /* Provincia */
            $this->guardarUbicacionSelect($resource,$connection,$attprovincia,$direccionid,$provincia);
            /* Comuna */
            $this->guardarUbicacionSelect($resource,$connection,$attcomuna,$direccionid,$comuna);
            
            $connection->commit();
        }catch(Exception $err){
            $connection->rollback();
            $this->logger->info("error: ".$err->getMessage());
            throw new Exception($err->getMessage());
        }
    }

    /**
     * Get Customer Address Mapper instance
     *
     * @return Mapper
     *
     * @deprecated
     */
    private function getCustomerAddressMapper()
    {
        if ($this->customerAddressMapper === null) {
            $this->customerAddressMapper = ObjectManager::getInstance()->get('Magento\Customer\Model\Address\Mapper');
        }
        return $this->customerAddressMapper;
    }
    protected function guardarUbicacionSelect($resource,$connection,$idattributo,$idaddress,$valor){
        try{
            $tableName = $resource->getTableName('customer_address_entity_int');
            $sql='SELECT 
                        value_id
                    FROM '.$tableName.'
                    WHERE
                        attribute_id='.$idattributo.' 
                        AND entity_id='.$idaddress;
            $result = $connection->fetchAll($sql);
            if((isset($result)) && (is_array($result)) && (count($result)>0)  ){
                $sql='UPDATE '.$tableName.' 
                    SET value = '.$valor.' 
                    WHERE
                        attribute_id='.$idattributo.' 
                        AND entity_id='.$idaddress;
                $connection->query($sql);
            }else{
                $sql='INSERT INTO '.$tableName.'(attribute_id,entity_id,value) 
                    VALUE('.$idattributo.','.$idaddress.','.$valor.')';
                $connection->query($sql);
            }
        }catch(Exception $err){
            throw new Exception($err->getMessage());
        }
    }
}
