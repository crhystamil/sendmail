<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attachment extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('file_model');
        
        $config['upload_path'] = $this->config->item('sees_data_dir');
        $config['allowed_types'] = 'gif|jpg|png|doc|txt|pdf|rar|docx|pptx|ppt|xlsx|xls|zip|html|htm|text';
        $config['max_size'] = 1024 * 12; // 12MB
        $config['encrypt_name'] = FALSE;        
        $this->upload->initialize($config);
    }
           
    public function upload()
    {
        $status = "";
        $msg = "";

        if(!preg_match('/^[a-zA-Z0-9-_]+\.[a-zA-Z]{2,6}$/', $_FILES['userfile']['name'])){
            $status = "error";
            $msg = "File name should be alphanumeric";
        }
        else{        
            if (!$this->upload->do_upload())
            {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
            }
            else
            {            
                $status = "success";
                $msg = "File successfully uploaded";
            }
        }
        echo json_encode(array('status' => $status, 'msg' => $msg));
    }   
    
    public function fetch()
    {
        $files = array();

        foreach (glob($this->config->item('sees_data_dir') . '*.*') as $filename) {
            $aFile = new File_Model;
            $aFile->name = basename($filename); 
            $aFile->size = filesize($filename);
            $aFile->date = filemtime($filename);
            $files[] = $aFile;
        }           
                
        echo json_encode($files);
    }    
    
}

