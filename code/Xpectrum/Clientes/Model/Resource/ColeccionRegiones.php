<?php
namespace Xpectrum\Clientes\Model\Resource;
//use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;

class Regiones extends AbstractCollection{
	protected function _construct(){
		$this->_init(
			'Xpectrum\Clientes\Model\Regiones',
			'Xpectrum\Clientes\Model\Resource\Regiones'
		);
	}
}