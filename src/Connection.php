<?php

namespace Facile\DoctrineMySQLComeBack\Doctrine\DBAL;

class Connection extends \Doctrine\DBAL\Connection
{
    use ConnectionTrait;

    public function connect()
    {
        if ($this->_conn !== null) {
            return false;
        }

        try {
            $this->_conn = $this->_driver->connect($this->getParams());
        } catch (\Doctrine\DBAL\Driver\Exception $e) {
            throw $this->convertException($e);
        }

        if (!$this->isAutoCommit()) {
            $this->beginTransaction();
        }

        return true;
    }
}
