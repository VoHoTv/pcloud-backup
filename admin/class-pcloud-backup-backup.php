<?php

class Pcloud_Backup_Backup {

	public function __construct( Pcloud_Backup_Backup_Request $backup_request ) {

        $this->backup_request = $backup_request;

    }

    public function create_backup()
    {   
        check_ajax_referer('backup_nonce', 'nonce');
        $this->backup_request->data(array('folder_id' => $_POST['folder_id'], 'pcloud_backup_files' => $_POST['pcloud_backup_files'] ?? null, 'pcloud_backup_database' => $_POST['pcloud_backup_database'] ?? null))->dispatch();

        echo json_encode(array());
        wp_die();
    }

    private function create_wp_content_backup() {
        $rootPath = realpath(WP_CONTENT_DIR);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath), 
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file)
        {
            if (!$file->isDir()) {

                $filePath = $file->getRealPath();

                $relativePath = substr($filePath, strlen($rootPath) + 1);

                $this->zip->addFile($filePath, $relativePath);

            }
        }
    }

    private function create_database_backup() : string {
        $location = get_temp_dir().uniqid().'-database.sql';

        $dump = new MySQLDump(new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME));
        $dump->save($location);

        $this->zip->addFile($location, basename($location));

        return $location;
    }

    private function upload_backup( string $path, int $folder_id  )
    {
        $this->pcloud_file->upload($path, $folder_id);
    }

    private function get_backup_file_location() {
        return get_temp_dir().get_bloginfo('name').' backup - '.date('d-m-Y').'.zip';
    }

}
