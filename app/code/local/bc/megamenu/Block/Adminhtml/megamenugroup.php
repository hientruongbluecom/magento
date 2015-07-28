<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 2:33 PM
 */
class bc_megamenu_Block_Adminhtml_megamenugroup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_megamenugroup';
        $this->_blockGroup = 'megamenu';
        $this->_headerText = Mage::helper('megamenu')->__('Group Manager');
        $this->_addButtonLabel = Mage::helper('megamenu')->__('Add Menu Group');

        parent::__construct();

    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/newgroup');
    }
}

?>