<?php

namespace WillRy\Uploader;

class Send extends Uploader
{
    /**
     * @var string[]
     */
    protected static $allowedExt = [];

    public function __construct(string $uploadDir, $allowedExt)
    {
        parent::__construct($uploadDir);
        self::$allowedExt = $allowedExt;
    }

    public function upload(array $file, string $name)
    {

        $this->generateFileData($file, $name);

        if (!$this->validateType()) {
            throw new \Exception("Invalid mime type or extension");
        }

        move_uploaded_file("{$file['tmp_name']}", "{$this->uploadDir}/{$this->fileName}");
        return "{$this->uploadDir}/{$this->fileName}";
    }
}