<?php
class Bc_Megamenu_Block_Toggle extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        if (!Mage::getStoreConfig('megamenu/general/enabled')) return;
        $layout = $this->getLayout();
        $topnav = $layout->getBlock('catalog.topnav');
        $head   = $layout->getBlock('head');
        if (is_object($topnav) && is_object($head)) {
            $topnav->setTemplate('bluecom/megamenu/top.phtml');
            $head->addItem('skin_css', 'bluecom/megamenu/css/megamenu.css');
            // --- Insert menu content ---
            if (!Mage::getStoreConfig('megamenu/general/ajax_load_content')) {
                $menuContent = $layout->getBlock('megamenu-content');
                if (!is_object($menuContent)) {
                    $menuContent = $layout->createBlock('core/template', 'megamenu-content')
                        ->setTemplate('bluecom/megamenu/menucontent.phtml');
                }
                $positionTarget = $layout->getBlock('before_body_end');
                if (is_object($positionTarget)) $positionTarget->append($menuContent);
            }
        }
    }
}