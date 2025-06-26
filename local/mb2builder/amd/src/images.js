/**
 *
 * @package    local_mb2builder
 * @copyright  2018 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

define(['jquery', 'core/ajax'], function($, Ajax) {

    'use strict';

    const previewImages = (mediatype, itemid, pageid, footerid, partid) => {

        let container =  mediatype == 1 ? $('#mb2-pb-modal-images').find('.mb2-pb-images') :
        $('#mb2-pb-modal-global-images').find('.mb2-pb-images');

        const getImages = (mediatype) => {
            const params = {
                filearea: mediatype == 1 ? container.attr('data-filearea') : 'images',
                itemid: itemid,
                pageid: pageid,
                footerid: footerid,
                partid: partid,
            };
    
            const request = {
                methodname: 'local_mb2builder_get_images_preview',
                args: params
            };

            return Ajax.call([request])[0];
        }

        getImages(mediatype).then(data => {
            container.html(data.images);
            return;
        }).catch(err => {
            require(["core/log"], function(log) {
                log.debug(err);
            });
        });
    };

    return {
        previewImages: previewImages
    }

});
