<?php
/**
 * Script para remover comentarios personalizados de archivos PHP y Vue.
 * Exclusiones: routes/api.php, frontend/src/router/index.js
 */
$root = dirname(__DIR__);
$exclude = [
    realpath($root.'/backend/routes/api.php'),
    realpath($root.'/frontend/src/router/index.js'),
];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$changed = 0;
foreach ($it as $file) {
    $path = $file->getPathname();
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['php','vue','js'])) continue;
    $real = realpath($path);
    if (in_array($real, $exclude)) continue;
    $content = file_get_contents($path);

    // Saltar vendor
    if (strpos($real, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR) !== false) continue;

    $original = $content;

    if ($ext === 'php') {
        // Remover comentarios de línea que no sean encabezados de licencia (// ...)
        $content = preg_replace('/^[ \t]*\/\/(?!\s*@|\s*php).*/m', '', $content);
        // Remover bloques /* ... */ que no contengan @ o phpdoc
        $content = preg_replace('#/\*(?!\*)(?!.*@).*?\*/#s', '', $content);
    } elseif ($ext === 'vue' || $ext === 'js') {
        // En templates Vue quitar <!-- -->
        $content = preg_replace('/<!--(?!\s*#)(.*?)-->/s', '', $content);
        // Quitar // ...
        $content = preg_replace('/^[ \t]*\/\/(?!\s*@).*/m', '', $content);
        // Quitar /* ... */ no con @preserve
        $content = preg_replace('#/\*(?!.*@preserve).*?\*/#s', '', $content);
    }

    // Compactar líneas en blanco múltiples
    $content = preg_replace("/\n{3,}/", "\n\n", $content);

    if ($content !== $original) {
        file_put_contents($path, $content);
        $changed++;
        echo "Limpio: $path\n";
    }
}

echo "Total archivos modificados: $changed\n";
