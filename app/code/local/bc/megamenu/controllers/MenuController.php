<?php
class Bc_Megamenu_MenuController extends Mage_Core_Controller_Front_Action
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