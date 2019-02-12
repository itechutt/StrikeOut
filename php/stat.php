<?php 
//Stats player
require_once('connection.php');
require_once('player.php');
class Stat{
    //ATTRIBUTES
    private $id;
    private $player;
    private $run;
    private $playedGames;
    private $hit;
    private $strike;
    private $balls;
    private $stolenBase;
    private $homeRuns;
    private $out;
    private $win;
    private $lose;


    //PROPERTIES

    //GETTERS
    public function getId(){return $this->id;}
    public function getRun(){return $this->run;}
    public function getPlayer(){return $this->player;}
    public function getPlayedGames(){return $this->playedGames;}
    public function getHit(){return $this->hit;}
    public function getStrike(){return $this->strike;}
    public function getballs(){return $this->balls;}
    public function getStolenBase(){return $this->stolenBase;}
    public function getHomeRuns(){return $this->homeRuns;}
    public function getOut(){return $this->out;}
    public function getAcurrity(){
        if($this->hit > 0 && $this->strike >0){
        $acu= ($this->hit/($this->strike + $this->hit) )*100;
        }else{
        $acu =0;
        }
        return $acu."%";
    }
    public function getWin(){return $this->win;}
    public function getLose(){return $this->lose;}
    public function getWinRate(){
        $rate=0;
        if($this->win > 0){
            $rate= $this->win/$this->playedGames *100;
        }
        return $rate."%";
    }

    //SETTERS
    public function setPlayedGames($playedGames){$this->playedGames = $playedGames;}
    public function setPlayer($player){$this->player = $player;}
    public function setRun($run){$this->run = $run;}
    public function setHit($hit){$this->hit = $hit;}
    public function setStrike($strike){$this->strike = $strike;}
    public function setBalls($balls){$this->balls = $balls;}
    public function setStolenBase($stolenBase){$this->stolenBase = $stolenBase;}
    public function setHomeRuns($homeRuns){$this->homeRuns = $homeRuns;}
    public function setOut($out){$this->out = $out;}
    public function setWin($win){$this->win = $win;}
    public function setLose($lose){$this->lose = $lose;}
    

    //CONSTRUCTS
    public function __construct(){
        //when recives zero arguments
        if(func_num_args()==0){
            $this->id = 0;
            $this->hit = 0;
            $this->run = 0;
            $this->player = new Player();
            $this->strike = 0;
            $this->balls = 0;
            $this->stolenBase = 0;
            $this->homeRuns = 0;
            $this->out = 0;
            $this->win = 0;
            $this->lose = 0;
           $this->playedGames = 0;
        }

        //1 argument received : gets data from database
        if (func_num_args() == 1) {
            //get connection
            $connection = MySqlConnection::getConnection();
            //query
            $query = 'SELECT Id,plaId,Hits,Strikes,Runs,Wins,Loses,HomeRuns,StolenBases,Outs,Balls,PlayedGames
             FROM `playerstats` WHERE plaId = ?';
            //prepare statement
            $command = $connection->prepare($query);
            //parameters
            $id = func_get_arg(0);
            $command->bind_param('i',$id );
            //execute
            $command->execute();
            //bind results
            $command->bind_result($id,$player, $hit, $strike,$run,$win,$lose,$homeRuns,$stolenBase,$out,$balls,$playedGames);
            //record was found
            if ($command->fetch()) {
                //pass values to the attributes
                $this->id = $id;
                $this->run = $run;
                $this->hit = $hit;
                $this->player = new Player($player);
                $this->strike = $strike;
                $this->balls = $balls;
                $this->stolenBase = $stolenBase;
                $this->homeRuns = $homeRuns;
                $this->out = $out;
                $this->win = $win;
                $this->lose = $lose;
                $this->playedGames = $playedGames;
            }

            
            
            //close command
            mysqli_stmt_close($command);
            //close connection
            $connection->close();
       }

        //when revices all arguments
        if(func_num_args()==12){
            $this->id = func_get_arg(0);
            $this->playedGames = func_get_arg(1);
            $this->hit = func_get_arg(2);
            $this->player = func_get_arg(3);
            $this->strike = func_get_arg(4);
            $this->balls = func_get_arg(5);
            $this->stolenBase = func_get_arg(6);
            $this->homeRuns = func_get_arg(7);
            $this->out = func_get_arg(8);
            $this->win = func_get_arg(9);
            $this->lose = func_get_arg(10);
            $this->run = func_get_arg(11);
            //$id, $playedGames, $hit,$player,$strike,$balls,$stolenBase,$homeRuns,$out,$win,$lose
        }
    }

    public function toJson(){
        return json_encode(array(
            'id'=> $this->id,
            'firstName'=> $this->player->getPerson()->getFirstName(),
            'lastName' => $this->player->getPerson()->getLastName(),
            'runs' => $this->run,
            'played games'=> $this->playedGames,
            'hits'=>$this->hit,
            'strikes'=>$this->strike,
            'balls'=>$this->balls,
            'stolen bases'=>$this->stolenBase,
            'home runs'=>$this->homeRuns,
            'outs'=>$this->out,
            'wins'=>$this->win,
            'loses'=>$this->lose,
            'acurrity'=> $this->getAcurrity(),
            'WinRate'=> $this->getWinRate()
        ));
    }


    //Instance methods
    public function add(){
    //get connection
    $connection = MySqlConnection::getConnection();
    //statement
    $statement = 'insert into playerStats (plaId,Hits,Strikes,Runs,Wins,Loses,HomeRuns,StolenBases,Outs,Balls,PlayedGames)
    values (?,?,?,?,?,?,?,?,?,?,?)';
    //prepare statement
    $command = $connection->prepare($statement);
    //bind params
    echo "values......".$this->playedGames;
    $command->bind_param('iiiiiiiiiii', $this->player, $this->hit,$this->strike,$this->run,$this->win,$this->lose,
    $this->homeRuns,$this->stolenBase,$this->out,$this->balls,$this->playedGames);
    //execute
    $result = $command->execute();
    //close command
    mysqli_stmt_close($command);
    //close connection
    $connection->close();
    //return result
    return $result;

    }

    public function Update(){
        //get connection
        $connection = MySqlConnection::getConnection();
        //statement
        $statement = 'update playerStats  set Hits=?,Strikes=?,Runs=?,Wins=?,Loses=?,HomeRuns=?,StolenBases=?,Outs=?,Balls=?,PlayedGames=? where plaId=?';
        //prepare statement
        $command = $connection->prepare($statement);
        //bind params
        $command->bind_param('iiiiiiiiiii', $this->hit,$this->strike,$this->run,$this->win,$this->lose,
        $this->homeRuns,$this->stolenBase,$this->out,$this->balls,$this->playedGames, $this->player);
        //execute
        $result = $command->execute();
        //close command
        mysqli_stmt_close($command);
        //close connection
        $connection->close();
        //return result
        return $result;
    
        }


    //METHODS

    

    public static function getAll(){
        $list = array(); //create list
        //get connection
        $connection = MySqlConnection::getConnection();
        //query
        $query = 'SELECT Id,plaId,Hits,Strikes,Runs,Wins,Loses,HomeRuns,StolenBases,Outs,Balls,PlayedGames
        FROM playerstats';
        //prepare statement
        $command = $connection->prepare($query);

        $command->execute();
        //bind results
        $command->bind_result($id, $player,$hit, $strike,$run,$win,$lose,$homeRuns,$stolenBase,$out,$balls,$playedGames);
        //fetch data
        while ($command->fetch()) {
            //add contact to list
            array_push($list, new Stat($id, $playedGames, $hit,new Player($player),$strike,$balls,$stolenBase,$homeRuns,$out,$win,$lose,$run));
        }
        //close command
        mysqli_stmt_close($command);
        //close connection
        $connection->close();
        //return list
        return $list; 
    }


        public static function getAllToJson(){
            $jsonArray = array();//array
            foreach(self::getAll() as $item){
                array_push($jsonArray, json_decode($item->toJson()));
            }
            return json_encode($jsonArray);//return array
        }

}

?>