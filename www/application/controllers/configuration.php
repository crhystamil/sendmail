<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configuration extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
    }
           
    public function fetch()
    {
        $this->load->model('configuration_model');
        $msg = $this->configuration_model->read();
        if(!$msg){
            $status = "success";
            $msg = "Configuration successfully read";
            $data = $this->configuration_model;              
        }
        else{        
            $status = "error";
            $data = "";          
        }
        
        echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
    }
    
    public function update(){
        
        $this->load->model('configuration_model');

        $postdata = file_get_contents("php://input");
        $configuration = json_decode($postdata);
        
        /*
        $this->configuration_model->domain = $this->input->post('domain', TRUE);
        $this->configuration_model->smtpserver = $this->input->post('smtpserver', TRUE);
        $this->configuration_model->smtplogpath = $this->input->post('smtplogpath', TRUE);
        $this->configuration_model->type = $this->input->post('type', TRUE);
        $this->configuration_model->time = $this->input->post('time', TRUE);
        */
        
        $status = "";
        $msg = "";   
        
        /*
        if($this->configuration_model->domain && $this->configuration_model->smtpserver && 
                $this->configuration_model->smtplogpath && $this->configuration_model->type && 
                $this->configuration_model->time ){
         */

        if(isset($configuration->domain) && 
                isset($configuration->smtpserver) && isset($configuration->smtplogpath) && 
                isset($configuration->type) && isset($configuration->time)){
            
            $this->configuration_model->domain = $configuration->domain;
            $this->configuration_model->smtpserver = $configuration->smtpserver;
            $this->configuration_model->smtplogpath = $configuration->smtplogpath;
            $this->configuration_model->type = $configuration->type;
            $this->configuration_model->time = $configuration->time;
                
            $msg = $this->configuration_model->write();
            if(!$msg){
                $status = "success";
                $msg = "Configuration successfully written";
            }
            else{
                $status = "error";
                $msg = "Error when writing the configuration";
            }
        }
        else{
            $status = "error";
            $msg = "Empty or wrong parameter values";
        }

        echo json_encode(array('status' => $status, 'msg' => $msg));        
    }
    
}

