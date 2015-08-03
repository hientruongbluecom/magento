<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/30/15
 * Time: 2:41 PM
 */
class bc_megamenu_AjaxMenuItemController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            Mage::helper('megamenu')->initCurrentCategory();
            $menu = Mage::helper('megamenu')->getMenuContent();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($menu));
        }
    }
}