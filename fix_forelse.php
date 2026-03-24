<?php
$dir = new RecursiveDirectoryIterator('d:/smartErp/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$count = 0;

foreach($ite as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);
        
        if (strpos($content, '@forelse') !== false) {
            $newContent = preg_replace_callback('/@forelse\s*\((.*?(?:\?\?[^as]+)?)\s+as\s+(.*?)\)/', function($matches) {
                // Ensure there is no trailing comma or unexpected characters
                $collection = trim($matches[1]);
                $item = trim($matches[2]);
                
                // Simple countable check, converting the collection block to safe iteration array
                return "@php \$__col = $collection; @endphp\n@if(is_array(\$__col) || \$__col instanceof \Countable ? count(\$__col) > 0 : !empty(\$__col))\n@foreach(\$__col as $item)";
            }, $content);
            
            // Replace @empty that are not followed by parentheses (e.g. @empty($var) should be ignored)
            $newContent = preg_replace('/@empty(?!\s*\()/m', '@endforeach' . "\n" . '@else', $newContent);
            
            // Replace @endforelse
            $newContent = str_replace('@endforelse', '@endif', $newContent);
            
            if ($content !== $newContent) {
                file_put_contents($filePath, $newContent);
                $count++;
            }
        }
    }
}
echo "Converted @forelse in $count blade files.\n";
