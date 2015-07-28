<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 2:18 PM
 */
class bc_megamenu_Model_megamenustoregroup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenu/megamenustoregroup');
    }
}
