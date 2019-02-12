<?php

// El apis
require_once('php/stat.php');

if($_SERVER['REQUEST_METHOD']=='GET'){
    if(isset($_GET['id'])){
        $stat = new Stat($_GET['id']);
        echo json_encode(array(
            'status' => 0,
            'statsPlayer' => json_decode($stat->toJson())
        ));
    }else{
        echo json_encode(array(
            'status' => 0,
            'statsPlayers' => json_decode(Stat::getAllToJson())
        ));
    }
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['id'])){
        $st = new Stat();
        $st->setPlayer($_POST['id']);
        if($st->add()){
            echo json_encode(array(
                'status' => 0,
                'Message' => "Added Successfully"
            ));
        }else{
            echo json_encode(array(
                'status' => 2,
                'Message' => "Could not Added"
            ));
        }
    }else{
        echo json_encode(array(
            'status' => 2,
            'ErrorMessage' => "Missing parameters"
        ));
    }
}



if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    //parse received data
    parse_str(file_get_contents('php://input'), $putData);
    //check if data key was received
    if(isset($putData['data'])){
         //decode data from json
         $jsonData = json_decode($putData['data'], true);
         if(isset($jsonData['player']) &&  isset($jsonData['hits']) && isset($jsonData['strikes']) &&
         isset($jsonData['homeruns']) && isset($jsonData['playedgames']) && isset($jsonData['balls']) &&
         isset($jsonData['stolenbases']) && isset($jsonData['outs']) && isset($jsonData['wins']) && 
         isset($jsonData['loses']) && isset($jsonData['runs'])){
            echo "SI FUNCIONA";
            $st = new Stat();
            $st->setPlayer($jsonDaata['player']);
            $st->setHit($jsonDaata['hits']);
            $st->setStrike($jsonDaata['strikes']);
            $st->setHomeRun($jsonDaata['homeruns']);
            $st->setPlayedGames($jsonDaata['playedgames']);
            $st->setBalls($jsonDaata['balls']);
            $st->setStolenBase($jsonDaata['stolenbases']);
            $st->setOut($jsonDaata['outs']);
            $st->setWin($jsonDaata['wins']);
            $st->setLose($jsonDaata['loses']);
            $st->setRun($jsonDaata['runs']);
            if($st->Update()){
                json_encode(array(
                    'status'=>1,
                    'Message'=>'Updated Successfully'
                ));
            }else{
               
                json_encode(array(
                    'status'=>1,
                    'ErrorMessage'=>'Could not updated'
                ));
            }
        }else{

            json_encode(array(
                'status'=> 3,
                'errorMessage'=> 'Are little arguments'
            ));
        }
    }else{
        json_encode(array(
            'status'=> 2,
            'errorMessage'=> 'Missing Parameters'
        ));
    }
}
?>