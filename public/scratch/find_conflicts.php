<?php
// We won't boot Laravel because that triggers the error handler
// We will just try to find which file has the merge conflict

$dir = new RecursiveDirectoryIterator('app');
foreach (new RecursiveIteratorIterator($dir) as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() !== 'php') continue;
    
    $content = file_get_contents($file->getPathname());
    if (strpos($content, '<<<<<<<') !== false) {
        echo "Merge conflict found in: " . $file->getPathname() . "\n";
    }
}

$dir = new RecursiveDirectoryIterator('resources');
foreach (new RecursiveIteratorIterator($dir) as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() !== 'php' && $file->getExtension() !== 'blade') continue;
    
    $content = file_get_contents($file->getPathname());
    if (strpos($content, '<<<<<<<') !== false) {
        echo "Merge conflict found in: " . $file->getPathname() . "\n";
    }
}
