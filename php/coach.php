<?php
    require_once("person.php");
    require_once("connection.php");
    class Coach{
        #attributes
        private $id;
        private $person;

        #getters&setters
        public function getId(){return $this->id;}
        public function setId($id){$this->id = $id;}
        public function getPerson(){return $this->person;}
        public function setPerson($person){$this->person = $person;}
        #constructor
        public function __construct(){
            if(func_num_args() == 0){
                $this->id = "";
                $this->person = new Person();
            }
            if(func_num_args() == 1){
                #get connection
                $connection = MySqlConnection::getConnection();
                #query
                $query = 'select Id, perId from coaches where Id = ?';
                #prepare statement
                $command = $connection->prepare($query);
                #id
                $id = func_get_arg(0);
                #params
                $command->bind_param('i',$id);
                #execute
                $command->execute();
                #bind params
                $command->bind_result($id, $person);
                if($command->fetch()){
                    $this->id = $id;
                    $this->person = new Person($person);
                    }
                else 
                {
                    throw new RecordNotFoundException(func_get_arg(0));
                }
            }
            
            if(func_num_args() == 2){
                $this->id = func_get_arg(0);
                $this->person = func_get_arg(1);
            }
        }
        #methods
        public function add($personId){
            $connection=MySqlConnection::getConnection();
			#query
			$statement='insert into coaches (perId) VALUES (?)';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('i', $personId
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        //No puse edit porque sólo es person y coa y no debería de poder cambiar ninguno de esos.
        public function delete(){
            $connection=MySqlConnection::getConnection();
			#query
			$statement='delete from coaches where coaId = ?';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('i', $this->id
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        public static function getAll(){
                $list = array();
                #get connection
                $connection = MySqlConnection::getConnection();
                #query
                $query = 'select c.Id, c.perId, p.FirstName, p.LastName from coaches c, persons p where p.Id = c.Id';
                #prepare statement
                $command = $connection->prepare($query);
                #execute
                $command->execute();
                #bind results
                $command->bind_result($id, $personId, $firstName, $lastName);
                #fetch data
                while ($command->fetch()){
                    #add contact to list
                    array_push($list, new Coach($id, new Person($personId)));
                }
                return $list;
        }
        public function toJson(){
            return json_encode(array(
                'id' => $this->id,
                'person'=>json_decode($this->person->toJson())
            ));
        }
        public static function getAllToJson(){
            $jsonArray=array();
                foreach(self::getAll() as $item){
                    array_push($jsonArray,json_decode($item->toJson()));
                }
                return json_encode($jsonArray);
        }
    }
?>
