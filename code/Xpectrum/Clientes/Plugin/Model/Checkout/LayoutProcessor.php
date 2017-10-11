<?php
namespace Xpectrum\Clientes\Plugin\Model\Checkout;
class LayoutProcessor 
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
     
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('smxpec_regiones');
        $sql="SELECT 
                    regiones_id as value,
                    regiones_nombre as label
                FROM
                    ".$tableName." 
                ORDER BY regiones_orden ASC";
        $result = $connection->fetchAll($sql);
        $data=array();
        $data[]=array(
                    'value' => '',
                    'label' => 'Seleccione'
                    );
        foreach($result as $item){
            $data[]=$item;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id_custom'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'region_id_custom',
            ],
            'dataScope' => 'shippingAddress.cmbregion',
            'label' => 'Region',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 85,
            'id' => 'region_id_custom',
            'options' => $data
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['provincia_id'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'provincia_id',
            ],
            'dataScope' => 'shippingAddress.cmbprovincias',
            'label' => 'Provincia',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 86,
            'id' => 'provincia_id',
            'options' => [
                [
                    'value' => '',
                    'label' => 'Seleccione',
                ]
            ]
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'region_id',
            ],
            'dataScope' => 'shippingAddress.region_id',
            'label' => 'Comuna',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 87,
            'id' => 'region_id',
            'options' => [
                [
                    'value' => '',
                    'label' => 'Seleccione',
                ]
            ]
        ];
        return $jsLayout;
    }
}