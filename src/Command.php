<?php
namespace Puleeno\Imagoader;

use Commando\Command as Commando;

class Command
{
    protected static $instance;
    protected $command;

    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance  = new self();
        }
        return self::$instance;
    }


    public function __construct()
    {
        $this->command = new Commando();
    }

    public static function __callStatic($name, $args)
    {
        $cmd = self::getInstance();
        $command = $cmd->command;

        if (is_callable(array($command, $name))) {
            return call_user_func_array(array($command, $name), $args);
        }

        throw new \Exception(sprintf('Method %s::%s() is not defined', __CLASS__, $name));
    }

    public static function getCommand()
    {
        return self::getInstance()->command;
    }
}
