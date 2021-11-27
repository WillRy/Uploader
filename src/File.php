<?php

namespace WillRy\Uploader;

/**
 *
 */
class File extends Uploader
{

    /**
     * @var string[]
     */
    protected static $allowedExt = [
        "zip",
        "rar",
        "bz",
        "pdf",
        "doc",
        "docx",
        "csv",
        "xls",
        "xlsx",
        "ods",
        "odt",
        "mp3",
        "mp4"
    ];

    /**
     * @param array $file
     * @param string $name
     * @return string
     * @throws \Exception
     */
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