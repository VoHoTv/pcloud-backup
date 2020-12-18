(function( $ ) {

    $(function() {

        /**
         * Initialize
         */

        let $tree = $("#tree").fancytree({
            source: $.ajax({
                url: pcloudBackup.ajaxurl,
                data: { action : 'get_root_folders'},
                dataType: 'json',
            }),

            lazyLoad: function(event, data) {
                let node = data.node;

                data.result = {
                    url: pcloudBackup.ajaxurl,
                    data: {key: node.key, 'action': 'get_child_folders'}
                }
            }
        });
        
        $('#pcloud-backup-wizard').smartWizard({
            theme: 'dots',
            autoAdjustHeight: false,
            enableURLhash: false,
            toolbarSettings: {
                showPreviousButton: false,
            },
            anchorSettings: {
                anchorClickable: false,
            },
        });

        /**
         * Event Listeners
         */

        $('#add-folder').on('click', function() {

            let parentFolder = $tree.fancytree('getTree').getActiveNode();
            
            $.ajax({
                type: 'POST',
                url: pcloudBackup.ajaxurl,
                data: {
                    'action'      : 'create_folder',
                    'folder_name' : $('#folder-name').val(),
                    'parent_folder_id' : parentFolder.key,
                },
                dataType: 'json',
                success: function (response) {
                    let rootNode = $tree.fancytree('getTree').getNodeByKey(parentFolder.key);
                    $('#folder-name').val('');
                    rootNode.resetLazy();
                    rootNode.setExpanded(true);
                }
            });
        });

        $('#pcloud-backup-wizard').on('stepContent', function(e, anchorObject, stepIndex, stepDirection) {
            if(stepIndex !== 3) return;

            data = {
                action: 'upload_backup',
                nonce: pcloudBackup.nonce,
                folder_id: $('#tree').fancytree('getTree').getActiveNode().key,
            };

            if($('#pcloud_backup_files').is(':checked')) {
                data.pcloud_backup_files = 'yes';
            }

            if($('#pcloud_backup_database').is(':checked')) {
                data.pcloud_backup_database = 'yes';
            }

            $.ajax({
                type: 'POST',
                url: pcloudBackup.ajaxurl,
                data: data,
                dataType: 'json',
                success: function (response) {
                    // $('#pcloud-backup-wizard').smartWizard('reset');
                }
            });
        });

        $('#pcloud-backup-wizard').on('leaveStep', function(e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
            if(currentStepIndex === 2 && !$('#tree').fancytree('getTree').getActiveNode()) return false;
        });

    });

})( jQuery );
