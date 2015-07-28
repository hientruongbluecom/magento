<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/27/15
 * Time: 3:52 PM
 */
class bc_megamenu_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}

?>