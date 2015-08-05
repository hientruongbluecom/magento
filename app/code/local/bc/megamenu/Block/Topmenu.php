<?php
/*extention enable show custom menu*/
if (!Mage::getStoreConfig('megamenu/general/enabled')) {
    class Bc_Megamenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu {}
    return;
}
class Bc_Megamenu_Block_Topmenu extends Bc_Megamenu_Block_Navigation {}