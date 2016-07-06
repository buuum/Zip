<?php

namespace Buuum\Zip;

use ZipArchive;

class Zip
{
    /**
     * ZipArchive internal pointer
     *
     * @var ZipArchive
     */
    private $zip_archive = null;

    /**
     * zip file name
     *
     * @var string
     */
    private $zip_file = null;

    /**
     * Path original files
     *
     * @var mixed
     */
    private $path = false;

    /**
     * Array of well known zip status codes
     *
     * @var array
     */
    private static $zip_status_codes = Array(
        ZipArchive::ER_OK          => 'No error',
        ZipArchive::ER_MULTIDISK   => 'Multi-disk zip archives not supported',
        ZipArchive::ER_RENAME      => 'Renaming temporary file failed',
        ZipArchive::ER_CLOSE       => 'Closing zip archive failed',
        ZipArchive::ER_SEEK        => 'Seek error',
        ZipArchive::ER_READ        => 'Read error',
        ZipArchive::ER_WRITE       => 'Write error',
        ZipArchive::ER_CRC         => 'CRC error',
        ZipArchive::ER_ZIPCLOSED   => 'Containing zip archive was closed',
        ZipArchive::ER_NOENT       => 'No such file',
        ZipArchive::ER_EXISTS      => 'File already exists',
        ZipArchive::ER_OPEN        => 'Can\'t open file',
        ZipArchive::ER_TMPOPEN     => 'Failure to create temporary file',
        ZipArchive::ER_ZLIB        => 'Zlib error',
        ZipArchive::ER_MEMORY      => 'Malloc failure',
        ZipArchive::ER_CHANGED     => 'Entry has been changed',
        ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
        ZipArchive::ER_EOF         => 'Premature EOF',
        ZipArchive::ER_INVAL       => 'Invalid argument',
        ZipArchive::ER_NOZIP       => 'Not a zip archive',
        ZipArchive::ER_INTERNAL    => 'Internal error',
        ZipArchive::ER_INCONS      => 'Zip archive inconsistent',
        ZipArchive::ER_REMOVE      => 'Can\'t remove file',
        ZipArchive::ER_DELETED     => 'Entry has been deleted'
    );

    public function __construct($zip_file)
    {

        if (empty($zip_file)) {
            throw new \Exception(self::getStatus(ZipArchive::ER_NOENT));
        }

        $this->zip_file = $zip_file;

    }

    public static function open($zip_file)
    {

        try {

            $zip = new Zip($zip_file);

            $zip->setArchive(self::openZipFile($zip_file));

        } catch (\Exception $ze) {

            throw $ze;

        }

        return $zip;

    }

    public static function check($zip_file)
    {

        try {

            $zip = self::openZipFile($zip_file, ZipArchive::CHECKCONS);

            $zip->close();

        } catch (\Exception $ze) {

            throw $ze;

        }

        return true;

    }

    public static function create($zip_file, $overwrite = false)
    {

        try {

            $zip = new Zip($zip_file);

            if ($overwrite) {
                $zip->setArchive(self::openZipFile($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE));
            } else {
                $zip->setArchive(self::openZipFile($zip_file, ZipArchive::CREATE));
            }

        } catch (\Exception $ze) {

            throw $ze;

        }

        return $zip;

    }

    final public function setArchive(ZipArchive $zip)
    {

        $this->zip_archive = $zip;

        return $this;

    }

    final public function getArchive()
    {

        return $this->zip_archive;

    }


    final public function getZipFile()
    {

        return $this->zip_file;

    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }


    public function listFiles()
    {

        $list = Array();

        for ($i = 0; $i < $this->zip_archive->numFiles; $i++) {

            $name = $this->zip_archive->getNameIndex($i);

            if ($name === false) {
                throw new \Exception(self::getStatus($this->zip_archive->status));
            }

            array_push($list, $name);

        }

        return $list;

    }

    public function extract($destination, $files = null)
    {

        if (empty($destination)) {
            throw new \Exception('Invalid destination path');
        }

        if (!file_exists($destination)) {

            $omask = umask(0);

            $action = mkdir($destination, 0777, true);

            umask($omask);

            if ($action === false) {
                throw new \Exception("Error creating folder " . $destination);
            }

        }

        if (!is_writable($destination)) {
            throw new \Exception('Destination path not writable');
        }

        if (is_array($files) && @sizeof($files) != 0) {

            $file_matrix = $files;

        } else {

            $file_matrix = $this->getArchiveFiles();

        }

        $extract = $this->zip_archive->extractTo($destination, $file_matrix);

        if ($extract === false) {
            throw new \Exception(self::getStatus($this->zip_archive->status));
        }

        return true;

    }

    public function add($file_name_or_array, $newname = false)
    {
        if (empty($file_name_or_array)) {
            throw new \Exception(self::getStatus(ZipArchive::ER_NOENT));
        }

        try {
            if (is_array($file_name_or_array)) {

                foreach ($file_name_or_array as $file_name => $newname) {
                    $this->addFile($file_name, $newname);
                }

            } else {
                $this->addFile($file_name_or_array, $newname);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    private function addFile($file_name, $newname = false)
    {
        if ($newname) {
            $this->zip_archive->addEmptyDir(dirname($newname));
        } elseif ($this->getPath()) {
            $newname = str_replace($this->getPath() . '/', '', $file_name);
        }
        $this->zip_archive->addFile($file_name, $newname);
    }

    public function delete($file_name_or_array)
    {

        if (empty($file_name_or_array)) {
            throw new \Exception(self::getStatus(ZipArchive::ER_NOENT));
        }

        try {

            if (is_array($file_name_or_array)) {

                foreach ($file_name_or_array as $file_name) {
                    $this->deleteItem($file_name);
                }

            } else {
                $this->deleteItem($file_name_or_array);
            }

        } catch (\Exception $ze) {

            throw $ze;

        }

        return $this;

    }

    public function close()
    {

        if ($this->zip_archive->close() === false) {
            throw new \Exception(self::getStatus($this->zip_archive->status));
        }

        return true;

    }

    private function getArchiveFiles()
    {

        $list = array();

        for ($i = 0; $i < $this->zip_archive->numFiles; $i++) {

            $file = $this->zip_archive->statIndex($i);

            if ($file === false) {
                continue;
            }

            $name = str_replace('\\', '/', $file['name']);

            var_dump($name);

            if ($name == "." || $name == "..") {
                continue;
            }

            array_push($list, $name);

        }

        return $list;

    }


    private function deleteItem($file)
    {

        $deleted = $this->zip_archive->deleteName($file);

        if ($deleted === false) {
            throw new \Exception(self::getStatus($this->zip_archive->status));
        }

    }

    private static function openZipFile($zip_file, $flags = null)
    {

        $zip = new ZipArchive();

        $open = $zip->open($zip_file, $flags);

        if ($open !== true) {
            throw new \Exception(self::getStatus($open));
        }

        return $zip;

    }

    private static function getStatus($code)
    {

        if (array_key_exists($code, self::$zip_status_codes)) {
            return self::$zip_status_codes[$code];
        } else {
            return sprintf('Unknown status %s', $code);
        }

    }


}