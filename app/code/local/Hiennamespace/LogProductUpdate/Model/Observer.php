<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/21/15
 * Time: 3:08 PM
 */
class Hiennamespace_LogProductUpdate_Model_Observer
{
    /**
     * Magento passes a Varien_Event_Observer object as
     * the first parameter of dispatched events.
     */
    public function logUpdate(Varien_Event_Observer $observer)
    {
        // Retrieve the product being updated from the event observer
        $product = $observer->getEvent()->getProduct();

        // Write a new line to var/log/product-updates.log
        $name = $product->getName();
        $sku = $product->getSku();
        Mage::log(
            "{$name} ({$sku}) updated",
            null,
            'product-updates.log'
        );
    }
}