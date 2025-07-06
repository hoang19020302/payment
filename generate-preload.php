<?php

// File preload sẽ được tạo
$preloadFile = __DIR__ . '/preload.php';

$header = <<<'PHP'
<?php

// Tự động tạo bởi generate-preload.php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

PHP;

// Tìm tất cả file PHP trong thư mục app/
$directory = new RecursiveDirectoryIterator(__DIR__ . '/app');
$iterator = new RecursiveIteratorIterator($directory);
$phpFiles = new RegexIterator($iterator, '/\.php$/');

$classes = [];

foreach ($phpFiles as $file) {
    $path = $file->getRealPath();

    // Đọc namespace và class
    $tokens = token_get_all(file_get_contents($path));

    $namespace = '';
    $class = '';
    $i = 0;
    while ($i < count($tokens)) {
        if ($tokens[$i][0] === T_NAMESPACE) {
            $i++;
            while ($tokens[$i][0] === T_WHITESPACE) $i++;
            while (in_array($tokens[$i][0], [T_STRING, T_NS_SEPARATOR])) {
                $namespace .= $tokens[$i++][1];
            }
        }

        if ($tokens[$i][0] === T_CLASS) {
            $i++;
            while ($tokens[$i][0] === T_WHITESPACE) $i++;
            if ($tokens[$i][0] === T_STRING) {
                $class = $tokens[$i][1];
                break;
            }
        }
        $i++;
    }

    if ($class) {
        $fullClass = $namespace ? $namespace . '\\' . $class : $class;
        $classes[] = $fullClass;
    }
}

$content = $header . PHP_EOL;

foreach ($classes as $class) {
    $content .= "class_exists(\\$class::class);\n";
}

file_put_contents($preloadFile, $content);

echo "✅ Đã tạo preload.php với " . count($classes) . " class.\n";
