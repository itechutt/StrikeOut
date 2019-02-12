<?php
    require_once("connection.php");
    class Person{
        private $id;
        private $firstName;
        private $lastName;
        private $photo;

        #getters&setters
        public function getId(){return $this->id;}
        public function setId($id){$this->id = $id;}
        public function getFirstName(){return $this->firstName;}
        public function setFirstName($firstName){$this->firstName = $firstName;}
        public function getLastName(){return $this->lastName;}
        public function setLastName($lastName){$this->lastName = $lastName;}
        public function getPhoto(){return $this->photo;}
        public function setPhoto($photo){$this->photo = $photo;}
        
        #constructor
        public function __construct(){
            if(func_num_args() == 0){
                $this->id = "";
                $this->firstName = "";
                $this->lastName = "";
                $this->photo = "";
            }
            if(func_num_args() == 1){
                $connection=MySqlConnection::getConnection();
                $query='select Id, FirstName, LastName, Photo from persons where perId = ?';
                $command=$connection->prepare($query);
                // se queja el interprete de php "solo variables"
                $id = func_get_arg(0);
                $command->bind_param('i', $id);
                $command->execute();
                $command->bind_result($id,$firstName,$lastName);
                if($command->fetch())
                {
                    $this->id=$id;
                    $this->firstName=$firstName;
                    $this->lastName=$lastName;
                }
            }
            if(func_num_args() == 4){
                $this->id = func_get_arg(0);
                $this->firstName = func_get_arg(1);
                $this->lastName = func_get_arg(2);
                $this->photo = func_get_arg(3);
            }
        }
        #methods
        public function add(){
            $connection=MySqlConnection::getConnection();
			#query
			$statement='insert into persons (FirstName,LastName, Photo) VALUES (?,?,?)';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('sss',
                                      $this->firstName,
                                      $this->lastName,
                                      $this->photo
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        public function edit(){
            $connection=MySqlConnection::getConnection();
			#query
			$statement= 'update persons set perFirstName = ?, perLastName = ? where perId = ?';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('ssi',
                                      $this->firstName,
                                      $this->lastName,
                                      $this->id
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        public function delete(){
            $connection=MySqlConnection::getConnection();
			#query
			$statement= 'delete from persons where perId = ?';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('i',
                                      $this->id
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        public function toJson(){
            return json_encode(array(
                'id' => $this->id,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'photo' => $this->photo
            ));
        }
    }
?>
