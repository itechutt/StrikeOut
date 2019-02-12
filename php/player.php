<?php
    require_once('connection.php');
    require_once("person.php");
    require_once('team.php');
    require_once('exceptions/recordnotfoundexception.php');

    class Player
    {
        #attributes
        private $id;
        private $person;
        private $dateOfBirth;
        private $nickName;
        private $num;
        private $debut;
        private $teamId;
        private $status;

        #properties
        public function getId(){return $this->id;}
        public function setId($id){$this->id = $id;}
        
        public function getPerson(){return $this->person;}
        public function setPerson($person){$this->person = $person}

        public function getDateOfBirth(){return $this->dateOfBirth;}
        public function setDateOfBirth($dateOfBirth){$this->dateOfBirth = $dateOfBirth;}

        public function getNickName(){return $this->nickName;}
        public function setNickName($nickName){$this->nickName = $nickName;}

        public function getNum(){return $this->num;}
        public function setNum($num){$this->num = $num;}

        public function getDebut(){return $this->debut;}
        public function setDebut($debut){$this->debut = $debut;}

        public function getTeamId(){return $teamId;}
        public function setTeamId($teamId){$this->teamId = $teamId;}

        public function getStatus(){return $this->status;}
        public function setStatus($status){$this->status = $status;}
        #constructor
        public function __construct(){
            if(func_num_args() == 0){
                $this->id = "";
                $this->person = new Person();
                $this->status = "";
                $this->team = new Team();
                $this->nickName = "";
                $this->birthDate = "";
                $this->debut = "";
                $this->image = "";
                $this->playerNumber = "";
            }

            if(func_num_args() == 1)
            {
                $connection = MySqlConnection::getConnection();
                $query = "select * from players where Id = ?";
                $command = $connection->prepare($query);
                $x = func_get_arg(0);
                $command->bind_param('i',$x);
                $command->execute();
                $command->bind_result($id,$person, $status, $team, $nickName, $birthDate, $debut, $image, $playerNumber);
                if($command->fetch())
                {
                    $this->id = $id;
                    $this->person = new Person($person);
                    $this->status = $status;
                    $this->team = new Team($team);
                    $this->nickName = $nickName;
                    $this->birthDate = $birthDate;
                    $this->debut = $debut;
                    $this->image = $image;
                    $this->playerNumber = $playerNumber;
                }
                else 
                {
                    throw new RecordNotFoundException(func_get_arg(0));
                }
            }

            if(func_num_args() == 9){
                $this->id = func_get_arg(0);
                $this->person = func_get_arg(1);
                $this->status = func_get_arg(2);
                $this->team = func_get_arg(3);
                $this->nickName = func_get_arg(4);
                $this->birthDate = func_get_arg(5);
                $this->debut = func_get_arg(6);
                $this->image = func_get_arg(7);
                $this->playerNumber = func_get_arg(8);
            }
        }
        #methods
        public function toJson(){
            return json_encode(array(
                'id' => $this->id,
                'person'=>json_decode($this->person->toJson()),
                'status'=>$this->status,
                'team'=>$this->team,
                'team'=>json_decode($this->team->toJson()),
                'nickName'=>$this->nickName,
                'birthDate'=>$this->birthDate,
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/baseball_project/src/photos/' . $this->image,
                'debut'=>$this->debut,
                'number'=>$this->playerNumber
            ));
        }

        public function add()
        {
            $connection = MySqlConnection::getConnection();
            $query = "Insert Into players (perId, staId, teaId, plaNickname, plaBirthdate, plaDebut, plaImage, plaNumber) Values (?,?,?,?,?,?,?,?)"; 
            $command = $connection->prepare($query);
            $command->bind_param('iiissssi', $this->person, $this->status, $this->team, $this->nickName, $this->birthDate, $this->debut, $this->image, $this->playerNumber);
            $result = $command->execute();
            mysqli_stmt_close($command);
            $connection->close();
            return $result;
        }

        public function edit()
        {
            $connection = MySqlConnection::getConnection();
            $query = "update players set perId = ?, staId = ?, teaId = ?, plaNickname = ?, plaBirthdate = ?, plaDebut = ?, plaImage = ?, plaNumber = ?";
            $command = $connection->prepare($query);
            $command->bind_param('iiissssi', $this->person, $this->status, $this->team, $this->nickName, $this->birthDate, $this->debut, $this->image, $this->playerNumber);
            $result = $command->execute();
            mysqli_stmt_close($command);
            $connection->close();
            return $result;
        }

        public function delete()
        {
            $connection = MySqlConnection::getConnection();
            $query = 'delete from players where plaId = ?';
            $command = $connection->prepare($query);
            $command->bind_param('i',$this->id);
            $result = $command->execute();
            mysqli_stmt_close($command);
            $connection->close();
            return $result;
        }

        public static function getAll()
        {
            $list = array();
            $connection = MySqlConnection::getConnection();
            $query='select * from players';
            $command = $connection->prepare($query);
            $command->execute();
            $command->bind_result($id, $person, $status, $team, $nickName, $birthDate, $debut, $image, $playerNumber);
            while($command->fetch())
            {
                array_push($list, new Player($id,new Person($person),$status,new Team($team), $nickName, $birthDate, $debut, $image, $playerNumber));
            }
            return $list;
        }

        public static function getAllToJson()
        {
            $jsonArray = array();
            foreach(self::getAll() as $item)
            {
                array_push($jsonArray, json_decode($item->toJson()));
            }
            return json_encode($jsonArray);
        }
    }
?>
