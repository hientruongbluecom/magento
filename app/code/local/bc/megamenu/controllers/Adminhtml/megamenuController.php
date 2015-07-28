<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:52 PM
 */
class bc_megamenu_Adminhtml_megamenuController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {


        $this->loadLayout()->_setActiveMenu('megamenu/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'),
            Mage::helper('adminhtml')->__('Menu Manager'));

        return $this;
    }
    public function indexAction() {

        $this->_initAction();
        $this->renderLayout();
    }
    public function groupAction(){
        $this->_initAction();
        $this->renderLayout();
    }
    public function editAction()
    {
        $Id = $this->getRequest()->getParam('id');

        $menuModel = Mage::getModel('megamenu/megamenu')->load($Id);

        if ($menuModel->getId() || $Id == 0) {

            if($Id == 0){
                $menuModel->setData("menutype",2);
                $menuModel->setData("mega_subcontent",1);
                $menuModel->setData("mega_group",0);
                $menuModel->setData("mega_cols",1);
                $menuModel->setData("showtitle",1);
            }

            Mage::register('megamenu_data', $menuModel);

            $this->loadLayout();
            $this->_setActiveMenu('megamenu/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'), Mage::helper('adminhtml')->__('Menu Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Description'), Mage::helper('adminhtml')->__('Menu Description'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit'))
                ->_addLeft($this->getLayout()->createBlock('megamenu/adminhtml_megamenu_edit_tabs'));

            $this->renderLayout();

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Menu Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editgroupAction()
    {
        $Id = $this->getRequest()->getParam('id');

        $groupModel = Mage::getModel('megamenu/megamenugroup')->load($Id);

        if ($groupModel->getId() || $Id == 0) {

            /*
            if($Id == 0){
               $menuModel->setData("menutype",2);
               $menuModel->setData("mega_subcontent",1);
               $menuModel->setData("mega_group",0);
               $menuModel->setData("mega_cols",1);
               $menuModel->setData("showtitle",1);
            }
            */

            Mage::register('megamenugroup_data', $groupModel);
            $this->loadLayout();
            $this->_setActiveMenu('megamenu/group');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Group Configuration'), Mage::helper('adminhtml')->__('Menu Group Configuration'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Group Configuration'), Mage::helper('adminhtml')->__('Menu Group Configuration'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('megamenu/adminhtml_megamenugroup_edit'))
                ->_addLeft($this->getLayout()->createBlock('megamenu/adminhtml_megamenugroup_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Menu Group does not exist'));
            $this->_redirect('*/*/');
        }
    }


    public function newgroupAction()
    {
        $this->_forward('editgroup');
    }
    protected function getNextorder($where){

        $resource = Mage::getSingleton('core/resource');
        $read= $resource->getConnection('core_read');
        $menutable = $resource->getTableName('megamenu');
        $sqlquery = 'SELECT MAX(ordering) From '.$menutable.' where '.$where;
        $rows = $read->fetchAll($sqlquery);
        if($rows){
            return $rows[0]['MAX(ordering)'] + 1;
        }

    }

    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('image');
                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS ."megamenu".DS;
                    $uploader->save($path, $_FILES['image']['name'] );
                } catch (Exception $e) {
                }
                //this way the name is saved in DB
                $image = "megamenu/".$_FILES['image']['name'];
            }
            try {
                $postData = $this->getRequest()->getPost();
                if(isset($postData['image']['delete']) && $postData['image']['delete']==1) {
                    $path = Mage::getBaseDir('media') .DS. $postData['image']['value'];
                    $path= preg_replace("/\//", "\\", $path);
                    unlink($path);
                    $postData['image']="";
                }else{
                    if (isset($image)) $postData['image']= $image;
                    else unset($postData['image']);
                }
                $resource = Mage::getSingleton('core/resource');
                $read= $resource->getConnection('core_read');
                $menutable = $resource->getTableName('megamenu');
                if($postData["menualias"] && $postData["menualias"] !== ""){
                    $query = 'SELECT tblmega.menu_id'
                        . ' FROM '.$menutable
                        . ' as tblmega WHERE tblmega.menualias = "'.$postData["menualias"]
                        . '" and tblmega.menu_id != "'.$this->getRequest()->getParam('id')
                        .'"  ORDER BY tblmega.menu_id';

                    $rows = $read->fetchAll($query);

                    if(count($rows)){
                        throw new Exception('The alias already used by another menu item.');
                    }
                }

                $postData['link'] = str_replace ( Mage::getBaseUrl() , "" , $postData['link'] );
                $postData['category'] = str_replace ( Mage::getBaseUrl() , "" , $postData['category'] );
                $postData['cms'] = str_replace ( Mage::getBaseUrl() , "" , $postData['cms'] );

                $menuModel = Mage::getModel('megamenu/megamenu');
                $helper = Mage::helper('megamenu');
                if( $this->getRequest()->getParam('id') <= 0 ) {
                    $menuModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
                    $where = " status >= 0 AND parent = ".(int) $postData['parent'];
                    $postData['ordering'] = $this->getNextorder($where);
                }
                $menuModel
                    ->addData($postData)
                    ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                $helper->reorder(' parent= '.(int)$postData['parent']);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setMegamenuData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $menuModel->getId()));
                    return;
                }
                $this->_redirect('*/*/index/groupid/'.$postData['menugroup']);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setMegamenuData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    public function savegroupAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();


                $groupModel = Mage::getModel('megamenu/megamenugroup');
                $helper = Mage::helper('megamenu');
                if( $this->getRequest()->getParam('id') <= 0 ) {
                    $groupModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
                }
                $oldstoreid =  $groupModel->load($this->getRequest()->getParam('id'))->getstoreid();

                $resource = Mage::getSingleton('core/resource');
                $read= $resource->getConnection('core_read');
                $write = $resource->getConnection('core_write');

                //remove assigning this menu group for the old store
                if($oldstoreid !== $postData['storeid']){
                    $storegrouptable = $resource->getTableName('megamenu_store_menugroup');
                    $query = 'Delete '
                        . ' FROM '.$storegrouptable
                        . '  WHERE store_id = "'.$oldstoreid
                        . '" and menugroupid = "'.$this->getRequest()->getParam('id').'"';

                    $write->query($query);
                }
                $query = 'SELECT store_id'
                    . ' FROM '.$resource->getTableName('megamenu_store_menugroup')
                    . '  WHERE store_id = "'.$oldstoreid
                    .'"  ORDER BY store_id';
                $storegroups = $read->fetchRow($query);


                $query = 'SELECT id,storeid'
                    . ' FROM '.$resource->getTableName('megamenu_types')
                    . '  WHERE storeid = "'.$oldstoreid
                    .'"  ORDER BY storeid';
                $groupstores = $read->fetchRow($query);


                if(empty($storegroups) and $groupstores){
                    $adds =  array();
                    $adds["store_id"] = $groupstores["storeid"];
                    $adds["menugroupid"] = $groupstores["id"];
                    $write->insert($resource->getTableName('megamenu_store_menugroup'),$adds);
                    $write->commit();
                }

                $groupModel
                    ->addData($postData)
                    ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                //check to see if there are any menu group associated with this store
                $storegroupModel =  Mage::getModel('megamenu/megamenustoregroup');
                $storecollection = $storegroupModel->getCollection()->addFieldToFilter("store_id",array('eq' => $postData['storeid']));
                if(!count($storecollection)){
                    $storegroupModel->setstore_id($postData['storeid'])
                        ->setmenugroupid($groupModel->getId())
                        ->save();
                }


                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu Group was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setMegamenugroupData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/editgroup', array('id' => $groupModel->getId()));
                    return;
                }
                $this->_redirect('*/*/group');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setMegamenugroupData($this->getRequest()->getPost());
                $this->_redirect('*/*/editgroup', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    public function ajaxAction(){

        $groupid = $this->getRequest()->getParam('menugroup');
        $activecat = $this->getRequest()->getParam('activecat');
        $activecms = $this->getRequest()->getParam('activecms');
        $helper = Mage::helper('megamenu');

        $groupModel = Mage::getModel('megamenu/megamenugroup')->load($groupid);
        $storeid = $groupModel->getData("storeid");
        $store = Mage::app()->getStore($storeid);
        //The root category id of this store
        $parent = $store->getRootCategoryId();
        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        if (!$category->checkId($parent)) {
            return array();
        }

        $recursionLevel  = max(0, $store->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getCategories($parent, $recursionLevel, false, true, true);

        $storeCategories =  $storeCategories->load()->addAttributeToSelect("*");

        //categories list
        $catlist = $helper->getoutputList($parent,$storeCategories,"name","entity_id","parent_id");
        $clist = array();
        foreach($catlist as $id => $cat){
            $category = Mage::getModel('catalog/category')->load($id);
            $url =  str_replace ( Mage::getBaseUrl() , "" , $category->getUrl() );
            $clist[$url] = $cat;
        }
        $response = array();
        $categories = '<select class=" select" name="category" id="category">';

        foreach($clist as $url => $catname){
            if($activecat == $url){
                $categories .= '<option selected value="'.$url.'">'.$catname.'</option>';
            }else{
                $categories .= '<option value="'.$url.'">'.$catname.'</option>';
            }
        }
        $categories .= '</select>';
        $response["category"] = $categories;

        //cmspages list
        $cmspages = array();
        $cmspages = '<select class=" select" name="cms" id="cms">';
        if(Mage::getStoreConfig("web/secure/use_in_adminhtml")){
            $baseurl = Mage::getStoreConfig("web/secure/base_url");
        }else{
            $baseurl = Mage::getBaseUrl();
        }
        if(!strpos($baseurl,"index.php")) $baseurl .= "index.php/";
        foreach($helper->getListcms($storeid) as $page){
            $url =  $baseurl.$page;
            if($page == $activecms){
                $cmspages .= '<option selected value="'.$url.'">'.$page.'</option>';
            }else{
                $cmspages .= '<option value="'.$url.'">'.$page.'</option>';
            }

        }

        $cmspages .= "</select>";
        $response["cmspage"] = $cmspages;

        //get collections of all menu item filter by the requested menu group
        if($this->getRequest()->getParam('menuid')){
            $menumodle = Mage::getModel('megamenu/megamenu')->load($this->getRequest()->getParam('menuid'));
            $parentid = $menumodle->getData("parent");
            $collections = Mage::getModel('megamenu/megamenu')->getCollection()->addFieldToFilter("menu_id",array("neq" => $this->getRequest()->getParam('menuid')))
                ->addFieldToFilter("menugroup",array('eq' => $groupid ));
        } else{
            $collections = Mage::getModel('megamenu/megamenu')->getCollection()->addFieldToFilter("menugroup",array('eq' => $groupid ));
        }

        $parent = '<select class=" select" name="parent" id="parent">';
        //Get the parent list
        $parents = $helper->getoutputList(0,$collections,"title","menu_id","parent",true);
        foreach($parents as $paid => $palabel){
            $parent .= '<option value="'.$paid;
            if($paid == $parentid){
                $parent .= '" selected="selected"';
            }else{
                $parent .= '" ';
            }
            $parent .= ' >'.$palabel.'</option>';
        }
        $parent .= '</select>';
        $response['parent'] = $parent;

        echo json_encode($response);

    }

}
?>