<?php

class Autoload
{
    private static $_class;
    
    private static $app;
    
    private static $config;
    
    private static $request;
    
    public static function _autoload($config)
    {
        self::$config = $config['params'];
        self::$request = $_REQUEST;
        
        $classes = $config['coreFiles'];
        
        if(is_array($classes))
        {   
            foreach($classes as $className => $classParams)
            {
                if(empty($className) || is_numeric($className))
                    $className = $classParams;

                self::$_class[$className] = $classParams;
                
                $classFile = str_replace('.', DIRECTORY_SEPARATOR, $className) . CLASS_POSTFIX.CLASS_EXT;
                
                if (file_exists(CLASSES_PATH . DIRECTORY_SEPARATOR . $classFile))
                    require_once($classFile);
            }
            self::run();
        }
        else
        {
            return false;
        }
    }
    
    private static function run()
    {
        foreach(self::$_class as $className => $classParams)
        {
            if(is_array($classParams))
                self::$app[$className] = new $className($classParams);
            else
                self::$app[$className] = new $className;
        }
        return true;
    }
    
    public static function getClass($className)
    {
        if(isset(self::$app[$className]))
            return self::$app[$className];
        else
            return new $className(self::$_class[$className]);
    }
    
    public static function getConfig($key = '')
    {
        if(!empty($key) && $key != '')
            return self::$config[$key];
        else
            return self::$config;
    }
    
    public static function getRequest($key = '')
    {
        if(!empty($key) && $key !='')
            return self::$request[$key];
        else
            return self::$request;
    }
    
    public function getView($view_file, $params = array())
    {
        $file = 'view/'.$view_file.CLASS_EXT;
        if(file_exists($file))
        {
            extract($params,true);
            require_once $file;
        }
    }
}