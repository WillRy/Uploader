# Uploader

Automatizar o upload de arquivos, validando o tipo de arquivo e otimizando imagens

## Recursos

- Classes prontas que validam o mime type e extensões dos arquivos
- Fácil utilização e customização do diretório de upload e extensões permitidas

## Instalação

A instalação está disponível via composer:

```bash
"willry/uploader": "^1.0"
```

Ou

```bash
composer require willry/uploader
```

## Exemplo

Para mais detalhes sobre como usar, veja uma pasta de exemplo no diretório do componente. Nela terá um exemplo de uso para cada classe. Ele funciona assim:


```php
<?php

require __DIR__ . "/../vendor/autoload.php";

use WillRy\Uploader\Image;
use WillRy\Uploader\File;
use WillRy\Uploader\Send;

if ($_POST && !empty($_GET["default"])) {
    $path = $_FILES['file']['name'];
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    //it's an image
    $isImage = Image::isAllowed($extension);

    //it's an commom file
    $isFile = File::isAllowed($extension);

    if ($isImage) {
        //cropper
        $maxWidth = 500;

        //optional
        //jpg -> 0% ~ 100%
        //png -> 0 ~ 9
        $quality = ["jpg" => 75, "png" => 5];

        $image = new Image("uploads");
        $filePath = $image->upload($_FILES["file"], $_POST['file_name'], $maxWidth, $quality);
    } elseif ($isFile) {

        $file = new File("uploads");
        $filePath = $file->upload($_FILES["file"], $_POST['file_name']);
    } else {
        throw new Exception("Invalid mime type or extension");
    }

    var_dump($filePath);
}

if ($_POST && !empty($_GET["custom"])) {
    $tiposDeExtensao = ["csv"];
    $send = new Send("uploads", $tiposDeExtensao);
    $filePath = $send->upload($_FILES["file"], $_POST['file_name']);
    var_dump($filePath);
}

?>

<h2>Envio de imagem ou arquivo</h2>
<form action="?default=default" name="upload" method="post" enctype="multipart/form-data">
    <input name="file_name" type="text" value="My filename" required />
    <input name="file" type="file" required />
    <button>Enviar</button>
</form>

<h2>Envio direto de arquivo, de forma customizada</h2>
<form action="?custom=custom" name="upload" method="post" enctype="multipart/form-data">
    <input name="file_name" type="text" value="My filename" required />
    <input name="file" type="file" required />
    <button>Enviar</button>
</form>
```

## Creditos

- [WillRy](https://github.com/willry)

## License

The MIT License (MIT). Please see [License File](https://github.com/willry/uploader/blob/master/LICENSE) for more information.
