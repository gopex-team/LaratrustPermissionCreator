<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit188a4d64d46c00ec47ecf1d46dfbc6cc
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Gopex\\LaratrustPermissionCreator\\' => 33,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Gopex\\LaratrustPermissionCreator\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit188a4d64d46c00ec47ecf1d46dfbc6cc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit188a4d64d46c00ec47ecf1d46dfbc6cc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit188a4d64d46c00ec47ecf1d46dfbc6cc::$classMap;

        }, null, ClassLoader::class);
    }
}