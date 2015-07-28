<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:02 PM
 */
class bc_megamenu_Block_Adminhtml_megamenugroup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    var $CurPage = 1;
    var $LastPage = 1;

    public function __construct()
    {
        parent::__construct();
        $this->setId('megamenugroupGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

    }
    /*
     * get collecton can show grid
     */
    protected function _prepareCollection()
    {

        $collections = Mage::getModel('megamenu/megamenugroup')->getCollection()->setOrder("id", "DESC");
        $this->setCollection($collections);
        parent::_prepareCollection();
        return $this;
    }
    /*
     * prepare before print grid
     * */
    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => Mage::helper('megamenu')->__('ID'),
            'align' =>'right',
            'width' => '50px',
            'index' => 'id',
        ));
        $this->addColumn('title', array(
            'header' => Mage::helper('megamenu')->__('Title'),
            'align' =>'left',
            'index' => 'title',
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('megamenu')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('megamenu')->__('Edit'),
                        'url' => array('base'=> '*/*/editgroup'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        return parent::_prepareColumns();
    }
    /*
     * return url a row
     * */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/index', array('groupid' => $row->getId()));
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('menu_id');
        $this->getMassactionBlock()->setFormFieldName('megamenu');
        $groupList = Mage::getSingleton('megamenu/listmenugroup')->getOptionArray();
        $this->getMassactionBlock()->addItem('duplicate', array(
            'label'    => Mage::helper('megamenu')->__('Duplicate'),
            'url'  => $this->getUrl('*/*/massDuplicateGroup', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'duplicate_to',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('megamenu')->__('Duplicate To'),
                    'values' => $groupList
                )
            )
        ));
        return $this;
    }
}