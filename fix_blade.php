<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$count = 0;
foreach($ite as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($file);
        $newContent = preg_replace('/\?\?\s*\[\]\s*as/', '?: [] as', $content);
        $newContent = preg_replace('/\?\?\s*collect\(\)\s*as/', '?: [] as', $newContent);
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            $count++;
        }
    }
}
echo "Updated $count blade files.\n";
