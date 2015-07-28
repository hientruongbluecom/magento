<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 1:57 PM
 */
class bc_megamenu_Model_Mysql4_megamenu extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('megamenu/megamenu', 'menu_id');
    }
}
