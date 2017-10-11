<?php
namespace Xpectrum\Clientes\Model\Resource;
use Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;

class Comunas extends AbstractCollection{
	protected function _construct(){
		$this->_init(
			'Xpectrum\Clientes\Model\Comunas',
			'Xpectrum\Clientes\Model\Resource\Comunas'
		);
	}
}