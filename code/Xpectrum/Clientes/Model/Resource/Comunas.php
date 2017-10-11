<?php
namespace Xpectrum\Clientes\Model\Resource;

class Comunas extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{

	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

	protected function _construct(){
		$this->_init('smxpec_comunas', 'comunas_id');
	}
	public function getAll(){
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getMainTable());
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getComunasProvinciaRegion($region_id,$provincia_id){
        $adapter = $this->getConnection();
        $select = $adapter->select()
        	->from($this->getMainTable())
            ->where('regiones_id=?',$region_id)
            ->where('provincia_id=?',$provincia_id)
        	->Order('comunas_orden','ASC');
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getOptionsComunas($region_id,$provincia_id,$atributo,$direccion_id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('customer_address_entity_int');
        $sql='SELECT value
                    FROM 
                        '.$tableName.' 
                    WHERE
                        attribute_id='.$atributo.' AND entity_id='.$direccion_id;
        $result = $connection->fetchAll($sql);
        $valor=0;
        foreach($result as $item){
            $valor=$item['value'];
        }
        $array=$this->getComunasProvinciaRegion($region_id,$provincia_id);
        $option='<option value=\'\'>Seleccione</option>';
        foreach($array as $item){
            $select=($item['comunas_id']==$valor)?' selected=\'selected\' ':'';
            $option=$option.'<option '.$select.' value=\''.$item['comunas_id'].'\'>'.$item['comunas_nombre'].'</option>';
        }
        return $option;

    }
}