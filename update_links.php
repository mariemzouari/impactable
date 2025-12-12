<?php
$directory = './View';
$iterator = new RecursiveDirectoryIterator($directory);
$filter = new RecursiveCallbackFilterIterator($iterator, function ($current, $key, $iterator) {
    if ($iterator->hasChildren()) {
        return true;
    }
    return $current->getExtension() === 'php';
});

foreach (new RecursiveIteratorIterator($filter) as $file) {
    $content = file_get_contents($file);
    $updated = str_replace('../controller/control.php?action=', 'index.php?action=', $content);
    if ($content !== $updated) {
        file_put_contents($file, $updated);
        echo "Updated: " . $file . "\n";
    }
}

echo "Done!\n";
?>
