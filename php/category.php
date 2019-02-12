<?php

require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

class Category
{

    private $id;
    private $name;

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function __construct() {
        if(func_num_args() == 0 ) {
        $this->id = 0;
        $this->name = "";
        }

        if(func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'select catId, catName from categories where catId = ?';
            $command = $connection->prepare($query);
            $id = func_get_arg(0);
            $command->bind_param('s', $id);
            $command->execute();
            $command->bind_result($id, $name);

        if($command->fetch()) {
            $this->id = $id;
            $this->name = $name;
        } 
        else 
            throw new RecordNotFoundException(func_get_arg(0));
        }

        if(func_num_args() == 2) {
            $this->id = func_get_arg(0);
            $this->name = func_get_arg(1);
        }
    }

    public function add()
    {
        $connection = MySqlConnection::getConnection(); 
        $statement = 'insert into categories (catName) values (?)';
        $command = $connection->prepare($statement);
        $name = $this->name;
        $command->bind_param('s', $name);
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public function delete()
    {
        // delete category then
        $connection = MySqlConnection::getConnection(); 
        $statement = 'delete from categories where catId = ?';    
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
        $statement = 'update categories set catName = ? where catId = ?';
        $command = $connection->prepare($statement);
        $id = $this->id;
        $name = $this->name;
        $command->bind_param('si', $name, $id); 
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public static function getAll()
    {
        $categories = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select catId, catName from categories';
        $command = $connection->prepare($query);
        $command->execute();
        $command->bind_result($id, $name);

        while ($command->fetch()) {
           array_push($categories, new Category($id, $name)); 
        }

	return $categories;
    }

    public static function getAllToJson()
    {
        $categoriesJson = array();
        $categories = self::getAll();

        foreach ($categories as $value) {
            array_push($categoriesJson, json_decode($value->toJson()));    
        }

        return json_encode(array(
            'categories' => $categoriesJson
        ));
    }

    public function toJson() {
        return json_encode (array(
        'id'=>$this->id,
        'name'=>$this->name,
        ));

    }
}
?>
