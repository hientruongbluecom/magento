<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 4:03 PM
 */
class bc_megamenu_Model_Listmenugroup extends Varien_Object
{
    static public function getOptionArray()
    {
        $collections = Mage::getModel('megamenu/megamenugroup')->getCollection();
        $ar= array();
        foreach ($collections as $collection){
            $ar[$collection->getId()] = $collection->getTitle();
        }

        return $ar;
    }
}