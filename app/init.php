<?php
/**
 * Load the config.
 */
$config = (require __DIR__.'/../config.php');

/**
 * Require the functions file.
 */
require __DIR__.'/functions.php';

/**
 * Set the script timeout.
 */
ini_set('upload_max_filesize', '256M');
ini_set('post_max_size', '256M');

/**
 * Create the URL.
 */
$url = explode('/', $_SERVER['REQUEST_URI']);
array_shift($url);
$url[0] = $url[0] == '' ? '/' : $url[0];

/**
 * Handle the request.
 */
if ($url[0] == '/') {
    render('index');
} elseif ($url[0] == 'auth') {
    header('Content-Type: application/json');
    if (!isset($_POST['password'])) {
        die(json_encode([
        'status' => 'error',
        'message' => 'No password given.'
        ]));
    }
    if ($_POST['password'] != $config['password']) {
        die(json_encode([
        'status' => 'error',
        'message' => 'Invalid password.'
        ]));
    }
    die(json_encode([
    'status' => 'success',
    'message' => 'Valid password!'
    ]));
} elseif ($url[0] == 'upload') {
    header('Content-Type: application/json');
    if (!isset($_FILES['file'])) {
        die(json_encode([
        'status' => 'error',
        'message' => 'No file given.'
        ]));
    }
    if (!isset($_POST['password'])) {
        die(json_encode([
        'status' => 'error',
        'message' => 'No password given.'
        ]));
    }
    if ($_POST['password'] != $config['password']) {
        die(json_encode([
        'status' => 'error',
        'message' => 'Invalid password.'
        ]));
    }

    $fileDir = randomString();
    while (is_dir(sprintf('%s/%s', $config['fileDir'], randomString($config['fileLength'])))) {
        $fileDir = randomString($config['fileLength']);
    }

    mkdir(sprintf('%s/%s', $config['fileDir'], $fileDir));

    $filePath = sprintf(
        '%s/%s/%s%s',
        $config['fileDir'],
        $fileDir,
        randomString($config['fileLength']),
        substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.'))
    );

    move_uploaded_file(
        $_FILES['file']['tmp_name'],
        $filePath
    );

    file_put_contents(sprintf('%s/%s/info.json', $config['fileDir'], $fileDir), json_encode([
    'name' => basename($_FILES['file']['name']),
    'uploaded' => time()
    ]));

    die(json_encode([
    'status' => 'success',
    'message' => sprintf('%s/%s', $config['baseUrl'], $fileDir)
    ]));
} else {
    header('Content-Type: application/json');
    if (!is_dir(sprintf('%s/%s', $config['fileDir'], $url[0]))) {
        die(json_encode([
        'status' => 'error',
        'message' => 'File not found.'
        ]));
    }

    $fileDir = sprintf('%s/%s', $config['fileDir'], $url[0]);
    $fileScan = scandir($fileDir);
    array_splice($fileScan, array_search('info.json', $fileScan), 1);
    $file = basename($fileScan[2]);

    $filePath = sprintf('%s/%s', $fileDir, $file);

    $fileInfo = json_decode(
        file_get_contents(
            sprintf('%s/info.json', $fileDir)
        ),
        true
    );

    if (isset($url[1]) && $url[1] == 'direct') {
        header('Content-Type: '.mime_content_type($filePath));
        if (!isPlayable($filePath)) {
            header('Content-Length: '.filesize($filePath));
            header(
                sprintf("Content-Disposition: attachment; filename='%s'", $fileInfo['name'])
            );
        }
        return readfile($filePath);
    }

    header('Content-Type: text/html');
    render('download', [
    'FILENAME' => $fileInfo['name'],
    'FILESIZE' => humanFileSize(filesize($filePath)),
    'FILEDATE' => date('d.m.Y, H:i:s', $fileInfo['uploaded']),
    'FILETYPE' => pathinfo($filePath)['extension'],
    'FILEURL' => sprintf('%s/%s/direct', $config['baseUrl'], $url[0])
    ]);
}
