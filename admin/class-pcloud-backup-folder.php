<?php

class Pcloud_Backup_Folder extends pCloud\Folder {

    public function get_root_folders()
    {
        $files = $this->getContent(0);

        echo json_encode(
            array_map(function($file) {
                $args = array(
                    'title' => $file->name,
                    'key' => $file->folderid,
                ); 

                if($file->isfolder) {
                    $args['folder'] = true;
                    $args['lazy'] = true;
                }

                return $args;
            }, $files)
        );

        wp_die();
    }

    public function get_child_folders()
    {
        $files = $this->getContent(intval($_GET['key']));

        echo json_encode(
            array_map(function($file) {
                $args = array(
                    'title' => $file->name,
                    'key' => $file->folderid,
                ); 

                if($file->isfolder) {
                    $args['folder'] = true;
                }

                return $args;
            }, $files)
        );

        wp_die();
    }

    public function create_folder() {

        $folder_name = sanitize_text_field($_POST['folder_name']);

        $folder_id = $this->create($folder_name, intval($_POST['parent_folder_key']));

        echo json_encode(array('folderId' => $folder_id, 'folderName' => $folder_name));
        wp_die();
    }

}
