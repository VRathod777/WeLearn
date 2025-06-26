/**
 *
 * @package    local_mb2builder
 * @copyright  2018 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

define(['jquery', 'core/ajax'], function($, Ajax) {

    'use strict';

    let lang = $('#mb2-pb-lang');

    const backupList = () => {

        const getBackups = (type, itemid) => {
            const params = {
                type: type,
                itemid: itemid
            };
    
            const request = {
                methodname: 'local_mb2builder_get_backup_list',
                args: params
            };

            return Ajax.call([request])[0];
        }

        let container = $('.mb2-pb-backup-list');
        let type = container.attr('data-type');
        let itemid = container.attr('data-itemid');

        getBackups(type, itemid).then(data => {
            if (data.backupitems) {
                container.closest('.backup-table').removeClass('hidden');
            } else {
                container.closest('.backup-table').addClass('hidden');
            }

            // Always load backup items even if the table is empty.
            container.html(data.backupitems);
            return;
        }).catch(err => {
            require(["core/log"], function(log) {
                log.debug(err);
            });
        });

    };

    const deleteBackup = () => {

        const deleteBackupRequest = (id) => {
            const params = {
                id: id
            };
    
            const request = {
                methodname: 'local_mb2builder_delete_backup',
                args: params
            };

            return Ajax.call([request])[0];
        }

        $(document).on('click', '.mb2-pb-delete-backup', function(){

            let loadingel = $(this).closest('.backup-table').find('.mb2-pb-actions-loading');

            if (confirm(lang.attr('data-confirmdeletebackup')) ) {
                deleteBackupRequest($(this).attr('data-id')).then(data => {
                    if (data.deleted) {
                        loadingel.fadeIn();
    
                        // Load backup lists.
                        backupList();
    
                        // Hide loading element.
                        setTimeout(function(){
                            loadingel.fadeOut();
                        }, 700);
                    }
    
                    return;
                }).catch(err => {
                    require(["core/log"], function(log) {
                        log.debug(err);
                    });
                });
            }
        });

    };

    const createBackup = () => {

        let formId = $('.new-backup-form').find('form').attr('id');
        let loadingel = $('.backup-table .mb2-pb-actions-loading');

        const createBackupRequest = (formdata) => {
            const params = {
                formdata: formdata
            };
    
            const request = {
                methodname: 'local_mb2builder_create_backup',
                args: params
            };

            return Ajax.call([request])[0];
        }

        $(document).on('click', 'button.new-backup', function(){
            // Set demo content.
            $('textarea[name="backup_content"]').val($('#democontent').val());

            $(this).prop('disabled', true);
            $(this).text(lang.attr('data-processing'));

            if(!$(this).hasClass('processing')) {
                // Submit the form.
                $('#' + formId).submit();
            }

            $(this).addClass('processing');

        });

        $('#' + formId).on('submit', function(e){
            e.preventDefault(); // Preven to reload page.
            let form = $(this);
            createBackupRequest($(this).serialize()).then(data => {

                if (data.backup) {

                    // Append message.
                    form.closest('.new-backup-form').find('.form-message').html(data.backup);

                    loadingel.fadeIn();

                    // Load manual backup list.
                    backupList(1);
                    
                    // Clear form.
                    form.find('input[type="text"]').val('');
                    form.find('textarea').val('');

                    let btn =$('button.new-backup');

                    // Hide loading element.
                    setTimeout(function(){
                        loadingel.fadeOut();

                        // Enable button.
                        btn.prop('disabled', false);
                        btn.text(lang.attr('data-create'));
                        btn.removeClass('processing');
                    }, 700);

                    // Hide message.
                    setTimeout(function(){
                        form.closest('.new-backup-form').find('.form-message').html('');
                    }, 20000);
                    
                }

                return;
            }).catch(err => {
                require(['core/log'], function(log) {
                    log.debug(err);
                });
            });
        });

    };


    const uploadBackup = () => {

        let formId = $('.mb2-pb-uploadbackup-wrap').find('form').attr('id');

        const uploadBackupRequest = (formdata) => {
            const params = {
                formdata: formdata
            };
    
            const request = {
                methodname: 'local_mb2builder_upload_backup',
                args: params
            };

            return Ajax.call([request])[0];
        }

        $(document).on('click', 'button.mb2-pb-upload-backup', function(){
            // Check if file is not empty.
            let btn = $(this);
            btn.prop('disabled', true);
            btn.text(lang.attr('data-processing'));

            if(!btn.hasClass('processing')) {
                // Submit the form.
                $('#' + formId).submit();
            }

            btn.addClass('processing');

        });

        $('#' + formId).on('submit', function(e){
            e.preventDefault(); // Preven to reload page.
            let form = $(this);
            let btn = form.closest('.restore-backup').find('button.mb2-pb-upload-backup');

            uploadBackupRequest(form.serialize()).then(data => {

                let restorebtn = $('.mb2-pb-uploadbackup-restore').find('button');
                let fileinfo = $('.restore-backup').find('.file-info');

                fileinfo.html(data.message);

                // Set restore button data.
                if (data.isvalid) {
                    restorebtn.removeClass('d-none');
                    restorebtn.attr('data-backupfileid', data.backupfileid);
                    restorebtn.attr('data-dirname', data.dirname);
                } else {
                    restorebtn.addClass('d-none');
                }

                // Rebuilt the file picker.
                let filepickerctn = form.find('.filepicker-filename');
                filepickerctn.html(filepickerContent());

                // Enable the upload button.
                btn.prop('disabled', false);
                btn.removeClass('processing');
                btn.text(lang.attr('data-uploadfile'));

                return;
            }).catch(err => {
                require(["core/log"], function(log) {
                    log.debug(err);
                });
            });
        });

    };

    const filepickerContent = () => {

        var html = '';

        html += '<div class="filepicker-container">';
        html += '<div class="dndupload-message">You can drag and drop files here to add them. <br>';
        html += '<div class="dndupload-arrow d-flex"><i class="fa fa-arrow-circle-o-down fa-3x m-auto"></i></div>';            
        html += '</div>';
        html += '</div>';
        html += '<div class="dndupload-progressbars"></div>';

        return html;

    };

    return {
        backupList: backupList,
        createBackup: createBackup,
        deleteBackup: deleteBackup,
        uploadBackup: uploadBackup
    }

});
