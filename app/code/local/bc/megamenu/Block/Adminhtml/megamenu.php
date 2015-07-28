<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 2:56 PM
 */
class bc_megamenu_Block_Adminhtml_megamenu extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_megamenu';
        $this->_blockGroup = 'megamenu';
        $groupid = $this->getRequest()->getParam('groupid');
        $group = Mage::getModel('megamenu/megamenugroup')->load($groupid);
        $this->_headerText = Mage::helper('megamenu')->__('Item Manager for '.$group->gettitle());
        $this->_addButtonLabel = Mage::helper('megamenu')->__('Add Menu Item');

        parent::__construct();
    }

    public function getCreateUrl()
    {
        $groupid = $this->getRequest()->getParam('groupid');

        return $this->getUrl('*/*/new/',array("groupid" => $groupid));
    }
}