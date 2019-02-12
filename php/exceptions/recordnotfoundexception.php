<?php
class RecordNotFoundException extends Exception{

    //attributes
    protected $message;

    //contructor
    public function __construct(){
        //0 arguments : generic message
        if(func_num_args()==0)
        $this->message = 'Record not found exception';
        // 1 argument : detailed message 
        if(func_num_args()==1)
        $this->message = 'Record not found for id'.func_get_arg(0);
        }
    }
?>