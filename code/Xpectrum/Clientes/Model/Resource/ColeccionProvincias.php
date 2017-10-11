<?php
namespace Xpectrum\Clientes\Model\Resource;
use Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;

class Provincias extends AbstractCollection{
	protected function _construct(){
		$this->_init(
			'Xpectrum\Clientes\Model\Provincias',
			'Xpectrum\Clientes\Model\Resource\Provincias'
		);
	}
}