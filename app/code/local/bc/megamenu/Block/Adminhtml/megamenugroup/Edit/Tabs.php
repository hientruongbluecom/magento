<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:04 PM
 */
class bc_megamenu_Block_Adminhtml_megamenugroup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('megamenugroup_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('megamenu')->__('Menu Group Configuration'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('megamenu')->__('Menu Group Configuration'),
            'title' => Mage::helper('megamenu')->__('Menu Group Configuration'),
            'content' => $this->getLayout()->createBlock('megamenu/adminhtml_megamenugroup_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}