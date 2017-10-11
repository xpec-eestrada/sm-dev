<?php
namespace Xpectrum\Clientes\Model\Resource;

class Rut extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{

	public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

	protected function _construct(){
		$this->_init('smxpec_rut', 'rut_id');
	}
	public function getAll(){
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getMainTable());
        $data = $adapter->fetchAll($select);
        return $data;
    }
    public function getRut($customer_id){
        $adapter = $this->getConnection();
        $select = $adapter->select()
        	->from($this->getMainTable());
        $data = $adapter->fetchOne($select);
        return $data->rut_rut;
    }
    public function insertar($cliente,$rutval){
        try{
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();

            $sql = "Insert Into ".$this->getMainTable()." (rut_cliente_id,rut_rut) Values (".$cliente.",'".$rutval."')";
            $connection->query($sql);
        }catch(Exception $err){
            throw new Exception($err->getMessage());
        }
    }
}