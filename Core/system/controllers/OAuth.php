<?php 

class OAuth extends Controller
{

    function __construct()
    {
        parent::__construct();
    }
    function index(){
        //load error 404
    }
    
    function fbLogin(){
        Verify::authLogin('location:'.URL.'index');
        $fb = new FacebookAPI($fb_Data);
        $fb->url = URL.'auth/fbResponce/login';
        $fb->run();
    }
    function fbSignUp(){
        Verify::authLogin('location:'.URL.'index');
        $fb = new FacebookAPI($fb_Data);
        $fb->url = URL.'auth/fbResponce/signup';
        $fb->run();
    }

    function fbResponce($action){
        Verify::authLogin('location:'.URL.'index');
        $fb = new FacebookAPI($fb_Data);
        $data = $fb->callback();
        $fb_model = new Facebook_Model();

        if ($action == 'login') {
            $result = $fb_model->access($data);
        }elseif ($action == 'signup') {
           $result = $fb_model->register($data);
        }
        
        

        if($result['result'] = 'success'){
            Utility::redirect(URL.'index/');
        }else{
            Utility::redirect(URL.'Auth/login');
        }
        
    }

    function gLogin(){
        Verify::authLogin('location:'.URL.'index');
        $fb = new FacebookAPI($fb_Data);
        $fb->url = URL.'auth/gResponce/login';
        $fb->run();
    }
    function gSignUp(){
        Verify::authLogin('location:'.URL.'index');
        $fb = new FacebookAPI($fb_Data);
        $fb->url = URL.'auth/gResponce/signup';
        $fb->run();
    }

    function gResponce($action){
        Verify::authLogin('location:'.URL.'index');
        $g = new GoogleAPI($g_Data);
        $data = $g->callback();
        $g_model = new Google_Model();

        if ($action == 'login') {
            $result = $g_model->access($data);
        }elseif ($action == 'signup') {
           $result = $g_model->register($data);
        }

        if($result['result'] = 'success'){
            Utility::redirect(URL.'index/');
        }else{
            Utility::redirect(URL.'Auth/login');
        }
        
    }

    function Ig_Responce()
    {
        
    }

}

?>