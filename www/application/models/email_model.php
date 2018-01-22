<?php

    class Email_Model extends CI_Model {

        var $name = '';
        var $subject = '';
        // fake email address that the sees mails will seem to be originate from
        var $from = ''; 
        // target email addresses
        var $to = '';
        
        function __construct()
        {          
            parent::__construct();
        }
        
        function read(&$emails){
            
            $file = $this->config->item('sees_mail_path');
                    
            if(!file_exists($file)){
                return "Mail file doesn't exist @ " . $file;
            }
                       
            $handle = @fopen($file, "r");
            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $buffer = trim($buffer);
                    $tokens = explode(":", $buffer);
                    if(count($tokens) === 4){                    
                        $email = new Email_Model();
                        $email->from = trim($tokens[0]);
                        $email->name = trim($tokens[1]);
                        $email->subject = trim($tokens[2]);
                        $email->to = trim($tokens[3]);
                        $emails[] = $email;
                    }                    
                }
                fclose($handle);
            }   
            
            return "";
            
        }
        
        function write($emails){
            
            $msg = "";
            
            $file = $this->config->item('sees_mail_path');
                    
            if(is_writable($file)){
                $content = "";
                
                for ($i = 0; $i < count($emails); $i++) 
                    $content .= $emails[$i]->from . ":" . $emails[$i]->name . ":" . $emails[$i]->subject . ":" . $emails[$i]->to . PHP_EOL;                    
                $content .= "exit" . PHP_EOL;

                $handle = @fopen($file, "w+");
                if ($handle) {

                    if(fwrite($handle, $content) === FALSE){
                        $msg = "Can not write to mail file @ " . $file;
                    }

                    fclose($handle);
                }            
                else{
                    $msg = "Can't open mail file @ " . $file;
                }
            }
            else{
                $msg = "Mail file doesn't exist or it's not writable @ " . $file;
            }
            
            return $msg;
        }        
    
    }

