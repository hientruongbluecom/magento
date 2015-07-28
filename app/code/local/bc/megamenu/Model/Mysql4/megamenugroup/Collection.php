<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 4:07 PM
 */
class bc_megamenu_Model_Mysql4_megamenugroup_Collection  extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {

        $this->_init('megamenu/megamenugroup');
    }
    /**
     * Returns pairs block_id - title
     *
     * @return array
     */

    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'title');
    }

}