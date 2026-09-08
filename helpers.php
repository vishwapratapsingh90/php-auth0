<?php

declare(strict_types=1);

function getView(string $moduleName, string $viewFile, array $data = []): never
{
    if (!preg_match('/\A[a-zA-Z0-9_-]+\z/', $moduleName) || !preg_match('/\A[a-zA-Z0-9_-]+\z/', $viewFile)) {
        throw new InvalidArgumentException('Invalid module or view name.');
    }

    $viewPath = __DIR__ . '/modules/' . $moduleName . '/views/' . $viewFile . '.phtml';
    if (!is_file($viewPath)) {
        throw new RuntimeException('View not found: ' . $moduleName . '/' . $viewFile);
    }

    extract($data, EXTR_SKIP);
    require $viewPath;
    exit;
}
