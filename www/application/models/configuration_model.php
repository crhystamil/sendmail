<?php

    class Configuration_Model extends CI_Model {

        // the source domain name that the target SMTP server will get the phishing emails from, e.g. example.com
        var $domain = ''; 
        // the source SMTP server that the phishing emails will be sent from
        var $smtpserver = '127.0.0.1';
        // the time differences between sent emails (in seconds)
        var $time = '1,3';
        // specifies which email server, e.g. postfix
        var $type = 'postfix';
        // specifies the log path email server uses, e.g. /var/log/mail.log
        var $smtplogpath = '/var/log/mail.log';
        
        function __construct()
        {          
            parent::__construct();
        }
        
        function read(){
            
            $file = $this->config->item('sees_config_path');
                    
            if(!file_exists($file)){
                return "Configuration file doesn't exist @ " . $file;
            }
            
            $handle = @fopen($file, "r");
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $buffer = trim($buffer);
                    $tokens = explode("=", $buffer);
                    if(count($tokens) === 2){                    
                        if(strpos(trim($tokens[0]), "domain") === 0)
                            $this->domain = trim($tokens[1]);                        
                        elseif(strpos(trim($tokens[0]), "server") === 0)
                            $this->smtpserver = trim($tokens[1]);                        
                        elseif(strpos(trim($tokens[0]), "time") === 0)
                            $this->time = trim($tokens[1]);                        
                        elseif(strpos(trim($tokens[0]), "type") === 0)
                            $this->type = trim($tokens[1]);                        
                        elseif(strpos(trim($tokens[0]), "log_path") === 0)
                            $this->smtplogpath = trim($tokens[1]); 
                    }                    
                }
                fclose($handle);
            }   
            
            return;
            
        }
        
        function write(){
            
            $msg = "";
            
            $file = $this->config->item('sees_config_path');
                    
            if(is_writable($file)){
                $content = "[mail]" . PHP_EOL;
                $content .= "domain = " . $this->domain . PHP_EOL;
                $content .= "[smtp]" . PHP_EOL;
                $content .= "server = " . $this->smtpserver . PHP_EOL;
                $content .= "time = " . $this->time. PHP_EOL;
                $content .= "[log]" . PHP_EOL;
                $content .= "type = " . $this->type . PHP_EOL;
                $content .= "log_path = " . $this->smtplogpath . PHP_EOL;

                $handle = @fopen($file, "w+");
                if ($handle) {

                    if(fwrite($handle, $content) === FALSE){
                        $msg = "Can not write to configuration file @ " . $file;
                    }

                    fclose($handle);
                }            
                else{
                    $msg = "Can't open configuration file @ " . $file;
                }
            }
            else{
                $msg = "Configuration file doesn't exist or it's not writable @ " . $file;
            }
            
            return $msg;
        }
    
    }