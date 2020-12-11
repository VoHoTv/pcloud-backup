(function( $ ) {

    $(function() {

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

        let $tree = $("#tree").fancytree({
            // Initial node data that sets 'lazy' flag on some leaf nodes
            source: $.ajax({
                url: pCloudBackup.ajaxurl,
                data: { action : 'get_root_folders'},
                dataType: 'json',
            }),

            lazyLoad: function(event, data) {
                var node = data.node;

                data.result = {
                    url: pCloudBackup.ajaxurl,
                    data: {key: node.key, 'action': 'get_child_folders'}
                }
            }
        });

        $('#pcloud-backup-wizard').on('stepContent', function(e, anchorObject, stepIndex, stepDirection) {
            if(stepIndex !== 3) return;

            data = {
                action: 'upload_backup',
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
                url: pCloudBackup.ajaxurl,
                data: data,
                dataType: 'json',
                success: function (response) {
                    $('#pcloud-backup-wizard').smartWizard('reset');
                }
            });
        });

        $('#pcloud-backup-wizard').on('leaveStep', function(e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
            if(currentStepIndex === 2 && !$('#tree').fancytree('getTree').getActiveNode()) return false;
        });

    });

})( jQuery );
