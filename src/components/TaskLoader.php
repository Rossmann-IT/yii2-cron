<?php
namespace rossmann\cron\components;

/**
 * Class TaskLoader
 * Loads classes and provides list of available methods
 * @author  mult1mate
 * @since 07.02.2016
 */
class TaskLoader
{
    /**
     * Contains array of directories from which TaskLoader will try to load classes
     * @var array
     */
    protected static $classFolders = [];

    /**
     * Scan folders for classes and return all their public methods
     *
     * @param string|array $folder
     * @param string|array $namespace
     *
     * @return array
     * @throws TaskManagerException
     */
    public static function getAllMethods($folder, $namespace = [])
    {
        self::setClassFolder($folder);
        $namespacesList = is_array($namespace) ? $namespace : [$namespace];
        $methods = [];

        $controllers = self::getControllersList(self::$classFolders, $namespacesList);
        foreach ($controllers as $c) {
            if (!class_exists($c)) {
                self::loadController($c);
            }
            $methods[$c] = self::getControllerMethods($c);
        }

        return $methods;
    }

    /**
     * Sets folders which contain needed classes
     *
     * @param $folder
     *
     * @return array
     */
    public static function setClassFolder($folder)
    {
        return self::$classFolders = is_array($folder) ? $folder : [$folder];
    }

    /**
     * Returns names of all php files in directories
     *
     * @param array $paths
     * @param       $namespacesList
     *
     * @return array
     * @throws TaskManagerException
     */
    protected static function getControllersList($paths, $namespacesList)
    {
        $controllers = [];
        foreach ($paths as $pathIndex => $path) {
            if (!file_exists($path)) {
                throw new TaskManagerException('folder ' . $path . ' does not exist');
            }
            $files = scandir($path);
            foreach ($files as $file) {
                if (preg_match('/^([A-Z]\w+)\.php$/', $file, $match)) {
                    $namespace = isset($namespacesList[$pathIndex]) ? $namespacesList[$pathIndex] . '\\' : '';
                    $controllers[] = $namespace . $match[1];
                }
            }
        }

        return $controllers;
    }

    /**
     * Looks for and loads required class via require_once
     *
     * @param string $className
     * @return bool
     * @throws TaskManagerException
     */
    public static function loadController($className)
    {
        foreach (self::$classFolders as $f) {
            $f = rtrim($f, '/');
            $filename = $f . '/' . $className . '.php';
            if (file_exists($filename)) {
                require_once $filename;
                if (class_exists($className)) {
                    return true;
                } else {
                    throw new TaskManagerException('file found but class ' . $className . ' not loaded');
                }
            }
        }

        throw new TaskManagerException('class ' . $className . ' not found');
    }

    /**
     * Returns all public methods for requested class
     *
     * @param string $class
     *
     * @return array
     * @throws TaskManagerException
     */
    public static function getControllerMethods($class)
    {
        if (!class_exists($class)) {
            throw new TaskManagerException('class ' . $class . ' not found');
        }
        $classMethods = get_class_methods($class);
        if ($parentClass = get_parent_class($class)) {
            $parentClassMethods = get_class_methods($parentClass);

            return array_diff($classMethods, $parentClassMethods);
        }

        return $classMethods;
    }
}
