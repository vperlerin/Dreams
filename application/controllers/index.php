<?php 

class index_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {
	 
        $content = new View('/index.html');

		if(!empty($this->input['sub'])):
			$_SESSION['dream'] = $this->input['dream'];

			// Test the terms in the dream
			// And redirect to the proper page 
			$res = Check_terms::checkterms($this->input['dream']);
			
			if(empty($res)):
				// We didn't find anything
				$content->input['dream'] = $this->input['dream'];
				$content->input['not_found']= true;
			else:
				$_SESSION['res'] = $res;
				redirect('step2');
			endif;

		
		endif;
		
		$this->template->header = new View('/shared/header.html'); 
 	    $this->template->content = $content;
	    $this->template->footer = new View('/shared/footer.html');
		
	}

	/*
	 * All unknown pages will hit here 
	 */
	public function step2() {
	 
		$content = new View('/step2.html');
	  
		if(empty($_SESSION['dream']) && empty($this->input['sub'])):
			redirect('/');
	 
		endif;

	 
		if(!empty($this->input['sub'])):
			// We rely on the browser for the required files
				 
			$to_send = array_merge($this->input,$_SESSION);
			$to_post = array();

			$to_post['last_name']  	= filter_var($to_send['last_name'], FILTER_SANITIZE_STRING);
			$to_post['first_name'] 	= filter_var($to_send['first_name'], FILTER_SANITIZE_STRING);
			$to_post['email'] 		= filter_var($to_send['email'], FILTER_SANITIZE_EMAIL);


			$to_post['custom_field']['greatest_wish'] = filter_var($to_send['greatest_wish'], FILTER_SANITIZE_STRING);
			$to_post['custom_field']['relationship_status'] = filter_var($to_send['relationship_status'], FILTER_SANITIZE_STRING);
			$to_post['custom_field']['employment_status'] = filter_var($to_send['employment_status'], FILTER_SANITIZE_STRING);

			$to_post['custom_field']['greatest_wish'] = filter_var($to_send['greatest_wish'], FILTER_SANITIZE_STRING);
			$to_post['custom_field']['dream_full_text'] = filter_var($to_send['dream'], FILTER_SANITIZE_STRING);
			$to_post['custom_field']['dream_keywords'] = filter_var(implode(', ', array_map(function ($entry) {  return $entry['term'];  },  $to_send['res'])), FILTER_SANITIZE_STRING);

 			$interpretations = Dream_model::get_interpretations(array('inter_ids'=>array_map(function ($entry) {  return $entry['inter_id'];  },  $to_send['res'])));
 			$to_post['custom_field']['interpretations'] = implode(PHP_EOL,array_map(function ($entry) {  return $entry['inter'];  },  $interpretations));

			unset($to_send);
				 

			// We send everything to Maropost
			$res = Send_to_maropost::request(MARO_API, MARO_auth_token, "POST", MARO_LIST, $to_post);
			
			pp($res);
		endif;


		$content->input = $this->input;
		$this->template->header = new View('/shared/header.html'); 
 	    $this->template->content = $content;
	    $this->template->footer = new View('/shared/footer.html');
		
	}
	 
    
    
	
}