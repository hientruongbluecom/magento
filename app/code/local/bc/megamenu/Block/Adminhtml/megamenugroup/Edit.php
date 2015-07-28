<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:02 PM
 */
class bc_megamenu_Block_Adminhtml_megamenugroup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'megamenu';
        $this->_controller = 'adminhtml_megamenugroup';
        $this->_updateButton('save', 'label', Mage::helper('megamenu')->__('Save Menu Group'));
        $this->_updateButton('delete', 'label', Mage::helper('megamenu')->__('Delete Menu Group'));
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        $objId = $this->getRequest()->getParam($this->_objectId);

        if (! empty($objId)) {
            $this->_addButton('delete', array(
                'label'     => Mage::helper('adminhtml')->__('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to remove this menu group and menu items belong to it?')
                    .'\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }


        $this->_formScripts[] = "

				      function saveAndContinueEdit(){
                         editForm.submit($('edit_form').action+'back/editgroup/');
                      };

				    ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('megamenugroup_data') && Mage::registry('megamenugroup_data')->getId() ) {
            return Mage::helper('megamenu')->__("Edit Group '%s'", $this->htmlEscape(Mage::registry('megamenugroup_data')->getTitle()));
        } else {
            return Mage::helper('megamenu')->__('Add Menu Group');
        }


    }
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/deletegroup', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}