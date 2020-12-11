<?php

class Pcloud_Backup_Ajax_Folders {

	private $pcloud_folder;

	public function __construct( pCloud\Folder $pcloud_folder ) {

		$this->pcloud_folder = $pcloud_folder;

    }

    public function get_root_folders()
    {
        $files = $this->pcloud_folder->getContent(0);

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
        $files = $this->pcloud_folder->getContent(intval($_GET['key']));

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

}
