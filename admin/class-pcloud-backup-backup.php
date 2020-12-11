<?php

class Pcloud_Backup_Backup {

    private $pcloud_file;
    
	private $zip;

	public function __construct( pCloud\File $pcloud_file, ZipArchive $zip ) {

		$this->pcloud_file = $pcloud_file;
		$this->zip = $zip;

    }

    public function create_backup()
    {   
        $location = $this->get_backup_file_location();

        $this->zip->open($location, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if(isset($_POST['pcloud_backup_files'])) {
            $this->create_wp_content_backup();
        }

        if(isset($_POST['pcloud_backup_database'])) {
            $database_location = $this->create_database_backup();
        }

        $this->zip->close();

        $this->upload_backup($location, $_POST['folder_id'] ?? 0);

        unlink($location);
        unlink($database_location);

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
