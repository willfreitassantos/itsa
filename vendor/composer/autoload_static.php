<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdd1fe3f32e87b6a868f1d9454907e521
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'ITSA\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ITSA\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdd1fe3f32e87b6a868f1d9454907e521::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdd1fe3f32e87b6a868f1d9454907e521::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
