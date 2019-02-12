<?php
    class MySqlConnection
    {
        #get connection
        public static function getConnection(){
            #read config
            $data = file_get_contents('http://localhost/Sotos_Scorecard/config/connection.json');
            $config = json_decode($data, true);
            #parameters
            if(isset($config['server']))
                 $server = $config['server'];
            else{
                echo 'Configuration error: MySql Server name not found';
                die;
            }

            if(isset($config['user']))
                 $user = $config['user'];
            else{
                echo 'Configuration error: User name not found';
                die;
            }

            if(isset($config['password']))
                $pass = $config['password'];
            else{
                echo 'Configuration error: Incorrect password';
                die;
            }

            if(isset($config['database']))
                $database = $config['database'];
            else{
                echo 'Configuration error: Server not found';
                die;
            }
            #open connection
            $connection = mysqli_connect($server, $user, $pass, $database);
            #error in connection
            if($connection === false){
                echo 'Could not connect to MySql';
                die;
            }
            #character set
            $connection ->set_charset('utf8');
            #return connection object
            return $connection;
        }
    }
?>
