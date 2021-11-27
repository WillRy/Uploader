<?php

namespace WillRy\Uploader;

/**
 *
 */
abstract class Uploader
{
    /**
     * @var string
     */
    protected $uploadDir;

    /**
     * @var
     */
    protected $file;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $fileName;

    protected static $allowedExt = [];

    /**
     * @param string $uploadDir
     * @throws \Exception
     */
    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
        $this->initDir($uploadDir);
    }

    /**
     * Create the upload folder
     * @param string $dirPath
     * @param int $permission
     * @throws \Exception
     */
    protected function initDir(string $dirPath, int $permission = 0755)
    {
        var_dump($dirPath);
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            $created = mkdir($dirPath, $permission, true);
            if (!$created) {
                throw new \Exception("Unable to create an upload folder");
            }
        }
    }

    /**
     * Validate mime type and extension
     * @return bool
     */
    protected function validateType()
    {
        if (!in_array($this->extension, static::$allowedExt)) {
            return false;
        }
        return true;
    }

    /**
     * Create file name
     * @param $name
     * @return string
     */
    protected function generateName($name)
    {
        $name = filter_var(mb_strtolower($name), FILTER_SANITIZE_STRIPPED);
        $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';
        $name = str_replace(["-----", "----", "---", "--"], "-",
            str_replace(" ", "-", trim(strtr(utf8_decode($name), utf8_decode($formats), $replace))));

        $this->fileName = "{$name}." . $this->extension;

        if (file_exists("{$this->uploadDir}/{$this->fileName}") && is_file("{$this->uploadDir}/{$this->fileName}")) {
            $this->fileName = "{$name}-" . time() . ".{$this->extension}";
        }
        return $this->fileName;
    }

    /**
     * Extract file info
     * @param $file
     * @param $name
     */
    protected function generateFileData($file, $name)
    {
        $this->file = $file;
        $this->extension = mb_strtolower(pathinfo($this->file['name'])['extension']);
        $this->fileName = $this->generateName($name);
    }

    /**
     * @return mixed
     */
    public static function allowedExt()
    {
        return static::$allowedExt;
    }


    public static function isAllowed(string $extension)
    {
        return in_array($extension, static::allowedExt());
    }
}
