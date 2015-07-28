<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:05 PM
 */
class bc_megamenu_Block_Adminhtml_megamenugroup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/savegroup', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $helper = Mage::helper('megamenu');

        $this->setForm($form);
        $fieldset = $form->addFieldset('megamenugroup_form', array('legend'=>Mage::helper('megamenu')->__('megamenu group information')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('megamenu')->__('Group title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));


        $fieldset->addField('menutype', 'text', array(
            'label' => Mage::helper('megamenu')->__('Group Unique'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'menutype',
        ));
        $stores = Mage::app()->getStores();

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('megamenu')->__('Group Description'),
            'name' => 'description',
        ));

        $fieldset->addField('storeid', 'select', array(
            'name'      => 'storeid',
            'label'     => Mage::helper('cms')->__('Store View'),
            'title'     => Mage::helper('cms')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
            'disabled'  => false
        ));
        if ( Mage::getSingleton('adminhtml/session')->getMegamenugroupData() )
        {

            $form->setValues(Mage::getSingleton('adminhtml/session')->getMegamenugroupData());
            Mage::getSingleton('adminhtml/session')->setMegamenugroupData(null);
        } elseif ( Mage::registry('megamenugroup_data') ) {

            $form->setValues(Mage::registry('megamenugroup_data')->getData());
        }
        return parent::_prepareForm();
    }

}