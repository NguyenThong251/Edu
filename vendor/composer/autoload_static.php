<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4e2fad0377e7a3a0133737ca66cf8f47
{
    public static $files = array (
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
    );

    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
        'S' => 
        array (
            'Symfony\\Contracts\\Service\\' => 26,
            'Symfony\\Contracts\\Cache\\' => 24,
            'Symfony\\Component\\VarExporter\\' => 30,
            'Symfony\\Component\\Process\\' => 26,
            'Symfony\\Component\\Cache\\' => 24,
            'Spatie\\TemporaryDirectory\\' => 26,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Container\\' => 14,
            'Psr\\Cache\\' => 10,
        ),
        'F' => 
        array (
            'FFMpeg\\' => 7,
        ),
        'E' => 
        array (
            'Evenement\\' => 10,
        ),
        'A' => 
        array (
            'Alchemy\\BinaryDriver\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
        'Symfony\\Contracts\\Service\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/service-contracts',
        ),
        'Symfony\\Contracts\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/cache-contracts',
        ),
        'Symfony\\Component\\VarExporter\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-exporter',
        ),
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Symfony\\Component\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/cache',
        ),
        'Spatie\\TemporaryDirectory\\' => 
        array (
            0 => __DIR__ . '/..' . '/spatie/temporary-directory/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'FFMpeg\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ffmpeg/php-ffmpeg/src/FFMpeg',
        ),
        'Evenement\\' => 
        array (
            0 => __DIR__ . '/..' . '/evenement/evenement/src',
        ),
        'Alchemy\\BinaryDriver\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ffmpeg/php-ffmpeg/src/Alchemy/BinaryDriver',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        '�' => __DIR__ . '/..' . '/symfony/cache/Traits/ValueWrapper.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4e2fad0377e7a3a0133737ca66cf8f47::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4e2fad0377e7a3a0133737ca66cf8f47::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4e2fad0377e7a3a0133737ca66cf8f47::$classMap;

        }, null, ClassLoader::class);
    }
}
