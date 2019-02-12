<?php
require_once('connection.php');
require_once('team.php');
require_once('player.php');
require_once('lineup.php');



require_once('exceptions/recordnotfoundexception.php');

class Match
{
    //Attributes
    private $id;
    private $category;
    private $homeTeam;
    private $guestTeam;
    private $field;
    private $startTime;
    private $endTime;
    private $runsHomeTeam;
    private $runsGuestTeam;

    //Getters and setters

    public function getId() {return $this->id;}
    public function setId($Id) { $this->id = $Id;}

    public function getCategory() {return $this->category;}
    public function setCategory($category) { $this->category = $category;}

    public function getHomeTeam() { return $this->homeTeam;}
    public function setHomeTeam($homeTeam) { $this->homeTeam = $homeTeam;}

    public function getGuestTeam() { return $this->guestTeam;}
    public function setGuestTeam($guestTeam) { $this->guestTeam = $guestTeam;}

    public function getField() { return $this->field;}
    public function setField($field) { $this->field = $field;}

    public function getstartTime() { return $this->startTime;}
    public function setstartTime($startTime) { $this->startTime = $startTime;}

    public function getEndTime() {return $this->endTime;}
    public function setEndTime($endTime) { $this->endTime = $endTime;}

    public function getRunsHomeTeam() { return $this->runsHomeTeam;}
    public function setRunsHomeTeam($runsHomeTeam) { $this->runsHomeTeam = $runsHomeTeam;}

    public function getRunsGuestTeam() { return $this->runsGuestTeam;}
    public function setRunsGuestTeam($runsGuestTeam) {$this->runsGuestTeam = $runsGuestTeam;}
    //Constructors

    public function __construct()
    {
        if(func_num_args() == 0)
        {
            $this->id = 0;
            $this->category = 0;
            $this->homeTeam = 0;
            $this->guestTeam = 0;
            $this->field = "";
            $this->startTime = "";
            $this->endTime = "";
            $this->runsHomeTeam = "";
            $this->runsGuestTeam = "";

        }

        if(func_num_args() == 1)
        {
        $connection = MySqlConnection::getConnection();
        $query = 'select matId,catId, matHomeTeam,matGuestTeam, matField, matStartTime, matEndTime, matRunsHomeTeam, matRunsGuestTeam
         from matches where  matId = ? ';
        $command = $connection->prepare($query);
        $idTemp = func_get_arg(0);
        $command->bind_param('i',$idTemp);
        $command->execute();
        $command->bind_result($id,$category,$homeTeam, $guestTeam, $field, $startTime, $endTime, $runsHomeTeam, $runsGuestTeam);
            if($command->fetch())
            {
               $this->id = $id;
               $this->category = $category;
               $this->homeTeam = $homeTeam;
               $this->guestTeam = $guestTeam;
               $this->field = $field;
               $this->startTime = $startTime;
               $this->endTime = $endTime;
               $this->runsHomeTeam = $runsHomeTeam;
               $this->runsGuestTeam = $runsGuestTeam;
            }
        }
        if(func_num_args() == 9)
        {
            $this->id = func_get_arg(0);
            $this->category = func_get_arg(1);
            $this->homeTeam = func_get_arg(2);
            $this->guestTeam = func_get_arg(3);
            $this->field = func_get_arg(4);
            $this->startTime = func_get_arg(5);
            $this->endTime = func_get_arg(6);
            $this->runsHomeTeam = func_get_arg(7);
            $this->runsGuestTeam = func_get_arg(8);
        }
    }

    //Methods

    public function toJson()
    {
        return json_encode(array(
            'id'=>$this->id,
            'category'=>$this->category,
            'homeTeam'=>$this->homeTeam,
            'guestTeam'=>$this->guestTeam,
            'field'=>$this->field,
            'startTime'=>$this->startTime,
            'endTime'=>$this->endTime,
            'runsHomeTeam'=>$this->runsHomeTeam,
            'runsGuestTeam'=>$this->runsGuestTeam,
        ));
    }

    public function toJsonFull(){
        
        //lineups
        $line = array();
        foreach($this->getLineups() as $item){
            array_push($line, json_decode($item->toJson()));
        }
        return json_encode(array(
            'id'=>$this->id,
            'category'=>$this->category,
            'homeTeam'=>$this->$line,
            'guestTeam'=>$this->guestTeam,
            'field'=>$this->field,
            'startTime'=>$this->startTime,
            'endTime'=>$this->endTime,
            'runsHomeTeam'=>$this->runsHomeTeam,
            'runsGuestTeam'=>$this->runsGuestTeam,
           
        ));
    }
    //add match
    public function add(){
        $list = array();
         $connection = MySqlConnection::getConnection();
         $statement='INSERT INTO matches (matId, catId, matHomeTeam, matGuestTeam, matField, matStartTime, matEndTime, matRunsHomeTeam, matRunsGuestTeam)
          VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
         $command = $connection->prepare($statement);
         $command->bind_param('iiiisssii', $this->id,$this->category,$this->homeTeam,$this->guestTeam,$this->field,$this->startTime,$this->endTime,$this->runsHomeTeam,$this->runsGuestTeam);
         $result= $command->execute();
        mysqli_stmt_close($command);
        $connection->close();
        return $result;
    }    

    public static function getAll(){
        $list = array(); 
        $connection = MySqlConnection::getConnection();
        $query='select matId,catId,matHomeTeam,matGuestTeam, matField, matStartTime, matEndTime, matRunsHomeTeam, matRunsGuestTeam 
        from matches ';
        $command = $connection->prepare($query);
        $command->execute();
        $command->bind_result($id,$category,$homeTeam,$guestTeam,$field,$startTime,$endTime,$runsHomeTeam,$runsGuestTeam);
        while($command->fetch()){
            array_push($list,new Match($id, $category,$homeTeam,$guestTeam,$field,$startTime,$endTime,$runsHomeTeam,$runsGuestTeam));
        }
        return $list; 

     }
     public function getLineups(){
        $list = array();
         //get connection
         $connection = MySqlConnection::getConnection();
         //query
         $query='select l.lupId,pl.plaId, l.teaId,lupBattingTurn,l.posId,m.catId,matHomeTeam,matGuestTeam,matField,
         matStartTime,matEndTime,matRunsHomeTeam,matRunsGuestTeam,plaImage, planumber ,perFirstName,perLastName
         from lineups l join matches m 
         on m.matId = l.matId
         join players pl on l.plaId = pl.plaId
         join persons p on pl.perId = p.perId
         where m.matHomeTeam = l.teaId and m.matId = ?';
         
        //prepare statement
        $command = $connection->prepare($query);
        //bind params
        $linId=$this->id;
        $command->bind_param('i', $linId);
        //execute
        $command->execute();
        //bind results
        $command->bind_result($liId, $plaId, $battingTurn, $liPos,$category,$homeTeam,$guestTeam,$matfield,$startTime,$endTime,$runsHomeTeam,$runsGuestTeam,$image,$number,$perId,$firstName,$lastName);
        //fetch data
        while ($command->fetch()) {
            //add contact to list
            array_push($list, new LineUp($liId, $battingTurn,$liPos), new Player($plaId,$image,$number),new Person($perId,$firstName,$lastName),$category,$matfield,$homeTeam,$guestTeam,$startTime,$endTime,$runsHomeTeam,$runsGuestTeam);
        }
        //close command
        mysqli_stmt_close($command);
        //close connection
        $connection->close();
        //return list
        return $list; 
    }
     
     public static function getAllToJson(){
            $jsonArray = array(); //array
            foreach(self::getAll() as $item){
                array_push($jsonArray, json_decode($item->toJson()));
            }
            return json_encode($jsonArray); //return array

         }
}

?>