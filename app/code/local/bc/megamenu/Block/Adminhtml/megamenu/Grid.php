<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:02 PM
 */
class bc_megamenu_Block_Adminhtml_megamenu_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    var $CurPage = 1;
    var $LastPage = 1;
    var $groupid;
    public function __construct()
    {
        parent::__construct();
        $groupid  = $this->getRequest()->getParam('groupid')?$this->getRequest()->getParam('groupid'):0;
        $this->groupid = $groupid;
        $this->setId('megamenuGrid');
        $this->setDefaultSort('menu_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setTemplate('widget/gridmenu.phtml');
    }
    protected function _preparePage()
    {

        $this->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
        $collections = Mage::getModel('megamenu/megamenu')->getCollection()->addFieldToFilter("menugroup",array('eq'=>$this->groupid))->setOrder("parent", "DESC");
        $this->setLastPage(ceil(count($collections)/$this->getParam($this->getVarNameLimit(), $this->_defaultLimit)));
    }

    protected function setCurPage($page)
    {
        $this->CurPage = $page;
    }

    protected function setLastPage($page)
    {
        $this->LastPage = $page;
    }

    protected function getCurPage($page)
    {
        return $this->CurPage;
    }

    protected function getLastPage($page)
    {
        return $this->LastPage;
    }
    protected function _prepareCollection()
    {

        $collections = Mage::getModel('megamenu/megamenu')->getCollection()->addFieldToFilter("menugroup",array('eq'=>$this->groupid))->setOrder("parent", "DESC")->setOrder("ordering","ASC");

        $this->setCollection($collections);
        parent::_prepareCollection();
        $limitstart = $this->getParam($this->getVarNameLimit(), $this->_defaultLimit)*($this->getParam($this->getVarNamePage(), $this->_defaultPage) - 1);
        $limit = $this->getParam($this->getVarNameLimit(), $this->_defaultLimit);
        if(count($this->getCollection())){
            $collections = $this->getCollection();
            $helper = Mage::helper('megamenu');
            $list = $helper->prepareGridCollection(0,$collections,"title","menu_id","parent",true);
            $list = array_slice($list,$limitstart,$limit);
            $this->setCollectionfake($listfake);
            foreach($collections as $collection){
                $collections->removeItemByKey($collection->menu_id);
            }
            foreach($list as $collection){
                $collections->addItem($collection);
            }

        }
        $this->setCollection($collections);
        return $this;
    }
    protected function setCollectionfake($list){
        $this->collectionfake = $list;
    }
    protected function getCollectionfake(){
        if($this->collectionfake) return $this->collectionfake;
    }
    protected function _prepareColumns()
    {

        $this->addColumn('menu_id', array(
            'header' => Mage::helper('megamenu')->__('ID'),
            'align' =>'right',
            'width' => '50px',
            'index' => 'menu_id',
        ));
        $this->addColumn('title', array(
            'header' => Mage::helper('megamenu')->__('Title'),
            'align' =>'left',
            'index' => 'title',
        ));
        $this->addColumn('ordering', array(
            'header' => Mage::helper('megamenu')->__('Order'),
            'align' =>'left',
            'index' => 'ordering',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('megamenu')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                0 => 'Disabled',
            ),
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
                        'url' => array('base'=> '*/*/edit'),
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
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('menu_id');
        $this->getMassactionBlock()->setFormFieldName('megamenu');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('megamenu')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('megamenu')->__("Are you sure to delete this items and it's childrens?")
        ));
        $this->getMassactionBlock()->addItem('duplicate', array(
            'label'    => Mage::helper('megamenu')->__('Duplicate'),
            'url'      => $this->getUrl('*/*/massDuplicate')
        ));
        $statuses = Mage::getSingleton('megamenu/status')->getOptionArray();
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('megamenu')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('megamenu')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }
}