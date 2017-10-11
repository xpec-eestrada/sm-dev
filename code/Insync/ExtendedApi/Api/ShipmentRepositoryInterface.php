<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
* See COPYING.txt for license details.
*/
namespace Insync\ExtendedApi\Api;

/**
 * Order repository interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 * @api
 */
interface ShipmentRepositoryInterface
{
    /**
     * Loads a specified shipment.
     *
     * @param int $id The shipment ID.
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    public function get($id);

    /**
     * Performs persist operations for a specified shipment.
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $entity The shipment.
     * @return \Magento\Sales\Api\Data\ShipmentInterface Shipment interface.
     */
    public function save(\Magento\Sales\Api\Data\ShipmentInterface $entity);

    
}
