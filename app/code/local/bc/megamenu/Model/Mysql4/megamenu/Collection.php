<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/28/15
 * Time: 11:03 AM
 */
class bc_megamenu_Model_Mysql4_megamenu_Collection  extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {

        $this->_init('megamenu/megamenu');
    }
}
