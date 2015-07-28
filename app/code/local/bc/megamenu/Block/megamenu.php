<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 2:31 PM
 */
require_once "megamenu/mega.class.php";
class bc_megamenu_Block_megamenu extends Mage_Page_Block_Html_Topmenu
{
    var $children = null;
    var $items = null;
    var $menu = null;
    var $open = null;
    public function __construct($attributes = array()) {

        parent::__construct ();


        $this->addData(array(
            'cache_lifetime' => null,
        ));

        $this->menu = new MenuMega();
        $this->params = new stdclass();

        $this->params->megamenu = 1;
        $this->params->startlevel = 0;
        $this->params->endlevel = 10;
    }
    public function _prepareLayout()
    {

        $headBlock = $this->getLayout()->getBlock('head');
        return parent::_prepareLayout();
    }
    public function _toHtml(){

        if(!Mage::helper('megamenu')->get('show')){
            return parent::_toHtml();
        }
        $this->setTemplate("bluecom/megamenu/output.phtml");
        $storeid = Mage::app()->getStore()->getStoreId();
        $resource = Mage::getSingleton('core/resource');
        $read= $resource->getConnection('core_read');


        $menutable = $resource->getTableName('megamenu_store_menugroup');
        $query = 'SELECT menugroupid '
            . ' FROM '.$menutable
            . ' WHERE store_id = '.(int) $storeid

            . ' ORDER BY id';
        $rows = $read->fetchRow($query);

        if(!$rows["menugroupid"]){
            $rows["menugroupid"] = 0;
        }
        $collections = Mage::getModel('megamenu/megamenu')->getCollection()->setOrder("parent", "ASC")->setOrder("ordering","ASC")->addFilter("status",1,"eq")->addFilter("menugroup",$rows["menugroupid"]);

        $tree = array();
        foreach($collections as $collection){
            $collection->tree = array();
            $parent_tree  = array();
            if(isset($tree[$collection->parent])){
                $parent_tree = $tree[$collection->parent];
            }
            //Create tree
            array_push($parent_tree, $collection->menu_id);
            $tree[$collection->menu_id] = $parent_tree;

            $collection->tree   = $parent_tree;
        }
        $this->menu->getList($collections);
        //$this->menu->genMenu();
        ob_start();
        $this->menu->genMenu();
        $menuoutput = ob_get_contents();
        $this->assign ( 'menuoutput', $menuoutput );
        ob_end_clean();
        return parent::_toHtml ();
    }
}