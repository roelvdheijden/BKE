<?PHP

function __Autoload($class) {
    $filePath = str_replace('\\', '/', $class);
    $file = __DIR__ . "/{$filePath}.php";
    require_once($file);
}
spl_autoload_register('__Autoload');

?>