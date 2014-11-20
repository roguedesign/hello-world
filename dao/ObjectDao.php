<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FlightBookingDao
 *
 * @author emmett.newman
 */
class Dao {

    private $db = null;

    public function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $config = Config::getConfig("db");
        try {
            $this->db = new PDO($config['dsn'], $config['username'], $config['password']);
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $this->db;
    }

    public function insert($object) {
        $now = new DateTime;
        $object->setId(null);
        $sql = 'INSERT INTO 
                VALUES();';
        
        return $this->execute($sql, $object);
    }

    public function update($object){
        $sql = '
            UPDATE 
            SET   
            WHERE';
               
        return $this->execute($sql, $object);
    }
    public function save($object){
        if ($object->getId() === null){
            return $this->insert($object);
        }
        return $this->update($object);

    }

    private function getParams($object) {
        $params = [
            ':id' => $object->getId(null),
            
        ];
        
        return $params;
    }

    private function execute($sql, $object) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($object));
        if (!$object->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        if (!$statement->rowCount()) {
            throw new NotFoundException('Object with ID "' . $object->getId() . '" does not exist.');
        }
        return $object;
    }

    private function executeStatement(PDOStatement $statement, array $params) {
        if (!$statement->execute($params)) {
            self::throwDbError($this->getDb()->errorInfo());
        }
    }

    public function findById($id) {
        $row = $this->query('
                SELECT * 
                FROM 
                WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $object = new FlightBooking();
        Mapper::map($object, $row);
        return $object;
    }

    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        return $statement;
    }

    public function delete($id) {
        $sql = '
            UPDATE 
            SET
            WHERE id = :id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            
        ));
        return $statement->rowCount() == 1;
    }

    public function __destruct() {
        $this->db = null;
    }
    
    private static function throwDbError(array $errorInfo) {
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }
    
    private static function formatDateTime(DateTime $dateTime) {
        $dateTime->format(DateTime::ISO8601);
        return $dateTime;
    }

}
