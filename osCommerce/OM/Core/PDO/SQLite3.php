<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2019 osCommerce; https://www.oscommerce.com
 * @license MIT; https://www.oscommerce.com/license/mit.txt
 */

namespace osCommerce\OM\Core\PDO;

class SQLite3 extends \osCommerce\OM\Core\PDO
{
    public function __construct(string $server, ?string $username, ?string $password, ?string $database, ?int $port, array $driver_options)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
        $this->driver_options = $driver_options;
    }

    public function connect()
    {
        $dsn = 'sqlite:' . $this->server;

        $this->instance = new \PDO($dsn, $this->username, $this->password, $this->driver_options);
    }
}
