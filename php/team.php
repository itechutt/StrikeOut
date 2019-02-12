<?php

require_once('category.php');
require_once('season.php');
require_once('coach.php');
require_once('connection.php');
require_once('player.php');
require_once('exceptions/recordnotfoundexception.php');

class Team {

    private $id;
    private $name; 
    private $category; 
    private $coach; 
    private $status;
    private $image;
    private $season;

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getCategory() { return $this->category; }
    public function setCategory($category) { $this->category = $category; }

    public function getCoach() { return $this->coach; }
    public function setCoach($coach) { $this->coach = $coach; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    public function getImage() { return $this->image; }
    public function setImage($image) { $this->image = $image; }

    public function getSeason() { return $this->season; }
    public function setSeason($season) { $this->season = $season; }

    public function __construct() {
        if(func_num_args() == 0) {
            $this->id = 0;
            $this->name = "";
            $this->category = new Category();
            $this->coach = new Coach();
            $this->status = 1;
            $this->image = "";
            $this->season = new Season();
        }

        if(func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'select teaId, staId, teaName, teaImage, catId, coaId, seaId from teams where teaId = ?';
            $command = $connection->prepare($query);
            $idTemp = func_get_arg(0);
            $command->bind_param('i', $idTemp);
            $command->execute();
            $command->bind_result($id, $status, $name, $image, $category, $coach, $season);
            if($command->fetch()) {
                $this->id = $id;
                $this->name = $name;
                $this->image = $image;
                $this->category = new Category($category);
                $this->coach = new Coach($coach);
                $this->season = new Season($season);
                $this->status = $status;
            } 
            else 
            {
                throw new RecordNotFoundException(func_get_arg(0));
            }
            
            mysqli_stmt_close($command);
            $connection->close();
        }

        if(func_num_args() == 7) {
            $this->id = func_get_arg(0);
            $this->status = func_get_arg(1);
            $this->name = func_get_arg(2);
            $this->category = func_get_arg(3);
            $this->coach = func_get_arg(4);
            $this->image = func_get_arg(5);
            $this->season = func_get_arg(6);
        }
    }

    public function getPlayers()
    {
        $teamPlayers = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select Id, players.perId, staId, teaId, Nickname, FirstName, LastName, BirthDate, Debut, Image, Num from players inner join persons  on players.perId = persons.Id where teaId = ?';
        $command = $connection->prepare($query);
        $teamId = $this->id;
        $command->bind_param('i', $teamId);
        $command->execute();
        $command->bind_result($id, $person, $status, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number);
        while($command->fetch())
        {
            array_push($teamPlayers, new Player($id, new Person($person), $status, new Team($team), $nickname, $birthdate, $debut, $image, $number));
        }
        mysqli_stmt_close($command);
        $connection->close();
        $playersJson = array();

        foreach($teamPlayers as $value)
        {
            array_push($playersJson, json_decode($value->toJson()));
        }

        return json_encode($playersJson);
    }

    public static function getAll()
    {
        $allTeams = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select Id, staId, teaName, teaImage, catId, coaId, seaId from teams';
        $command = $connection->prepare($query);
        $command->execute();
        $command->bind_result($id, $status, $name, $image, $category, $coach, $season);
        while($command->fetch())
        {
            array_push($allTeams, new Team($id, $status, $name, new Category($category), new Coach($coach), $image, new Season($season)));
        }

        mysqli_stmt_close($command);
        $connection->close();

        return $allTeams;
    }

    public static function getAllToJson()
    {
        $teamsJson = array();
        $teams = self::getAll();

        foreach ($teams as $value) {
            array_push($teamsJson, json_decode($value->toJson()));
        }

        return json_encode(array(
            'teams' => $teamsJson
        ));

	return $teamsJson;
    }

    public function getPlayersToJson()
    {
        $playersJson = array();
        $players = self::getPlayers();

        foreach($players as $value)
        {
            array_push($playersJson, json_decode($value->toJson()));
        }

        return json_encode($playersJson);
    }

    /* public static function getFullToJson() */
    /* { */
        /* $players = array(); */
        /* $jsonTemp = $this->getPlayersToJson(); */

        /* foreach($jsonTemp as $player) */
        /* { */
            /* array_push($players, json_decode($player->toJson())); */
        /* } */

/*         return json_encode( */
            /* array( */
            /* 'id' => $this->id, */
            /* 'name' => $this->name, */
            /* /1* 'category' => json_decode($this->category->toJson()), *1/ */
            /* 'coach' => json_decode($this->coach->toJson()), */
            /* 'players' => json_decode($this->getTeamPlayersToJson()))); */
    /* /1* } *1/ */

    public function toJson() {
        return json_encode(array(
            'id'=>$this->id,
            'status' => $this->status,
            'name'=>$this->name,
            'category'=>json_decode($this->category->toJson()),
            'coach' => json_decode($this->coach->toJson()),
            'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/baseball_project/src/photos/' . $this->image,
            'season' => json_decode($this->season->toJson())
        ));
    }

    public function delete()
    {
        // delete team 
        $connection = MySqlConnection::getConnection(); 
        $statement = 'delete from teams where Id = ?';    
        $command = $connection->prepare($statement);
        $id = $this->id;
        $command->bind_param('i', $id);
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public function edit()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'update teams set staId = ?, Name = ?, Image = ?, catId = ?, coaId = ?, seaId = ? where Id = ?';
        $command = $connection->prepare($statement);
        $id = $this->id;
        $status = $this->status;
        $name = $this->name;
        $image = $this->image;
        $categoryId = $this->category->getId();
        $coachId = $this->coach->getId();
        $seasonId = $this->season->getId();
        $command->bind_param('issiiii',$status, $name, $image, $categoryId, $coachId, $seasonId, $id);
        $result = $command->execute();
        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public function add()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'insert into teams(staId, Name, Image, catId, coaId, seaId) values(?, ?, ?, ?, ?, ?)';
        $command = $connection->prepare($statement);
        $status = $this->status;
        $name = $this->name;
        $categoryId = $this->category->getId();
        $coachId = $this->coach->getId();
        $image = $this->image;
        $seasonId = $this->season->getId();
        $command->bind_param('issiii',$status, $name, $image, $categoryId, $coachId, $seasonId);
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }
}
?>
