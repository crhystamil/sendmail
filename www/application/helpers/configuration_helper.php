<?php

/*
function getconfiguration(){
    
    // CAN'T STORE an object in session since
    // CI's session stores session data in the cookie!!!
    // you have to use DB or encryption
    // still can't store the objects, so we need to convert objects in to assoc arrays
    // before storing!!! SOOO PROBLEMATIC!
    
    $CI =& get_instance();
    
    $CI->load->model('configuration_model');
    $conf = new Configuration_Model();
    
    $conf->domain = $CI->session->userdata('domain');
    $conf->smtpserver = $CI->session->userdata('smtpserver');
    $conf->type = $CI->session->userdata('type');
    $conf->time = $CI->session->userdata('time');
    $conf->smtplogpath = $CI->session->userdata('smtplogpath');
    
    if(!$conf->domain || !$conf->smtpserver || !$conf->smtplogpath || !$conf->time || !$conf->type){
        if($conf->read()){
            $newdata = array(
                   'domain'     => $conf->domain,
                   'smtpserver' => $conf->smtpserver,
                   'smtplogpath'=> $conf->smtplogpath,
                   'time'       => $conf->time,
                   'type'       => $conf->type
            );            
            $CI->session->set_userdata($newdata);
            return $conf;
        }
        else{
            // can't initialize the configuration, so log or do smt.
        }
    }
    else{
        return $conf;
    }
}

*/