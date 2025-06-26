/**
 *
 * @package    local_mb2builder
 * @copyright  2018 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

define(['jquery'], function($) {

    'use strict';

    const getPartCode = () => {

        $(document).on('click', '.mb2part-getcode', function(){

            let btn = $(this);
            let code = $('[name="' + btn.attr('data-part') + '"]').val();
            let text1 = btn.find('.text1');
            let text2 = btn.find('.text2');

            if (!btn.hasClass('copied')) {

                navigator.clipboard.writeText(code);
                
                btn.addClass('copied');
                text1.addClass('hidden');
                text2.removeClass('hidden');

                setTimeout(function(){
                    btn.removeClass('copied');
                    text1.removeClass('hidden');
                    text2.addClass('hidden');
                }, 1200);
            }

        });
       
    };

    const settingsTab = () => {

        $(document).on('click', '.mb2-pb-tabs-link', function(){

            let btn = $(this);

            // Deactivate tabs.
            btn.closest('.mb2-pb-tabs').find('.mb2-pb-tabs-link').removeClass('active');
            btn.closest('.mb2-pb-tabs').find('.mb2-pb-tabs-pane').removeClass('active');   

            // Activate tab.
            btn.addClass('active');
            $('#'+btn.attr('aria-controls')).addClass('active');

        });
       
    };

    return {
        getPartCode: getPartCode,
        settingsTab: settingsTab
    }

});
