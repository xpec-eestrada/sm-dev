<?php
namespace Xpectrum\Clientes\Block\Address;

class Edit extends \Magento\Customer\Block\Address\Edit
{
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    protected $_regiones;
    protected $_provincias;
    protected $_comunas;
    protected $_customer;
    protected $_idreg;
    protected $_http;
    protected $_idattributeRegion;
    protected $_idattributeProvincia;
    protected $_idattributeComuna;

    protected $_valueRegion;
    protected $_valueProvincia;
    


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Registry $registry,
        \Xpectrum\Clientes\Model\Resource\RegionesFactory $regiones,
        \Xpectrum\Clientes\Model\Resource\ProvinciasFactory $provincias,
        \Xpectrum\Clientes\Model\Resource\ComunasFactory $comunas,
        \Magento\Framework\App\Request\Http $http,
        array $data = []
    ){
        $this->_http=$http;
        $this->_registry = $registry;
        $this->_regiones = $regiones;
        $this->_provincias = $provincias;
        $this->_comunas = $comunas;
        $this->_customer=$customerSession;
        parent::__construct($context, $directoryHelper, $jsonEncoder, $configCacheType, $regionCollectionFactory, $countryCollectionFactory, $customerSession, $addressRepository, $addressDataFactory, $currentCustomer, $dataObjectHelper, $data);
    }


    public function _toHtml(){
        $this->loadDBAtributo();
        $this->_idreg=($this->_http->getParam('id')!==null)?$this->getRequest()->getParam('id'):0;
        if ($this->getTemplate() == 'address/edit.phtml'){
            $this->setTemplate('Xpectrum_Clientes::address/custom.phtml');
            $addressCandidatesHtml = $this->fetchView($this->getTemplateFile());
            return $addressCandidatesHtml;
        }
        
        return parent::_toHtml();
    }
    public function getRegionAll(){
        $reg = $this->_regiones->create();
        return $reg->getRegion();
    }
    public function getUrlAjax(){
        return $this->getUrl('direcciones/index/ajaxprovincia');
    }
    public function getUrlAjaxComunas(){
        return $this->getUrl('direcciones/index/ajaxcomuna');
    }
    public function getSaveUrlAjax(){
        return $this->getUrl('direcciones/index/insertardatos');
    }
    public function getCustomerId(){
        //return ($this->_customer->getId()!==null)?$this->_customer->getId():0;
        return $this->_customer->getId();
    }
    public function getSaveUrl(){
        $url=($this->_idreg==0)?'direcciones/address/grabar':'direcciones/address/editar';
        return $this->getUrl($url);
    }
    public function getDireccionId(){
        return $this->_idreg;
    }
    public function getIdAttributeRegion(){
        return $this->_idattributeRegion;
    }
    public function getIdAttributeProvincia(){
        return $this->_idattributeProvincia;
    }
    public function getIdAttributeComuna(){
        return $this->_idattributeComuna;
    }
    private function loadDBAtributo(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('eav_attribute');
        $sql='SELECT attribute_id
                    FROM 
                        '.$tableName.' 
                    WHERE
                        entity_type_id=2 AND attribute_code=\'cmbregion\' ';
        $result = $connection->fetchAll($sql);
        $this->_idattribute_region=0;
        foreach($result as $item){
            $this->_idattributeRegion=$item['attribute_id'];
        }

        $sql='SELECT attribute_id
                    FROM 
                        '.$tableName.' 
                    WHERE
                        entity_type_id=2 AND attribute_code=\'cmbprovincias\' ';
        $result = $connection->fetchAll($sql);
        $this->_idattribute_provincia=0;
        foreach($result as $item){
            $this->_idattributeProvincia=$item['attribute_id'];
        }

        $sql='SELECT attribute_id
                    FROM 
                        '.$tableName.' 
                    WHERE
                        entity_type_id=2 AND attribute_code=\'cmbcomunas\' ';
        $result = $connection->fetchAll($sql);
        $this->_idattribute_provincia=0;
        foreach($result as $item){
            $this->_idattributeComuna=$item['attribute_id'];
        }
    }
    public function getIdAttributeComunas(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('eav_attribute');
        $sql='SELECT attribute_id
                    FROM 
                        '.$tableName.' 
                    WHERE
                        entity_type_id=2 AND attribute_code=\'cmbcomunas\' ';
        $result = $connection->fetchAll($sql);
        $id=0;
        foreach($result as $item){
            $id=$item['attribute_id'];
        }
        return $id;
    }
    public function getOptionRegiones(){
        $iddireccion=$this->getDireccionId();
        $idatributo=$this->getIdAttributeRegion();
        $reg = $this->_regiones->create();
        $options=$reg->getOptionRegion($iddireccion,$idatributo);
        $this->_valueRegion=$reg->getValorSeleccionado();
        return $options;

    }
    public function getOptionProvincias(){
        $iddireccion=$this->getDireccionId();
        $idatributo=$this->getIdAttributeProvincia();
        $provi = $this->_provincias->create();
        $val=$provi->getOptionsProvincias($this->_valueRegion,$iddireccion,$idatributo);
        $this->_valueProvincia=$provi->getSelectedProvincia();
        return $val;
    }
    public function getOptionComunas(){
        $idprovincia=$this->_valueProvincia;
        $idregion=$this->_valueRegion;
        $comuna=$this->_comunas->create();
        $atributo=$this->getIdAttributeComunas();
        $iddireccion=$this->getDireccionId();
        return $comuna->getOptionsComunas($idregion,$idprovincia,$atributo,$iddireccion);
    }

}
?>