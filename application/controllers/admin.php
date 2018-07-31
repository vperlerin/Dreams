<?php
ini_set('memory_limit', '-1');
class admin_controller extends Template_Controller {
	
	 
 	public function __construct($cont, $func) {
        parent::__construct($cont, $func);
        if($func!='login') {
			$this->logged_in_required();
        }
    }

    public function index() {
        if(Auth::is_user_logged_in()) {
            redirect('/admin/addterms/');
        } else {
            redirect('/admin/login/');
        }
    }

    // Auth Admin
    public function auth() {
          // Regular login    
        $user = array(
            'email'        => $this->input['email'],
            'password'     => md5($this->input['password']) 
        );  
        return Auth::check_user($user);
    }


    // Login page
    public function login() {

       $this->page_title = 'Login';
       $content = new View('/admin/login.html');  
         
       if(!empty($this->input['email'])) {
            $binds = array('email' => $this->input['email']);
            if(self::auth()) {
                redirect('/admin/addterms');
            }
       }     
 
        $this->template->header = new View('/shared/header.html'); 
        $this->template->content = $content;
        $this->template->footer = new View('/shared/footer.html'); 
    }


    public function logged_in_required() { 
        if(!empty($_SESSION[Cookie::get('au_666_FR')])) {
            return true;
        }
        redirect('/admin/login/');
    }

    public function edit() {
        $content = new View('/admin/edit.html');  

        if(!empty($this->input['inter_id'])):
           $res = Dream_model::get_inter_and_terms($this->input);
           $content->input['terms'] = implode(', ', array_map(function ($entry) { return $entry['term'];   },  $res['terms']));
           $content->input['inter'] = $res['inter'][0]['inter'];
           $content->input['inter_id'] = $res['inter'][0]['inter_id'];
        endif;

        if(!empty($this->input['sub'])):
            if(empty($this->input['terms'])):
                $this->input['errors'][] = 'You need to enter a least one term';
            endif;

            if(empty($this->input['inter'])):
                $this->input['errors'][] = 'You need to enter a least one interpretation';
            endif;
  
            if(empty($this->input['errors'])):

                $res = Dream_model::test_inter_but_inter_id($this->input);
                
                if(!empty($res)):
                    $alreadyexists = Dream_model::get_inter_and_terms(array('inter_id'=>$res[0]['inter_id']));
                    $content->input['errors']['al']   = '<p>You already have an interpretation for at least one of the terms your entered:</p>';
                    $content->input['errors']['al']  .= '<strong>'.implode(', ', array_map(function ($entry) { return $entry['term'];   },  $alreadyexists['terms'])).'</strong>:<br/>';
                    $content->input['errors']['al']  .= '<em>'. $alreadyexists['inter'][0]['inter'].'</em>';
                    $content->input['errors']['al']  .= '<p class="mt-4"><a href="/admin/edit?inter_id='.$alreadyexists['inter'][0]['inter_id'].'" class="btn btn-secondary">Edit</a> <a href="/admin/delete?inter_id='.$alreadyexists['inter'][0]['inter_id'].'" class="btn btn-danger">Delete</a></p>';
                else:
                    Dream_model::delete_interpretation($this->input);
                    $res = Dream_model::add_inter($this->input);
                    unset($this->input['sub']);
                    redirect('/admin/edit?val=yes&inter_id='. $res);
                endif;
 
                
            endif;
        endif;
 
        
        $this->template->header = new View('/shared/header_admin.html'); 
        $this->template->header->bodyClass = 'admin';
        $this->template->content = $content; 
    }


    public function delete() {
        $content = new View('/admin/delete.html');  

        if(!empty($this->input['inter_id'])):
            Dream_model::delete_interpretation($this->input);
            redirect($_SERVER['HTTP_REFERER']);
        endif;
        
        $this->template->header = new View('/shared/header_admin.html'); 
        $this->template->header->bodyClass = 'admin';
        $this->template->content = $content; 
    }

    
    public function addterms() {
 

        $content = new View('/admin/addterms.html');  

        if(!empty($this->input['sub'])):
            
            if(empty($this->input['generic'])):
                $this->input['errors'][] = 'You need to enter one generic term';
            endif;
 
            if(empty($this->input['terms'])):
                $this->input['errors'][] = 'You need to enter a least one term';
            endif;

            if(empty($this->input['inter'])):
                $this->input['errors'][] = 'You need to enter a least one interpretation';
            endif;
  
            if(empty($this->input['errors'])):
                $res = Dream_model::add_inter($this->input);
                if(!is_numeric($res)):
                    // We get the existing terms/interpretation
                    //Dream_model::get_inter_and_terms($res['']);
                    $alreadyexists = Dream_model::get_inter_and_terms(array('inter_id'=>$res[0]['inter_id']));
                    $this->input['errors']['al']   = '<p>You already have an interpretation for at least one of the terms your entered:</p>';
                    $this->input['errors']['al']  .= '<strong>'.implode(', ', array_map(function ($entry) { return $entry['term'];   },  $alreadyexists['terms'])).'</strong>:<br/>';
                    $this->input['errors']['al']  .= '<em>'. $alreadyexists['inter'][0]['inter'].'</em>';
                    $this->input['errors']['al']  .= '<p class="mt-4"><a href="/admin/edit?inter_id='.$alreadyexists['inter'][0]['inter_id'].'" class="btn btn-secondary">Edit</a> <a href="/admin/delete?inter_id='.$alreadyexists['inter'][0]['inter_id'].'" class="btn btn-danger">Delete</a></p>';
                endif;
            endif;

        endif;

        // Get current terms/inter
        $this->input['defs'] = Dream_model::get_all_inter();

        $content->input = $this->input;

        $this->template->header = new View('/shared/header_admin.html'); 
        $this->template->header->bodyClass = 'admin';
        $this->template->content = $content; 
    }
    
    /**
	 * Logout  
	*/
	public function logout() {
        unset($_SESSION[Cookie::get('au_666_FR')]);
        Cookie::delete('au_666_FR');
        redirect('/admin/login');
    }

}

?>