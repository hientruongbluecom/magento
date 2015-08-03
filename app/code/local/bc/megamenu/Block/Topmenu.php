<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/30/15
 * Time: 2:53 PM
 */
/*extention enable show custom menu*/
if (!Mage::getStoreConfig('megamenu/general/enabled')) {
    class bc_megamenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu {}
    return;
}
class bc_megamenu_Block_Topmenu extends bc_megamenu_Block_Navigation {}