<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class DB
{

    /**
     * @var
     */
    private $db;

    /**
     * @var Capsule
     */
    private $capsule;

    /**
     * Connection name
     * @var string
     */
    private $name = 'default';

    /**
     * @var \Illuminate\Database\Connection
     */
    private $connection;

    public function __construct($driver, $hostname, $username, $password, $database, $port = NULL, $prefix = NULL, $name = 'default')
    {
        $this->name = $name;

        if (!in_array($driver, ['mysql', 'pgsql', 'sqlite', 'sqlsrv'])) {
            exit('Error: Could not load database driver ' . $driver . '!');
        }

//		$class = 'DB\\' . $driver;

//		if (class_exists($class)) {
//			$this->db = new $class($hostname, $username, $password, $database, $port);
//		} else {
//			exit('Error: Could not load database driver ' . $driver . '!');
//		}

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => $driver,
            'host' => $hostname,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => $prefix,
            'port' => $port
        ], $name);

        // Set the event dispatcher used by Eloquent models... (optional)
        $capsule->setEventDispatcher(new Dispatcher(new Container));

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();

        $this->capsule = $capsule;

        $this->connection = $this->capsule->schema($this->name)->getConnection();
    }

    public function query($sql)
    {
        if ($this->checkIsSelect($sql)) {
            $return = $this->connection->getPdo()->query($sql);

            $result = new \stdClass();
            $result->num_rows = $return->rowCount();
            $result->row = is_array($return->fetch()) ? $return->fetch() : [];
            $result->rows = $return->fetchAll();

            var_dump($sql);
            var_dump($return->fetchAll());

        } else {
            $result = $this->execute($sql);
        }

//        var_dump($sql);
//        var_dump($result);

        return $result;
//		return $this->db->query($sql);
    }

    public function execute($sql)
    {
        return $this->connection->statement($sql);
    }

    public function escape($value)
    {
        return $value;
//        return $this->capsule->schema($this->$this->name')->getConnection()->getPdo()->quote($value);
//        return $this->db->escape($value);
    }

    public function countAffected()
    {
//        return $this->connection;
//        return $this->db->countAffected();
    }

    public function getLastId()
    {
        return $this->connection->getPdo()->lastInsertId();
//        return $this->db->getLastId();
    }

    /**
     * Verifica se o sql é uma consulta ou uma execucao
     * @param $sql
     * @return bool true to select query
     */
    private function checkIsSelect($sql)
    {
        return (substr(strtoupper(trim($sql)), 0, 6) == 'SELECT');
    }
}
