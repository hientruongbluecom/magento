<?php
class Bc_Megamenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    const CUSTOM_BLOCK_TEMPLATE = "mgmenu_%d";
    private $_productsCount     = null;
    private $_topMenu           = '';
    private $_popupMenu         = '';
    public function drawMegamenuMobileItem($category, $level = 0, $last = false)
    {
        if (!$category->getIsActive()) return '';
        $html = '';
        $id = $category->getId();
        // --- Sub Categories ---
        $activeChildren = $this->_getActiveChildren($category, $level);
        // --- class for active category ---
        $active = ''; if ($this->isCategoryActive($category)) $active = ' act';
        $hasSubMenu = count($activeChildren);
        $catUrl = $this->getCategoryUrl($category);
        $html.= '<div id="menu-mobile-' . $id . '" class="menu-mobile level0' . $active . '">';
        $html.= '<div class="parentMenu">';
        // --- Top Menu Item ---
        $html.= '<a class="level' . $level . $active . '" href="' . $catUrl .'">';
        $name = $this->escapeHtml($category->getName());
        if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) {
            $name = str_replace(' ', '&nbsp;', $name);
        }
        $html.= '<span>' . $name . '</span>';
        $html.= '</a>';
        if ($hasSubMenu) {
            $html.= '<span class="button" rel="submenu-mobile-' . $id . '" onclick="mgSubMenuToggle(this, \'menu-mobile-' . $id . '\', \'submenu-mobile-' . $id . '\');">&nbsp;</span>';
        }
        $html.= '</div>';
        // --- Add Popup block (hidden) ---
        if ($hasSubMenu) {
            // --- draw Sub Categories ---
            $html.= '<div id="submenu-mobile-' . $id . '" rel="level' . $level . '" class="mg-mega-menu-submenu" style="display: none;">';
            $html.= $this->drawMobileMenuItem($activeChildren);
            $html.= '<div class="clearBoth"></div>';
            $html.= '</div>';
        }
        $html.= '</div>';
        $html =  $html;
        return $html;
    }
    public function getTopMenuArray()
    {
        return $this->_topMenu;
    }
    public function getPopupMenuArray()
    {
        return $this->_popupMenu;
    }
    public function drawMegamenuItem($category,$first=-1, $level = 0, $last = false)
    {
        if (!$category->getIsActive()) return;
        $htmlTop ='';
        $id = $category->getId();
        // --- Static Block ---
        $blockId = sprintf(self::CUSTOM_BLOCK_TEMPLATE, $id); // --- static block key
        #Mage::log($blockId);
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addFieldToFilter('identifier', array(array('like' => $blockId . '_w%'), array('eq' => $blockId)))
            ->addFieldToFilter('is_active', 1);
        $blockId = $collection->getFirstItem()->getIdentifier();
        #Mage::log($blockId);
        $blockHtml = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($blockId)->toHtml();
        // --- Sub Categories level 0 ---
        $activeChildren = $this->_getActiveChildren($category, $level);
        // --- class for active category ---
        $active = ''; if ($this->isCategoryActive($category)) $active = ' act';
        // --- Popup functions for show ---
        $drawPopup = ($blockHtml || count($activeChildren));
        if ($drawPopup) {
            if($first==0){
                $htmlTop.= '<div id="menu' . $id . '" class="first menu' . $active . '" onmouseover="mgShowMenuPopup(this, event, \'popup' . $id . '\');" onmouseout="mgHideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';

            }else{
                $htmlTop.= '<div id="menu' . $id . '" class="menu' . $active . '" onmouseover="mgShowMenuPopup(this, event, \'popup' . $id . '\');" onmouseout="mgHideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';
            }
            } else {
            if($first==0){
                $htmlTop.= '<div id="menu' . $id . '" class="first menu' . $active . '">';
            }else{
                $htmlTop.= '<div id="menu' . $id . '" class="menu' . $active . '">';
            }
        }
        // --- Top Menu Item ---
        $htmlTop.= '<div class="parentMenu">';
        if ($level == 0 && $drawPopup) {
            $htmlTop.= '<a  class="level' . $level . $active . '" href="javascript:void(0);" rel="'.$this->getCategoryUrl($category).'">';
        } else {
            $htmlTop.= '<a  class="level' . $level . $active . '" href="'.$this->getCategoryUrl($category).'">';
        }
        $name = $this->escapeHtml($category->getName());
        if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) {
            $name = str_replace(' ', '&nbsp;', $name);
        }
        $htmlTop.= '<span>' . $name . '</span>';
        $htmlTop.= '</a>';
        $htmlTop.= '</div>';
        $htmlTop.= '</div>';
        $this->_topMenu.=  $htmlTop;
        /*get img category parent*/
        $helper    = $this->helper('catalog/output');
        $categoryImage  = Mage::getModel('catalog/category')->load($category->getId());
        $imgHtml   = '';
        if ($imgUrl = $categoryImage->getImageUrl()) {
            $imgHtml = '<div class="thumb-image-menu"> <a href="'.$this->getCategoryUrl($category).'"><img class="mega-nav-item-image" src="'.$imgUrl.'" alt="'.$this->htmlEscape($categoryImage->getName()).'" title="'.$this->htmlEscape($categoryImage->getName()).'" /></a></div>';
            $imgHtml = $helper->categoryAttribute($categoryImage, $imgHtml, 'image');
        }
        /*end get img category parent*/
        // --- Add Popup block (hidden) ---
        if ($drawPopup) {
            $htmlPopup = '';
            // --- Popup function for hide ---
            $htmlPopup.= '<div id="popup' . $id . '" class="mg-mega-menu-popup" onmouseout="mgHideMenuPopup(this, event, \'popup' . $id . '\', \'menu' . $id . '\')" onmouseover="mgPopupOver(this, event, \'popup' . $id . '\', \'menu' . $id . '\')">';
            // --- draw Sub Categories ---

            if (count($activeChildren)) {
                $columns = (int)Mage::getStoreConfig('megamenu/columns/count');
                $htmlPopup.= '<div class="column"';
                $htmlPopup.= $this->drawColumns($activeChildren, $columns);
                $htmlPopup.= $imgHtml;
                $htmlPopup.= '<div class="view_all_mn"><a  class="" href="'.$this->getCategoryUrl($category).'">';
                if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) {
                    $name = str_replace(' ', '&nbsp;', $name);
                }
                $htmlPopup.= '<span class="go-all"> View All ' . $name . '</span>';
                $htmlPopup.= '<span class="go-all-icon"> >></span>';
                $htmlPopup.= '</a>';
                $htmlPopup.= '</div>';
                $htmlPopup.= '<div class="clearBoth"></div>';
                $htmlPopup.= '</div>';
            }
            // --- draw Custom User Block ---
            if ($blockHtml) {
                $htmlPopup.= '<div id="' . $blockId . '" class="block2"';
                $htmlPopup.= $blockHtml;
                $htmlPopup.= '</div>';
            }
            $htmlPopup.= '</div>';
            $this->_popupMenu.= $htmlPopup;
        }
    }
    public function drawMobileMenuItem($children, $level = 1)
    {
        $keyCurrent = $this->getCurrentCategory()->getId();
        $html = '';
        foreach ($children as $child) {
            if (is_object($child) && $child->getIsActive()) {
                // --- class for active category ---
                $active = '';
                $id = $child->getId();
                $activeChildren = $this->_getActiveChildren($child, $level);
                if ($this->isCategoryActive($child)) {
                    $active = ' actParent';
                    if ($id == $keyCurrent) $active = ' act';
                }
                $html.= '<div id="menu-mobile-' . $id . '" class="itemMenu level' . $level . $active . '">';
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) $name = str_replace(' ', '&nbsp;', $name);
                $html.= '<div class="parentMenu">';
                $html.= '<a class="itemMenuName level' . $level . $active . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name . '</span></a>';
                if (count($activeChildren) > 0) {
                    $html.= '<span class="button" rel="submenu-mobile-' . $id . '" onclick="mgSubMenuToggle(this, \'menu-mobile-' . $id . '\', \'submenu-mobile-' . $id . '\');">&nbsp;</span>';
                }
                $html.= '</div>';
                if (count($activeChildren) > 0) {
                    $html.= '<div id="submenu-mobile-' . $id . '" rel="level' . $level . '" class="mg-mega-menu-submenu level' . $level . '" style="display: none;">';
                    $html.= $this->drawMobileMenuItem($activeChildren, $level + 1);
                    $html.= '<div class="clearBoth"></div>';
                    $html.= '</div>';
                }
                $html.= '</div>';
            }
        }
        return $html;
    }

    public function drawMenuItem($children, $level = 1)
    {
        $html = '<div class="itemMenu level' . $level . '">';
        $keyCurrent = $this->getCurrentCategory()->getId();
        $c = 1;
        foreach ($children as $child) {
            if (is_object($child) && $child->getIsActive()) {
                // --- class for active category ---
                $active = '';
                if ($this->isCategoryActive($child)) {
                    $active = ' actParent';
                    if ($child->getId() == $keyCurrent) $active = ' act';
                }
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) $name = str_replace(' ', '&nbsp;', $name);
                if($level >1){
                    $html.= '<a class="itemSubMenuName level' . $level . $active . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name . '</span></a>';
                    if($c==(int)Mage::getStoreConfig('megamenu/columns/litmit_row')){
                        $html.="</div>";
                        $html.= '<div class="itemMenu not-first level' . $level . '">';
                    }
                }else{
                    $html.= '<span class="sub-cats-mn">' . $name . '</span>';
                }
                $activeChildren = $this->_getActiveChildren($child, $level);
                if (count($activeChildren) > 0) {
                    $html.= '<div class="sub-cats-container">';
                    $html.= $this->drawMenuItem($activeChildren, $level + 1);
                    $html.= '</div>';
                }
            }
            $c++;
        }
        $html.= '</div>';
        $html.= '<div class="clearBoth"></div>';
        return $html;
    }
    public function drawColumns($children, $columns = 1)
    {
        $Popwidth = Mage::getStoreConfig('megamenu/popup/width');
        $colwidth = ($Popwidth/$columns)-2;
        $html = '';
        // --- explode by columns ---
        if ($columns < 1) $columns = 1;
        $chunks = $this->_explodeByColumns($children, $columns);
        // --- draw columns ---
        $lastColumnNumber = count($chunks);
        $i = 1;
        foreach ($chunks as $key => $value) {
            if (!count($value)) continue;
            $class = '';
            if ($i == 1) $class.= ' first';
            if ($i == $lastColumnNumber) $class.= ' last';
            if ($i % 2) $class.= ' odd'; else $class.= ' even';
            $html.= '<div class="column' . $class . '" style="width:' . $colwidth . 'px;">';
            $html.= $this->drawMenuItem($value, 1);
            $html.= '</div>';
            $i++;
        }
        return $html;
    }
    protected function _getActiveChildren($parent, $level)
    {
        $activeChildren = array();
        // --- check level ---
        $maxLevel = (int)Mage::getStoreConfig('megamenu/general/max_level');
        if ($maxLevel > 0) {
            if ($level >= ($maxLevel - 1)) return $activeChildren;
        }
        // --- / check level ---
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = $parent->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $parent->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        if ($hasChildren) {
            foreach ($children as $child) {
                if ($this->_isCategoryDisplayed($child)) {
                    array_push($activeChildren, $child);
                }
            }
        }
        return $activeChildren;
    }
    private function _isCategoryDisplayed(&$child)
    {
        if (!$child->getIsActive()) return false;
        // === check products count ===
        // --- get collection info ---
        if (!Mage::getStoreConfig('megamenu/general/display_empty_categories')) {
            $data = $this->_getProductsCountData();
            // --- check by id ---
            $id = $child->getId();
            #Mage::log($id); Mage::log($data);
            if (!isset($data[$id]) || !$data[$id]['product_count']) return false;
        }
        // === / check products count ===
        return true;
    }
    private function _getProductsCountData()
    {
        if (is_null($this->_productsCount)) {
            $collection = Mage::getModel('catalog/category')->getCollection();
            $storeId = Mage::app()->getStore()->getId();
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount(true)
                ->setStoreId($storeId);
            $productsCount = array();
            foreach($collection as $cat) {
                $productsCount[$cat->getId()] = array(
                    'name' => $cat->getName(),
                    'product_count' => $cat->getProductCount(),
                );
            }
            #Mage::log($productsCount);
            $this->_productsCount = $productsCount;
        }
        return $this->_productsCount;
    }
    private function _explodeByColumns($target, $num)
    {
        if ((int)Mage::getStoreConfig('megamenu/columns/divided_horizontally')) {
            $target = self::_explodeArrayByColumnsHorisontal($target, $num);
        } else {
            $target = self::_explodeArrayByColumnsVertical($target, $num);
        }
        #return $target;
        if ((int)Mage::getStoreConfig('megamenu/columns/integrate') && count($target)) {
            // --- combine consistently numerically small column ---
            // --- 1. calc length of each column ---
            $max = 0; $columnsLength = array();
            foreach ($target as $key => $child) {
                $count = 0;
                $this->_countChild($child, 1, $count);
                if ($max < $count) $max = $count;
                $columnsLength[$key] = $count;
            }
            // --- 2. merge small columns with next ---
            $xColumns = array(); $column = array(); $cnt = 0;
            $xColumnsLength = array(); $k = 0;
            foreach ($columnsLength as $key => $count) {
                $cnt+= $count;
                if ($cnt > $max && count($column)) {
                    $xColumns[$k] = $column;
                    $xColumnsLength[$k] = $cnt - $count;
                    $k++; $column = array(); $cnt = $count;
                }
                $column = array_merge($column, $target[$key]);
            }
            $xColumns[$k] = $column;
            $xColumnsLength[$k] = $cnt - $count;
            // --- 3. integrate columns of one element ---
            $target = $xColumns; $xColumns = array(); $nextKey = -1;
            if ($max > 1 && count($target) > 1) {
                foreach($target as $key => $column) {
                    if ($key == $nextKey) continue;
                    if ($xColumnsLength[$key] == 1) {
                        // --- merge with next column ---
                        $nextKey = $key + 1;
                        if (isset($target[$nextKey]) && count($target[$nextKey])) {
                            $xColumns[] = array_merge($column, $target[$nextKey]);
                            continue;
                        }
                    }
                    $xColumns[] = $column;
                }
                $target = $xColumns;
            }
        }
        $_rtl = Mage::getStoreConfigFlag('megamenu/general/rtl');
        if ($_rtl) {
            $target = array_reverse($target);
        }
        return $target;
    }
    private function _countChild($children, $level, &$count)
    {
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $count++; $activeChildren = $this->_getActiveChildren($child, $level);
                if (count($activeChildren) > 0) $this->_countChild($activeChildren, $level + 1, $count);
            }
        }
    }
    private static function _explodeArrayByColumnsHorisontal($list, $num)
    {
        if ($num <= 0) return array($list);
        $partition = array();
        $partition = array_pad($partition, $num, array());
        $i = 0;
        foreach ($list as $key => $value) {
            $partition[$i][$key] = $value;
            if (++$i == $num) $i = 0;
        }
        return $partition;
    }

    private static function _explodeArrayByColumnsVertical($list, $num)
    {
        if ($num <= 0) return array($list);
        $listlen = count($list);
        $partlen = floor($listlen / $num);
        $partrem = $listlen % $num;
        $partition = array();
        $mark = 0;
        for ($column = 0; $column < $num; $column++) {
            $incr = ($column < $partrem) ? $partlen + 1 : $partlen;
            $partition[$column] = array_slice($list, $mark, $incr);
            $mark += $incr;
        }
        return $partition;
    }
}