<?php 

class Database {
    private $db,
            $lastInsertID = 0;
    public function __construct($username = "", $password = "", $host = "", $database = "", $exitOnError = true) {
        try {
            $this->db = new PDO('mysql:host='.$host.';dbname='.$database.';charset=utf8mb4', $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            if ($exitOnError)
                exit("Failed to connect to database!");
            else {
                $this->db = null;
                return false;
            }
        }
    }
    public function isMySQLConnected() {
        if (empty($this->db)) {
            return false;
        }
        return true;
    }
    public function getMysqlError($dbResult){
        if (isset($dbResult['message']))
            return $dbResult['message'];
        return "";
    }
    public function checkDBResult(&$dbResult) {
        if ($dbResult['error'])
            return false;
        $temp = $dbResult;
        $dbResult = $temp['rows'];
        return true;
    }
    public function query($sql, $exec, $all = false) {
        $return = $this->mysqlRequest($sql, $exec);
        if ($return['error'] == false) {
            $command = explode(" ", $sql);
            $command = strtolower($command[0]);
            if($command == "select") {
                if ($all)
                    $return['rows'] = $return['stmt']->fetchAll(PDO::FETCH_ASSOC);
                else
                    $return['rows'] = $return['stmt']->fetch(PDO::FETCH_ASSOC);
            }elseif($command == "insert") {
                $return['rows'] = $return['stmt']->rowCount();
            }elseif($command == "update") {
                $return['rows'] = $return['stmt']->rowCount();
            }elseif($command == "delete") {
                $return['rows'] = $return['stmt']->rowCount();
            }
        }
        return $return;
    }
    private function mysqlRequest($sql, $exec) {
        $return = array();
        if (empty($this->db)) {
            $return['error'] = true;
            $return['message'] = "MySQL NULL";
            return $return;
        }
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);
            $stmt->execute($exec);
            
            if (strtolower(substr($sql, 0, 6)) == "insert") {
                $this->lastInsertID = $this->db->lastInsertId();
            }
            
            $this->db->commit();
            $return['error'] = false;
            $return['stmt'] = $stmt;
        } catch(PDOException $ex) {
            $this->db->rollBack();
            $return['error'] = true;
            $return['message'] = $ex->getMessage();
        }
        return $return;
    }
    public function getInsertID() {
        return $this->lastInsertID;
    }
}

?>