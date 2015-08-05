var mgMenuLoaded = false;
var mgMobileMenuLoaded = false;

function mgInitPopupContent()
{
    if (mgMenuLoaded) return;
    var xMenu = $('megamenu');
    if (typeof mgPopupMenuContent != 'undefined') xMenu.innerHTML = mgPopupMenuContent + xMenu.innerHTML;
    mgMenuLoaded = true;
}

function mgInitMobileMenuContent()
{
    if (mgMobileMenuLoaded) return;
    var xMenu = $('menu-content');
    if (typeof mgMobileMenuContent != 'undefined') xMenu.innerHTML = mgMobileMenuContent;
    mgMobileMenuLoaded = true;
}
/**
 * Show popup sub-menu
 * @param objMenu
 * @param event
 * @param popupId
 */
function mgShowMenuPopup(objMenu, event, popupId)
{
    mgInitPopupContent();
    if (typeof mgCustommenuTimerHide[popupId] != 'undefined') clearTimeout(mgCustommenuTimerHide[popupId]);
    objMenu = $(objMenu.id); var popup = $(popupId); if (!popup) return;
    if (!!mgActiveMenu) {
        mgHideMenuPopup(objMenu, event, mgActiveMenu.popupId, mgActiveMenu.menuId);
    }
    mgActiveMenu = {menuId: objMenu.id, popupId: popupId};
    if (!objMenu.hasClassName('active')) {
        mgCustommenuTimerShow[popupId] = setTimeout(function() {
            objMenu.addClassName('active');
            var popupWidth = CUSTOMMENU_POPUP_WIDTH;
            if (!popupWidth) popupWidth = popup.getWidth();
            var pos = mgPopupPos(objMenu, popupWidth);
            popup.style.top = pos.top + 'px';
            popup.style.left = pos.left + 'px';
            mgSetPopupZIndex(popup);
            if (CUSTOMMENU_POPUP_WIDTH)
                popup.style.width = CUSTOMMENU_POPUP_WIDTH + 'px';
            // --- Static Block width ---
            var block2 = $(popupId).select('div.block2');
            if (typeof block2[0] != 'undefined') {
                var wStart = block2[0].id.indexOf('_w');
                if (wStart > -1) {
                    var w = block2[0].id.substr(wStart+2);
                } else {
                    var w = 0;
                    $(popupId).select('div.block1 div.column').each(function(item) {
                        w += $(item).getWidth();
                    });
                }
                if (w) block2[0].style.width = w + 'px';
            }
            // --- change href ---
            var mgMenuAnchor = $(objMenu.select('a')[0]);
            mgChangeTopMenuHref(mgMenuAnchor, true);
            // --- show popup ---
            if (typeof jQuery == 'undefined') {
                popup.style.display = 'block';
            } else {
                jQuery('#' + popupId).stop(true, true).show();
            }
        }, CUSTOMMENU_POPUP_DELAY_BEFORE_DISPLAYING);
    }
}
/**
 * hide popup sub-menu
 * @param element
 * @param event
 * @param popupId
 * @param menuId
 */
function mgHideMenuPopup(element, event, popupId, menuId)
{
    if (typeof mgCustommenuTimerShow[popupId] != 'undefined') clearTimeout(mgCustommenuTimerShow[popupId]);
    var element = $(element); var objMenu = $(menuId) ;var popup = $(popupId); if (!popup) return;
    var mgCurrentMouseTarget = getCurrentMouseTarget(event);
    if (!!mgCurrentMouseTarget) {
        if (!mgIsChildOf(element, mgCurrentMouseTarget) && element != mgCurrentMouseTarget) {
            if (!mgIsChildOf(popup, mgCurrentMouseTarget) && popup != mgCurrentMouseTarget) {
                if (objMenu.hasClassName('active')) {
                    mgCustommenuTimerHide[popupId] = setTimeout(function() {
                        objMenu.removeClassName('active');
                        // --- change href ---
                        var mgMenuAnchor = $(objMenu.select('a')[0]);
                        mgChangeTopMenuHref(mgMenuAnchor, false);
                        // --- hide popup ---
                        if (typeof jQuery == 'undefined') {
                            popup.style.display = 'none';
                        } else {
                            jQuery('#' + popupId).stop(true, true).hide();
                        }
                    }, CUSTOMMENU_POPUP_DELAY_BEFORE_HIDING);
                }
            }
        }
    }
}

function mgPopupOver(element, event, popupId, menuId)
{
    if (typeof mgCustommenuTimerHide[popupId] != 'undefined') clearTimeout(mgCustommenuTimerHide[popupId]);
}

function mgPopupPos(objMenu, w)
{
    var pos = objMenu.cumulativeOffset();
    var wraper = $('megamenu');
    var posWraper = wraper.cumulativeOffset();
    var xTop = pos.top - posWraper.top
    if (CUSTOMMENU_POPUP_TOP_OFFSET) {
        xTop += CUSTOMMENU_POPUP_TOP_OFFSET;
    } else {
        xTop += objMenu.getHeight();
    }
    var res = {'top': xTop};
    if (CUSTOMMENU_RTL_MODE) {
        var xLeft = pos.left - posWraper.left - w + objMenu.getWidth();
        if (xLeft < 0) xLeft = 0;
        res.left = xLeft;
    } else {
        var wWraper = wraper.getWidth();
        var xLeft = pos.left - posWraper.left;
        if ((xLeft + w) > wWraper) xLeft = wWraper - w;
        if (xLeft < 0) xLeft = 0;
        res.left = xLeft;
    }
    return res;
}

function mgChangeTopMenuHref(mgMenuAnchor, state)
{
    if (state) {
        mgMenuAnchor.href = mgMenuAnchor.rel;
    } else {
        mgMenuAnchor.href = 'javascript:void(0);';
    }
}

function mgIsChildOf(parent, child)
{
    if (child != null) {
        while (child.parentNode) {
            if ((child = child.parentNode) == parent) {
                return true;
            }
        }
    }
    return false;
}

function mgSetPopupZIndex(popup)
{
    $$('.mg-mega-menu-popup').each(function(item){
        item.style.zIndex = '9999';
    });
    popup.style.zIndex = '10000';
}

function getCurrentMouseTarget(xEvent)
{
    var mgCurrentMouseTarget = null;
    if (xEvent.toElement) {
        mgCurrentMouseTarget = xEvent.toElement;
    } else if (xEvent.relatedTarget) {
        mgCurrentMouseTarget = xEvent.relatedTarget;
    }
    return mgCurrentMouseTarget;
}

function getCurrentMouseTargetMobile(xEvent)
{
    if (!xEvent) var xEvent = window.event;
    var mgCurrentMouseTarget = null;
    if (xEvent.target) mgCurrentMouseTarget = xEvent.target;
    else if (xEvent.srcElement) mgCurrentMouseTarget = xEvent.srcElement;
    if (mgCurrentMouseTarget.nodeType == 3) // defeat Safari bug
        mgCurrentMouseTarget = mgCurrentMouseTarget.parentNode;
    return mgCurrentMouseTarget;
}

/* Mobile */
function mgMenuButtonToggle()
{
    $('menu-content').toggle();
}

function mgGetMobileSubMenuLevel(id)
{
    var rel = $(id).readAttribute('rel');
    return parseInt(rel.replace('level', ''));
}

function mgSubMenuToggle(obj, activeMenuId, activeSubMenuId)
{
    var currLevel = mgGetMobileSubMenuLevel(activeSubMenuId);
    // --- hide submenus ---
    $$('.mg-mega-menu-submenu').each(function(item) {
        if (item.id == activeSubMenuId) return;
        var xLevel = mgGetMobileSubMenuLevel(item.id);
        if (xLevel >= currLevel) {
            $(item).hide();
        }
    });
    // --- reset button state ---
    $('megamenu-mobile').select('span.button').each(function(xItem) {
        var subMenuId = $(xItem).readAttribute('rel');
        if (!$(subMenuId).visible()) {
            $(xItem).removeClassName('open');
        }
    });
    // ---
    if ($(activeSubMenuId).getStyle('display') == 'none') {
        $(activeSubMenuId).show();
        $(obj).addClassName('open');
    } else {
        $(activeSubMenuId).hide();
        $(obj).removeClassName('open');
    }
}

function mgResetMobileMenuState()
{
    if ($('menu-content') != undefined) $('menu-content').hide();
    $$('.mg-mega-menu-submenu').each(function(item) {
        $(item).hide();
    });
    if ($('megamenu-mobile') != undefined) {
        $('megamenu-mobile').select('span.button').each(function(item) {
            $(item).removeClassName('open');
        });
    }
}

function mgMegamenuMobileToggle()
{
    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth,
        y = w.innerHeight|| e.clientHeight|| g.clientHeight;

    if (mgMobileMenuEnabled && CUSTOMMENU_MOBILE_MENU_WIDTH_INIT > x) {

        mgInitMobileMenuContent();
        if ($('megamenu') != undefined) $('megamenu').hide();
        if ($('megamenu-mobile') != undefined) $('megamenu-mobile').show();
        // --- ajax load ---
        if (mgMoblieMenuAjaxUrl) {
            new Ajax.Request(
                mgMoblieMenuAjaxUrl, {
                    asynchronous: true,
                    method: 'post',
                    onSuccess: function(transport) {
                        if (transport && transport.responseText) {
                            try {
                                response = eval('(' + transport.responseText + ')');
                            } catch (e) {
                                response = {};
                            }
                        }
                        mgMobileMenuContent = response;
                        mgMobileMenuLoaded = false;
                        mgInitMobileMenuContent();
                    }
                }
            );
            mgMoblieMenuAjaxUrl = null;
        }

    } else {

        if ($('megamenu-mobile') != undefined) $('megamenu-mobile').hide();
        mgResetMobileMenuState();
        if ($('megamenu') != undefined) $('megamenu').show();
        // --- ajax load ---
        if (mgMenuAjaxUrl) {
            new Ajax.Request(
                mgMenuAjaxUrl, {
                    asynchronous: true,
                    method: 'post',
                    onSuccess: function(transport) {
                        if (transport && transport.responseText) {
                            try {
                                response = eval('(' + transport.responseText + ')');
                            } catch (e) {
                                response = {};
                            }
                        }
                        if ($('megamenu') != undefined) $('megamenu').update(response.topMenu);
                        mgPopupMenuContent = response.popupMenu;
                    }
                }
            );
            mgMenuAjaxUrl = null;
        }

    }

    if ($('megamenu-loading') != undefined) $('megamenu-loading').remove();
}
