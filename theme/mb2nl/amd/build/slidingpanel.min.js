/**
 *
 * @package   theme_mb2nl
 * @copyright 2017 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license   PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */ define(["jquery","theme_mb2nl/tgsdb"],function(t,n){"use strict";var a=function(){t(".sliding-panel").attr("data-open","false"),t("body").hasClass("tgsdb")&&n.tgsdbTopPos()};return{init:function(){t(document).on("click",".header-tools-jslink",function(){t(this).attr("data-id")===t(".sliding-panel").attr("data-open")?a():(t(".sliding-panel").attr("data-open",t(this).attr("data-id")),t("body").hasClass("tgsdb")&&n.tgsdbTopPos())}),t(document).on("click",".sliding-panel-close",function(){a()})}}});