<?php
class bc_megamenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_menuData = null;
    public function saveCurrentCategoryIdToSession()
    {
        $currentCategory = Mage::registry('current_category');
        $currentCategoryId = 0;
        if (is_object($currentCategory)) {
            $currentCategoryId = $currentCategory->getId();
        }
        Mage::getSingleton('catalog/session')
            ->setCustomMenuCurrentCategoryId($currentCategoryId);
    }
    public function initCurrentCategory()
    {
        $currentCategoryId = Mage::getSingleton('catalog/session')->getCustomMenuCurrentCategoryId();
        $currentCategory = null;
        if ($currentCategoryId) {
            $currentCategory = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($currentCategoryId);
        }
        Mage::unregister('current_category');
        Mage::register('current_category', $currentCategory);
    }
    public function getMenuData()
    {
        if (!is_null($this->_menuData)) return $this->_menuData;
        $blockClassName = Mage::getConfig()->getBlockClassName('megamenu/navigation');
        $block = new $blockClassName();
        $categories = $block->getStoreCategories();
        if (is_object($categories)) $categories = $block->getStoreCategories()->getNodes();

        $this->_menuData = array(
            '_block'                        => $block,
            '_categories'                   => $categories,
            '_showHomeLink'                 => Mage::getStoreConfig('megamenu/general/show_home_link'),
        );
        return $this->_menuData;
    }

    public function getMenuContent()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        // --- Home Link ---
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        if ($_showHomeLink) {
            $homeLink = <<<HTML
<li class="menu">
        <a class="level0" href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
</li>
HTML;
        }
        // --- Menu Content ---
        $menuContent = '';
        $menuContentArray = '';
        foreach ($_categories as $_category) {
            $_block->drawMegamenuItem($_category);
        }
        $topMenuArray = $_block->getTopMenuArray();
        if ($topMenuArray!='') {
            $topMenuContent =  $topMenuArray;
        }
        // --- Result ---
        $topMenu = <<<HTML
$homeLink
$topMenuContent
<div class="clearBoth"></div>
HTML;
        return array('topMenu' => $topMenu);
    }
}