<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/30/15
 * Time: 2:39 PM
 */
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
        if (Mage::getStoreConfig('megamenu/general/ajax_load_content')) {
            $_moblieMenuAjaxUrl = str_replace('http:', '', Mage::getUrl('megamenu/ajaxmobilemenuitem'));
            $_menuAjaxUrl = str_replace('http:', '', Mage::getUrl('megamenu/ajaxmenuitem'));
        } else {
            $_moblieMenuAjaxUrl = '';
            $_menuAjaxUrl = '';
        }
        $this->_menuData = array(
            '_block'                        => $block,
            '_categories'                   => $categories,
            '_moblieMenuAjaxUrl'            => $_moblieMenuAjaxUrl,
            '_menuAjaxUrl'                  => $_menuAjaxUrl,
            '_showHomeLink'                 => Mage::getStoreConfig('megamenu/general/show_home_link'),
            '_popupWidth'                   => Mage::getStoreConfig('megamenu/popup/width') + 0,
            '_popupTopOffset'               => Mage::getStoreConfig('megamenu/popup/top_offset') + 0,
            '_popupDelayBeforeDisplaying'   => Mage::getStoreConfig('megamenu/popup/delay_displaying') + 0,
            '_popupDelayBeforeHiding'       => Mage::getStoreConfig('megamenu/popup/delay_hiding') + 0,
            '_rtl'                          => Mage::getStoreConfig('megamenu/general/rtl') + 0,
            '_mobileMenuEnabled'            => Mage::getStoreConfig('megamenu/general/mobile_menu') + 0,
            '_mobileMenuWidthInit'          => Mage::getStoreConfig('megamenu/general/mobile_menu_width_init') + 0,
        );
        return $this->_menuData;
    }
    public function getMobileMenuContent()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        if (!$_mobileMenuEnabled) return '';
        // --- Home Link ---
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        if ($_showHomeLink) {
            $homeLink = <<<HTML
<div id="menu-mobile-0" class="menu-mobile level0">
    <div class="parentMenu">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
        }
        // --- Menu Content ---
        $mobileMenuContent = '';
        $mobileMenuContentArray = array();
        foreach ($_categories as $_category) {
            $mobileMenuContentArray[] = $_block->drawMegamenuMobileItem($_category);
        }
        if (count($mobileMenuContentArray)) {
            $mobileMenuContent = implode("\n", $mobileMenuContentArray);
        }
        // --- Result ---
        $menu = <<<HTML
$homeLink
$mobileMenuContent
<div class="clearBoth"></div>
HTML;
        return $menu;
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
<div class="menu">
    <div class="parentMenu menu0">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
        }
        // --- Menu Content ---
        $menuContent = '';
        $menuContentArray = array();
        $c=0;
        foreach ($_categories as $_category) {
            $_block->drawMegamenuItem($_category,$c);
            $c++;
        }
        $topMenuArray = $_block->getTopMenuArray();
        if (count($topMenuArray)) {
            $topMenuContent = implode("\n", $topMenuArray);
        }
        $popupMenuArray = $_block->getPopupMenuArray();
        $popupMenuContent = '';
        if (count($popupMenuArray)) {
            $popupMenuContent = implode("\n", $popupMenuArray);
        }
        // --- Result ---
        $topMenu = <<<HTML
$homeLink
$topMenuContent
<div class="clearBoth"></div>
HTML;
        return array('topMenu' => $topMenu, 'popupMenu' => $popupMenuContent);
    }
}