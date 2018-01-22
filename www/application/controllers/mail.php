<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
    }
           
    public function fetch()
    {
        $this->load->model('email_model');
        $emails = array();
        $msg = $this->email_model->read($emails);
        if(!$msg){
            $status = "success";
            $msg = "Email file successfully read";
            $data = $emails;              
        }
        else{        
            $status = "error";
            $data = "";          
        }
        
        echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
    }
    
    public function send(){
        
        $this->load->model('email_model');

        $postdata = file_get_contents("php://input");
        $emails = json_decode($postdata);
        
        $status = "";
        $msg = "";   
        
        if(is_array($emails) && count($emails) > 0){
                
            $msg = $this->email_model->write($emails);
            if(!$msg){
                if(!empty($emails[0]->attachment) && !preg_match('/^[a-zA-Z0-9-_]+\.[a-zA-Z]{2,6}$/', $emails[0]->attachment)){
                    $status = "error";
                    $msg = "Attachment file name should be alphanumeric";
                }
                if(!empty($emails[0]->html) && !preg_match('/^[a-zA-Z0-9-_]+\.[a-zA-Z]{2,6}$/', $emails[0]->html)){
                    $status = "error";
                    $msg = "Html file name should be alphanumeric";
                }                
                else{
                    $output = $this->execute($emails[0]->attachment, $emails[0]->html);
                    $status = "success";                    
                    $msg = "Mail initialized, check log " . $output;// . print_r($emails);
                }
            }
            else{
                $status = "error";
                $msg = "Error when writing the email file";
            }
        }
        else{
            $status = "error";
            $msg = "Empty or wrong parameter values";
        }

        echo json_encode(array('status' => $status, 'msg' => $msg));        
    }
    
    private function execute($attachment, $body){

        $CI =& get_instance();

        $seesbinary = $CI->config->item('sees_root_dir') . '/sees.py';
        $seesconfig = $CI->config->item('sees_config_path');
        $seesmail = $CI->config->item('sees_mail_path');
        $seesdata = $CI->config->item('sees_data_dir');

        $params = "";

        if(!$attachment){
            $params = " --text --html_file " . $seesdata . $body;
        }
        else{
            if($body)
                $params = " --html_file " . $seesdata . $body . " --attach " . $seesdata . $attachment;
            else
                $params = " --attach " . $seesdata . $attachment;
        }

        $cmd = 'python ' . $seesbinary . ' --config_file ' . $seesconfig . ' --mail_user ' . $seesmail . $params . ' -w ';
     
        return shell_exec($cmd);
    }    
    
}

