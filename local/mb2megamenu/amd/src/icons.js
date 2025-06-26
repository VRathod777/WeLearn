/**
 *
 * @package    local_mb2megamenu
 * @copyright  2019 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

define( ['jquery', 'core/ajax'], function($, Ajax) {

    'use strict';

    const themeIcons = () => {

        const getIcons = () => {
            const params = {};
    
            const request = {
                methodname: 'local_mb2megamenu_get_theme_icons',
                args: params
            };

            return Ajax.call([request])[0];
        }

        getIcons().then(data => {
            $('#mb2megamenu-modal-icons .mb2megamenu-modal-body').html(data.icons);
            return;
        }).catch(err => {
            require(["core/log"], function(log) {
                log.debug(err);
            });
        });
    };

    return {
        themeIcons: themeIcons
    }

});
