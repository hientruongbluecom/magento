<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 2:17 PM
 */
class bc_megamenu_Model_Mysql4_megamenugroup extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('megamenu/megamenu_types', 'id');
    }
    /**
     * Returns pairs block_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('block_id', 'title');
    }

}