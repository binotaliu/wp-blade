<?php

namespace Fiskhandlarn;

use Illuminate\Container\Container;
use Sober\Controller\Utils;

/**
 * This is the loader class for blade controllers
 *
 * @author Oskar Joelson <oskar@joelson.org>
 */
class BladeControllerLoader
{
    private static $instance = null;

    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    private static function instance(): BladeControllerLoader
    {
        if (self::$instance === null) {
            self::$instance = new BladeControllerLoader(
                rtrim(apply_filters('blade/controller/namespace', 'App\Controllers'))
            );
        }

        return self::$instance;
    }

    public static function dataFromController(string $controllerClass, array $additionalData = []): ?array
    {
        $class = self::getClassToRun(self::instance()->namespace, $controllerClass);

        if ($class !== null) {
            $container = Container::getInstance();

            // Recreate the class so that $post is included
            $controller = $container->make($class);

            // Params
            $controller->__setParams();

            // Lifecycle
            $controller->__before();

            // Data
            $controller->__setData($additionalData);

            // Lifecycle
            $controller->__after();

            // Return
            return $controller->__getData();
        }
    }

    private static function getClassToRun(string $namespace, string $class): ?string
    {
        try {
            $reflection = new \ReflectionClass($namespace . '\\' . $class);
        } catch (\Exception $exception) {
            // class not found
            throw new \Exception("No such class found in namespace $namespace: $class");
            return null;
        }

        $filename = $reflection->getFileName();

        // Exclude non-Controller classes
        if (!$reflection->isSubclassOf('Fiskhandlarn\BladeController')) {
            throw new \Exception("Class does not extend BladeController: $namespace\\$class");
            return null;
        }

        return $namespace . '\\' . pathinfo($filename, PATHINFO_FILENAME);
    }
}
