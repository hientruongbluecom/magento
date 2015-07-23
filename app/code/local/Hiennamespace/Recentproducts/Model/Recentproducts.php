<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/21/15
 * Time: 3:08 PM
 */
class Hiennamespace_Recentproducts_Model_Recentproducts extends Mage_Core_Model_Abstract {
    public function getRecentProducts() {
        $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->setOrder('entity_id', 'DESC')
                ->setPageSize(5);
    return $products;
  }
}