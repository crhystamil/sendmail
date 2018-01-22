<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }
           
    public function read()
    {
        $data = "";
        $status = "";
        
        $this->load->model('configuration_model');        
        $msg = $this->configuration_model->read();
        if(!$msg){
            
            $file = $this->configuration_model->smtplogpath;

            if(file_exists($file)){

                clearstatcache(false, $file);

                $len = filesize($file);

                if(!$this->session->userdata('lastpos')){
                    $lastpos = $len; // we don't want to read the log file from the start but the end, like tailing
                    $this->session->set_userdata('lastpos', $lastpos);
                }
        
                $lastpos = $this->session->userdata('lastpos');

                if ($len < $lastpos) {
                    //file has been deleted or reset
                    $lastpos = $len;
                    $this->session->set_userdata('lastpos', $len);
                    $data = "";
                }
                elseif ($len > $lastpos) {
                    $f = fopen($file, "rb");

                    fseek($f, $lastpos);
                    $lines = array();
                    while (($buffer = fgets($f, 4096)) !== false) {
                        $lines[] = $buffer;
                    }                

                    $this->session->set_userdata('lastpos', ftell($f));

                    fclose($f);

                    $data = $lines;

                    //flush();                    
                }
                $status = "success";
                $msg = "Log successfully read";                
            }
            else{
                $status = "error";
                $msg = "Log file doesn't exist @" . $file;            
                $data = "";
            }
        }
        else{
            $status = "error";
            $data = "Error happened when reading the log file";
        }
        
        echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
        
    }
        
}

