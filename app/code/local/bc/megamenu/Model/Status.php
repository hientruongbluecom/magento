<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 1:48 PM
 */
class bc_megamenu_Model_Status extends Varien_Object
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED => Mage::helper('megamenu')->__
                    ('Enabled'),
            self::STATUS_DISABLED => Mage::helper('megamenu')->__
                    ('Disabled')
        );
    }
}