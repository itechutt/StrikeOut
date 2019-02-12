<?php

require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

    class DisplayMatches
    {

        //Atributte
        private $matId;
        private $dmtEntry;
        private $dmtBatter;
        private $dmtBalls;
        private $dmtStrikes;
        private $dmtOuts;
        private $dmtRunsHomeTeam;
        private $dmtRunsGuestTeam;
        private $dmtBase1;
        private $dmtBase2;
        private $dmtBase3;

        //getters and setters

        //MatId
        public function getMatId() { return $this->matId;}
        public function setMatId($matId) { $this->matId = $matId;}

        //dmtEntry
        public function getDmtEntry() { return $this->dmtEntry;}
        public function setDmtEntry($dmtEntry) { $this->dmtEntry = $dmtEntry;}

        //dmtBatter
        public function getDmtBatter() { return $this->dmtBatter;}
        public function setDmtBatter($dmtBatter) { $this->dmtBatter = $dmtBatter;}

        //dmtBalls
        public function getDmtBalls() { return $this->dmtBalls;}
        public function setDmtBalls($dmtBalls) { $this->dmtBalls = $dmtBalls;}

        //dmtStrikes
        public function getDmtStrikes() { return $this->dmtStrikes;}
        public function setDmtStrikes($dmtStrikes) { $this->dmtStrikes = $dmtStrikes;}

        //dmtOuts
        public function getDmtOuts() { return $this->dmtOuts;}
        public function setDmtOuts($dmtOuts) { $this->dmtOuts = $dmtOuts;}

        //dmtRunsHomeTeam
        public function getDmtRunsHomeTeam() { return $this->dmtRunsHomeTeam;}
        public function setDmtRunsHomeTeam($dmtRunsHomeTeam) { $this->dmtRunsHomeTeam = $dmtRunsHomeTeam;}

        //dmtRunsGuestTeam
        public function getDmtRunsGuestTeam() { return $this->dmtRunsGuestTeam;}
        public function setDmtRunsGuestTeam($dmtRunsGuestTeam) { $this->dmtRunsGuestTeam = $dmtRunsGuestTeam;}

        //dmtBase1
        public function getDmtBase1() { return $this->dmtBase1;}
        public function setDmtBase1($dmtBase1) { $this->dmtBase1 = $dmtBase1; }

        //dmtBase2
        public function getDmtBase2() { return $this->dmtBase2;}
        public function setDmtBase2($dmtBase2) { $this->dmtBase2 = $dmtBase2;}

        //dmtBase3
        public function getDmtBase3() { return $this->dmtBase3;}
        public function setDmtBase3($dmtBase3) { $this->dmtBase3 = $dmtBase3;}

        //Constructors
        public function __construct()
        {
            //receive 0 arguments
            if(func_num_args() == 0)
            {
                $this->matId= 0;
                $this->dmtEntry = 0;
                $this->dmtBatter = 0;
                $this->dmtBalls = 0;
                $this->dmtStrikes = 0;
                $this->dmtOuts = 0;
                $this->dmtRunsHomeTeam = 0;
                $this->dmtRunsGuestTeam = 0;
                $this->dmtBase1 = 0;
                $this->dmtBase2 = 0;
                $this->dmtBase3 = 0;
            }

           
            if(func_num_args()==1)
            {
                $connection=MySqlConnection::getConnection();
                $query='select matId, dmtEntry, dmtBatter, dmtBalls, dmtStrikes, dmtOuts, dmtRunsHomeTeam,dmtRunsGuestTeam,dmtBase1,
                dmtBase2, dmtBase3 from displaymatches where matId = ?';
                $command=$connection->prepare($query);
                $command->bind_param('i',func_get_arg(0));
                $command->execute();
                $command->bind_result($matId,$dmtEntry,$dmtBatter,$dmtBalls,$dmtStrikes,$dmtOuts,$dmtRunsHomeTeam,$dmtRunsGuestTeam,$dmtBase1,$dmtBase2,$dmtBase3);
                if($command->fetch())
                {
                    $this->matId= $matId;
                    $this->dmtEntry=$dmtEntry;
                    $this->dmtBatter=$dmtBatter;
                    $this->dmtBalls=$dmtStrikes;
                    $this->dmtOuts=$dmtOuts;
                    $this->dmtRunsHomeTeam=$dmtRunsHomeTeam;
                    $this->dmtRunsGuestTeam =$dmtRunsGuestTeam;
                    $this->dmtBase1=$dmtBase1;
                    $this->dmtBase2=$dmtBase2;
                    $this->dmtBase3=$dmtBase3;
                }
                else
                    throw new RecordNotFoundException(func_get_arg(0));
                    mysqli_stmt_close($command);
                    //close connection
                    $connection->close();				
            }
            
            // 11 arguments
            if(func_num_args() == 11)
            {
                //empty object
                $this->matId= func_get_arg(0);
                $this->dmtEntry = func_get_arg(1);
                $this->dmtBatter = func_get_arg(2);
                $this->dmtBalls = func_get_arg(3);
                $this->dmtStrikes = func_get_arg(4);
                $this->dmtOuts = func_get_arg(5);
                $this->dmtRunsHomeTeam = func_get_arg(6);
                $this->dmtRunsGuestTeam = func_get_arg(7);
                $this->dmtBase1 = func_get_arg(8);
                $this->dmtBase2 = func_get_arg(9);
                $this->dmtBase3 = func_get_arg(10);

            }
        }

         //instancia de la clase toJson
        public function toJson()
        {
            return json_encode(array(
                'matId' => $this->matId,
                'dmtEntry' => $this->dmtEntry,
                'dmtBatter' => $this->dmtBatter,
                'dmtBalls' => $this->dmtBalls,
                'dmtStrikes' => $this->dmtStrikes,
                'dmtOuts' => $this->dmtOuts,
                'dmtRunsHomeTeam' => $this->dmtRunsHomeTeam,
                'dmtRunsGuestTeam' => $this->dmtRunsGuestTeam,
                'dmtBase1' => $this->dmtBase1,
                'dmtBase2' => $this->dmtBase2,
                'dmtBase3' => $this->dmtBase3
                ));
        }

    
        //class methods
        public static function getAll()
        {
			$list =array();
			//get connection
			$connection=MySqlConnection::getConnection();
			//query
            $query='select matId, dmtEntry, dmtBatter, dmtBalls, dmtStrikes, dmtOuts, dmtRunsHomeTeam,dmtRunsGuestTeam,dmtBase1,
            dmtBase2, dmtBase3 from displaymatches';
			//prepare statement
			$command=$connection->prepare($query);
			//execute
			$command->execute();
			//blind results
			$command->bind_result($matId,$dmtEntry,$dmtBatter,$dmtBalls,$dmtStrikes,$dmtOuts,$dmtRunsHomeTeam,$dmtRunsGuestTeam,$dmtBase1,$dmtBase2,$dmtBase3);
			//fetch data
			while($command ->fetch()){
				array_push($list,new DisplayMatches($matId,$dmtEntry,$dmtBatter,$dmtBalls,$dmtStrikes,$dmtOuts,$dmtRunsHomeTeam,$dmtRunsGuestTeam,$dmtBase1,$dmtBase2,$dmtBase3));
			}
			//add items to array
			return $list;   
        }


        
               
        public static function getAllToJson()
        {
            $jsonUser = array(); //array

            foreach(self::getAll() as $item)
            {
                array_push($jsonUser, json_decode($item->toJson()));
            }
            return json_encode ($jsonUser); //return array
        }

      /*  
        public function toJsonFull()
        {
            //add Matches
            $Matches = array();
            foreach($this->getMatches() as $item)
            {
                array_push($Matches, json_decode($item->toJson()));
            }

            return json_encode(array(
                'matId' => $this->matId,
                'dmtEntry' => $this->dmtEntry,
                'dmtBatter' => $this->dmtBatter,
                'dmtBalls' => $this->dmtBalls,
                'dmtStrikes' => $this->dmtStrikes,
                'dmtOuts' => $this->dmtOuts,
                'dmtRunsHomeTeam' => $this->dmtRunsHomeTeam,
                'dmtRunsGuestTeam' => $this->dmtRunsGuestTeam,
                'dmtBase1' => $this->dmtBase1,
                'dmtBase2' => $this->dmtBase2,
                'dmtBase3' => $this->dmtBase3,
                'Matches' => $Matches
            ));
        }
        */
        

        //add 
        public function add() {
            //get connection
			$connection = MySqlConnection::getConnection();
			//statement
            $statement = 'insert into displaymatches (matId,dmtEntry, dmtBatter, dmtBalls, dmtStrikes, dmtOuts, dmtRunsHomeTeam,dmtRunsGuestTeam,dmtBase1,
            dmtBase2, dmtBase3) values (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
			//prepare statement
            $command = $connection->prepare($statement);
            //bind params
            $command->bind_param('iiiiiiiiiii',$this->matId,$this->dmtEntry, $this->dmtBatter, $this->dmtBalls, $this->dmtStrikes, $this->dmtOuts, $this->dmtRunsHomeTeam, $this->dmtRunsGuestTeam, $this->dmtBase1, $this->dmtBase2, $this->dmtBase3);
			//execute
            $result = $command->execute();
            //close command
            mysqli_stmt_close($command);
            //close connection
            $connection->close();
            //return result
            return $result;
        }

        //Apdate
        public function updateDispMatch()
        {
            
        	$connection = MySqlConnection::getConnection();
            $query = "update displaymatches set dmtEntry = ?, dmtBatter = ?, 
            dmtBalls = ?, dmtStrikes = ?, dmtOuts = ?, dmtRunsHomeTeam = ?, 
            dmtRunsGuestTeam = ?, dmtBase1 = ?, dmtBase2 = ?, dmtBase3 = ? 
            where matId = ?";
        	$command = $connection->prepare($query);
            $command->bind_param("iiiiiiiiiii", $this->dmtEntry, $this->dmtBatter, 
            $this->dmtBalls, $this->dmtStrikes, $this->dmtOuts, 
            $this->dmtRunsHomeTeam, $this->dmtRunsGuestTeam, 
            $this->dmtBase1, $this->dmtBase2, $this->dmtBase3, 
            $this->matId);
            $result = $command->execute();
            // var_dump($connection);
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }

        //delete
        public function delete()
        {
            // delete displayMatches then
            $connection = MySqlConnection::getConnection(); 
            $statement = 'delete from displaymatches where matId= ?';    
            $command = $connection->prepare($statement);
            $matId = $this->matId;
            $command->bind_param('i', $matId);
            $result = $command->execute();
            mysqli_stmt_close($command);
            $connection->close();
            return $result;
        }
        

        
        //Matches
        public function getMatches() {
            $list = array(); //create list
            //get connection
			$connection = MySqlConnection::getConnection();
			//query
            $query = 'select m.matId, m.seald, m.matHomeTeam, m.matGuestTeam, m.matField, m.StartTime, m.matEndTime, m.matRunsHomeTeam, m.matRunsGuestTeam, 
            d.matId, d.dmtEntry, d.dmtBatter,d.dmtBalls, d.dmtStrikes, d.dmtOuts, d.dmtRunsHomeTeam, d.dmtRunsGuestTeam, d.dmtBase1, d.dmtBase2, d.dmtBase3,
            from matches as m
            join displaymatches as d on m.matId = d.matId
            where m.matId = ?';
			//prepare statement
            $command = $connection->prepare($query);
            //bind params
            $command->bind_param('i', $this->id);
			//execute
			$command->execute();
			//bind results
			$command->bind_result($matId,$seald,$matHomeTeam,$matGuestTeam,$matField,$StartTime,$matEndTime,$matRunsHomeTeam,$matRunsGuestTeam,$matId,$dtmEntry,$dmtBatter,$dmtBalls,$dmtStrikes,$dmtOuts,$dmtRunsHomeTeam,$dmtRunsGuestTeam,$dmtBase1,$dmtBase2,$dmtBase3);
			//fetch data
			while ($command->fetch()) {
                //add contact to list
                $pnt = new Match($matId,$seald,$matHomeTeam,$matGuestTeam,$matField,$StartTime,$matEndTime,$matRunsHomeTeam,$matRunsGuestTeam);
				array_push($list, new DisplayMatches($matId,$pnt,$dtmEntry,$dmtBatter,$dmtBalls,$dmtStrikes,$dmtOuts,$dmtRunsHomeTeam,$dmtRunsGuestTeam,$dmtBase1,$dmtBase2,$dmtBase3));
            }
            //close command
            mysqli_stmt_close($command);
            //close connection
            $connection->close();
            //return list
            return $list; 
        }
        
        
    }

?>