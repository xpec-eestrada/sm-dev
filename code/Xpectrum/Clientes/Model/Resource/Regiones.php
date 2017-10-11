<?php
namespace Xpectrum\Clientes\Model\Resource;

class Regiones extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    private $_valorregion;
	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

	protected function _construct(){
		$this->_init('xpec_regiones', 'regiones_id');
        $this->_valorregion=0;
	}
	public function getAll(){
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getMainTable());
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getRegion(){
        $adapter = $this->getConnection();
        $select = $adapter->select()
        	->from($this->getMainTable())
        	->Order('regiones_orden','ASC');
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getOptionRegion($iddireccion,$idatributo){
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
        $this->_valorregion=$value;
        $adapter = $this->getConnection();
        $select = $adapter->select()
        	->from($this->getMainTable())
        	->Order('regiones_orden','ASC');
        $data = $adapter->fetchAll($select);
        $arrayoptions='<option value=\'\'>Seleccione</option>';
        foreach($data as $item){
            $selected=($item['regiones_id']==$value)?' selected=\'selected\' ':'';
            $arrayoptions=$arrayoptions.'<option '.$selected.' value=\''.$item['regiones_id'].'\'>'.$item['regiones_nombre'].'</option>';
        }
        return $arrayoptions;
    }
    public function getValorSeleccionado(){
        return $this->_valorregion;
    }
}