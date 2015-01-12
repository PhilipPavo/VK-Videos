<?Php
    class Database{
        public $db;
        public function __construct(){
            try {
                 $this-> db = new PDO(sprintf('mysql:host=%s; dbname=%s; port=3306', 'pavophilip.cloudapp.net', ''), '', '');
            } catch (PDOException $e) {
                die('Database connect error');
        	}
        }
        public function change_database($name){
             $this-> db = new PDO(sprintf('mysql:host=%s; dbname=%s; port=3306', 'pavophilip.cloudapp.net', $name), '', '');
        }
        public function insertUser($data){
            if($this->getRow('users', 'login', $data['login'])) return false;
            $sessions = serialize(array());
            $query = $this->db->prepare("INSERT INTO users (login, password_hash, email, sessions, access_level) VALUES(:login, :password_hash, '', :sessions, :access_level)");
            $query->bindParam(':login', $data['login']);
            $query->bindParam(':password_hash', $data['password_hash']);
            $query->bindParam(':sessions', $sessions);
            $query->bindParam(':access_level', 5);
            $query->execute();
            return $this->is_success();
        }
        public function insertRow($table, $data){
            $data_prep = implode(',', array_keys($data));
            $values_prep = array();
            $values =array();
            foreach ($data as $key => $value) {
                $values_prep[]= '?';
                $values[] = $value;
            }
            $values_prep = implode(',', $values_prep);
            $query = $this->db->prepare("INSERT INTO $table ($data_prep) VALUES($values_prep)");
            $query->execute($values);
            return $this->is_success();
        }
        public function getRow($table, $key, $value){
            $query = $this->db->prepare("SELECT * FROM $table WHERE LOWER($key)=LOWER(:value) LIMIT 1");
            $query->bindValue(':value', $value, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
           // $query ->debugDumpParams();
            return $row;
        }
        public function getRows($table, $key= false, $value= false, $offset= false, $limit = 100){
              $query = $this->db->prepare("SELECT * FROM $table ".($key ? " WHERE LOWER($key)=LOWER(:value)" : ""). "LIMIT $limit");
              $query->bindValue(':value', $value);
              $query->execute();
              $rows = $query->fetchAll(PDO::FETCH_ASSOC);
              return $rows;
        }
        public function updateRow($table, $row_key, $data){
            $data_prep = array();
            $values =array();
            $row_key_prep;
            $row_key_val;
            foreach ($data as $key => $value) {
                $data_prep[] = "`$key` = ?";
                $values[] = $value;
            }
            $data_prep = implode(',', $data_prep);
            $key = key($row_key);
            $values[] = $row_key[$key];
            $query = $this->db->prepare("UPDATE $table SET $data_prep WHERE LOWER($key)=?");
            $query->execute($values);
            return $this->is_success();
        }
        public function is_success(){
            if($this->db || $this->db->errorCode() == 0000) return true;
            else return false ;
        }
        function __destruct() {
           $this-> db = null;
        }
    }
?>
