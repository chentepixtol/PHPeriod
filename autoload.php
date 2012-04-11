<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once 'vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$libPath = '/usr/share/php/pcslib';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'PHPeriod' => 'src/',
    'Test'  => realpath('.'),
));
$loader->registerPrefix("Zend_", $libPath);
$loader->register();
