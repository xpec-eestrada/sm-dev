<?php
namespace Xpectrum\Clientes\Model\Config\Source;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
/**
* Custom Attribute Renderer
*/
class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource{
    /**
    * @var OptionFactory
    */
    protected $optionFactory;
    /**
    * @param OptionFactory $optionFactory
    */
    /**
    * Get all options
    *
    * @return array
    */
    public function getAllOptions(){
        /* your Attribute options list*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('xpec_regiones');
        $sql='SELECT regiones_id,regiones_nombre
                    FROM 
                        '.$tableName.' 
                    ORDER BY
                        regiones_orden ASC';
        $result = $connection->fetchAll($sql);
        $data=array();
        $data[]=array('label' => 'Seleccione','value' => '');
        foreach($result as $item){
            $data[]=array('label' => $item['regiones_nombre'],'value' => $item['regiones_id']);
        }
        return $data;
    }
}
