<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit72a546785d291cc3a436bc9eadb74cdf
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Lcobucci\\JWT\\' => 13,
            'Lcobucci\\Clock\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Lcobucci\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/lcobucci/jwt/src',
        ),
        'Lcobucci\\Clock\\' => 
        array (
            0 => __DIR__ . '/..' . '/lcobucci/clock/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit72a546785d291cc3a436bc9eadb74cdf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit72a546785d291cc3a436bc9eadb74cdf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit72a546785d291cc3a436bc9eadb74cdf::$classMap;

        }, null, ClassLoader::class);
    }
}
