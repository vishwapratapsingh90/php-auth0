<?php

declare(strict_types=1);

$moduleFiles = [];
$moduleDirectory = new RecursiveDirectoryIterator(
    __DIR__,
    FilesystemIterator::SKIP_DOTS,
);
$moduleIterator = new RecursiveIteratorIterator($moduleDirectory);

foreach ($moduleIterator as $moduleFile) {
    if (!$moduleFile->isFile() || 'php' !== strtolower($moduleFile->getExtension())) {
        continue;
    }

    $modulePath = $moduleFile->getRealPath();
    if (false !== $modulePath && $modulePath !== realpath(__FILE__)) {
        $moduleFiles[] = $modulePath;
    }
}

sort($moduleFiles, SORT_STRING);

foreach ($moduleFiles as $moduleFile) {
    require_once $moduleFile;
}
