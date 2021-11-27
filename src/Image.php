<?php

namespace WillRy\Uploader;

/**
 *
 */
class Image extends Uploader
{

    /**
     * @var string[]
     */
    protected static $allowedExt = [
        "jpeg",
        "jpg",
        "png",
        "gif",
    ];


    /**
     * @param array $file
     * @param string $name
     * @param int $maxWidth
     * @param array|int[]|null $quality
     * @return string
     * @throws \Exception
     */
    public function upload(array $file, string $name, int $maxWidth = 2000, ?array $quality = ["jpg" => 75, "png" => 5])
    {
        $this->generateFileData($file, $name);

        //valida tipo
        if (!$this->validateType()) {
            throw new \Exception("Invalid mime type or extension");
        }

        if (!$this->imageCreate($file)) {
            throw new \Exception("Invalid mime type or extension");
        }

        if ($this->extension === "gif") {
            move_uploaded_file("{$file['tmp_name']}", "{$this->uploadDir}/{$this->fileName}");
            return "{$this->uploadDir}/{$this->fileName}";
        }

        $this->imageGenerate($maxWidth, $quality);

        return "{$this->uploadDir}/{$this->fileName}";
    }

    /**
     * @param array $image
     * @return bool
     */
    private function imageCreate(array $image): bool
    {
        if ($image['type'] == "image/jpeg") {
            $this->file = imagecreatefromjpeg($image['tmp_name']);
            $this->extension = "jpg";
            $this->fixImageRotate($image);
            return true;
        }

        if ($image['type'] == "image/png") {
            $this->file = imagecreatefrompng($image['tmp_name']);
            $this->extension = "png";
            return true;
        }

        if ($image['type'] == "image/gif") {
            $this->extension = "gif";
            return true;
        }

        return false;
    }

    /**
     * @param $image
     */
    private function fixImageRotate($image): void
    {
        $exif = @exif_read_data($image["tmp_name"]);
        $orientation = (!empty($exif["Orientation"]) ? $exif["Orientation"] : null);

        switch ($orientation) {
            case 8:
                $this->file = imagerotate($this->file, 90, 0);
                break;
            case 3:
                $this->file = imagerotate($this->file, 180, 0);
                break;
            case 6:
                $this->file = imagerotate($this->file, -90, 0);
                break;
        }
    }

    /**
     * @param int $width
     * @param array $quality
     */
    private function imageGenerate(int $width, array $quality): void
    {
        $fileX = imagesx($this->file);
        $fileY = imagesy($this->file);
        $imageW = ($width < $fileX ? $width : $fileX);
        $imageH = ($imageW * $fileY) / $fileX;
        $imageCreate = imagecreatetruecolor($imageW, $imageH);

        if ($this->extension == "jpg") {
            imagecopyresampled($imageCreate, $this->file, 0, 0, 0, 0, $imageW, $imageH, $fileX, $fileY);
            imagejpeg($imageCreate, "{$this->uploadDir}/{$this->fileName}", $quality['jpg']);
        }

        if ($this->extension == "png") {
            imagealphablending($imageCreate, false);
            imagesavealpha($imageCreate, true);
            imagecopyresampled($imageCreate, $this->file, 0, 0, 0, 0, $imageW, $imageH, $fileX, $fileY);
            imagepng($imageCreate, "{$this->uploadDir}/{$this->fileName}", $quality['png']);
        }

        imagedestroy($this->file);
        imagedestroy($imageCreate);
    }
}
