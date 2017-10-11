<?php
namespace Xpectrum\Clientes\Model\Resource;

class Provincias extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    private $_select;
	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

	protected function _construct(){
        $this->_select=0;
		$this->_init('xpec_provincias', 'provincia_id');
	}
	public function getAll(){
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getMainTable());
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getProvinciaRegion($region_id){
        $adapter = $this->getConnection();
        $select = $adapter->select()
        	->from($this->getMainTable())
            ->where('regiones_id=?',$region_id)
        	->Order('provincia_orden','ASC');
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getOptionsProvincias($idregion,$iddireccion,$idatributo){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('customer_address_entity_int');
        $sql='SELECT value
                    FROM 
                        '.$tableName.' 
                    WHERE
                        attribute_id='.$idatributo.' AND entity_id='.$iddireccion;
        $result = $connection->fetchAll($sql);
        $value=0;
        foreach($result as $item){
            $value=$item['value'];
        }
        $this->_select=$value;
        $tableName = $resource->getTableName('xpec_provincias');
        $sql='SELECT provincia_id,provincia_nombre
                    FROM 
                        '.$tableName.' 
                    WHERE
                        regiones_id='.$idregion.' 
                    ORDER BY 
                        provincia_orden ASC';
        $result = $connection->fetchAll($sql);
        $options='<option value=\'\' >Seleccione</option>';
        foreach($result as $item){
            $selected=($item['provincia_id']==$value)?' selected=\'selected\' ':'';
            $options=$options.'<option '.$selected.' value=\''.$item['provincia_id'].'\' >'.$item['provincia_nombre'].'</option>';
        }
        return $options;
        
    }
    public function getSelectedProvincia(){
        return $this->_select;
    }
}