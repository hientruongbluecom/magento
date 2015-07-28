<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 10:53 AM
 */
class bc_megamenu_Model_Observer
{
    public function prefarestoreForm($block){
        if(Mage::registry('store_type') == 'store'){
            $form = $block["block"]->getForm();
            $storeid = Mage::app()->getRequest()->getParam("store_id");

            $collections = Mage::getModel('megamenu/megamenugroup')->getCollection()->addFieldToFilter("storeid",array("eq" => $storeid))->setOrder("id", "DESC");
            $listgroup = array();

            foreach ($collections as $collection) {
                $listgroup[$collection->id] = $collection->title;
            }

            //add the menugoup field
            if(!empty($listgroup)){

                $resource = Mage::getSingleton('core/resource');
                $read= $resource->getConnection('core_read');
                $menutable = $resource->getTableName('megamenu_store_menugroup');
                $query = 'SELECT menugroupid '
                    . ' FROM '.$menutable
                    . ' WHERE store_id = '.(int) $storeid

                    . ' ORDER BY id';
                $rows = $read->fetchRow($query);
                $fieldset = $form->addFieldset('megamenu_fieldset', array(
                    'legend' => Mage::helper('core')->__('Megamenu Information')
                ));

                //print_r($listgroup);die();
                $fieldset->addField('menugroup', 'select', array(
                    'name'      => 'menugroup',
                    'label'     => Mage::helper('megamenu')->__('Menu Group'),
                    'no_span'   => true,
                    'values'     => $listgroup
                ));
                $menugroup = $form->getElement("menugroup");
                $menugroup->setValue($rows['menugroupid']);
                if($rows['menugroupid']){
                    //die($rows['menugroupid']);
                    $form->setValue("menugroup",$rows['menugroupid']);
                }
                $block["block"]->setForm($form);
                // print_r($block);die();
            }


        }

    }


    public function storeedit($store){

        if(Mage::app()->getRequest()->isPost() && $postData = Mage::app()->getRequest()->getPost() ){
            if($postData['store_type'] == 'store'){

                $storegroupmodel = Mage::getModel('megamenu/megamenustoregroup');
                $storecollection = Mage::getModel('megamenu/megamenustoregroup')->getCollection()->addFieldToFilter("store_id",array("eq" => $postData['store']['store_id']));

                foreach($storecollection as $collection){
                    $id = $collection->id;
                    break;
                }
                if($id){
                    $storegroupmodel->load($id);
                }


                $save['menugroupid'] = $postData['menugroup'];
                $save['store_id'] = $postData['store']['store_id'];

                $storegroupmodel->setData('menugroupid',$postData['menugroup']);
                $storegroupmodel->setData('store_id',$postData['store']['store_id']);
                $storegroupmodel->save();
            }
        }
    }
}