/**
 *
 * @module     theme_mb2nl
 * @copyright 2017 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license   PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */ define(["jquery","theme_mb2nl/userpreference"],function(e,i){return{closeNotice:function(){e(".mb2notices-item.canclose").each(function(){var t=e(this),n="mb2notices_item_"+t.data("itemid"),c=t.find(".mb2notices-item-close");"hide"!==i.getCookie(n)&&t.show(),c.click(function(e){e.preventDefault(),t.slideUp(250),i.setCookie(n,"hide")})})}}});