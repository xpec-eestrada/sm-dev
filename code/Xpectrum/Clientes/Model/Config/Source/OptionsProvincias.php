<?php
namespace Xpectrum\Clientes\Model\Config\Source;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
/**
* Custom Attribute Renderer
*/
class OptionsProvincias extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource{
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
        $tableName = $resource->getTableName('xpec_provincias');
        $sql='SELECT provincia_id,provincia_nombre
                    FROM 
                        '.$tableName.' 
                    ORDER BY
                        provincia_orden ASC';
        $result = $connection->fetchAll($sql);
        $data=array();
        $data[]=array('label' => 'Seleccione','value' => '');
        foreach($result as $item){
            $data[]=array('label' => $item['provincia_nombre'],'value' => $item['provincia_id']);
        }
        return $data;
    }
}
