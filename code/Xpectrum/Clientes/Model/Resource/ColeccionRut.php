<?php
namespace Xpectrum\Clientes\Model\Resource;
use Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;

class Rut extends AbstractCollection{
	protected function _construct(){
		$this->_init(
			'Xpectrum\Clientes\Model\Rut',
			'Xpectrum\Clientes\Model\Resource\Rut'
		);
	}
}