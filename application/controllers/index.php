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

			// We don't care if we find an interpretation,
			// as it's a total scam
			/*
			if(empty($res)):
				// We didn't find anything
				$content->input['dream'] 	= $this->input['dream'];
				$content->input['not_found']= true;
			else:
			*/
			$_SESSION['res'] = $res;
			redirect('step2');
			 	/*
			endif;
			*/
		
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
		 
			$to_post = array();

			$to_post['last_name']  	= filter_var($this->input['last_name'], FILTER_SANITIZE_STRING);
			$to_post['first_name'] 	= filter_var($this->input['first_name'], FILTER_SANITIZE_STRING);
			$to_post['email'] 		= filter_var($this->input['email'], FILTER_SANITIZE_EMAIL);
 
			$to_post['custom_field']['greatest_wish'] 		= filter_var(str_replace('_',' ',$this->input['greatest_wish']), FILTER_SANITIZE_STRING);
			$to_post['custom_field']['relationship_status'] = filter_var(str_replace('_',' ',$this->input['relationship_status']), FILTER_SANITIZE_STRING);
			$to_post['custom_field']['employment_status'] 	= filter_var(str_replace('_',' ',$this->input['employment_status']), FILTER_SANITIZE_STRING);

			$to_post['custom_field']['dream_full_text'] = filter_var($_SESSION['dream'], FILTER_SANITIZE_STRING);

			
	 		if(!empty($_SESSION['res'])):
				// We have keywords / interpretation
				// We want 5 max.
				$keywords   = array_map(function ($entry) {  return $entry['term'];  },  $_SESSION['res']);
 
				$tmp_interpretations = array_map(function ($entry) {  return $entry['inter_id'];  },  $_SESSION['res']);

				foreach($tmp_interpretations as $int):
 					$interpretations[] = Dream_model::get_interpretation(array('inter_id'=>$int));
				endforeach;

				$i=0;
				foreach($keywords as $c=>$key):
					if($i<5):
						$to_post['custom_field']['dream_keyword_'.$i] 	= filter_var($key,FILTER_SANITIZE_STRING);
						$to_post['custom_field']['interpretation_'.$i]  = filter_var($interpretations[$c],FILTER_SANITIZE_STRING);
					endif;
					$i+=1;
				endforeach;
 
			endif;
 

			unset($to_send);
 		 
			// We send everything to Maropost
			$res = Send_to_maropost::request(MARO_API, MARO_auth_token, "POST", MARO_LIST, $to_post);
			
			redirect('/thankyou');
		endif;


		$content->input = $this->input;
		$this->template->header = new View('/shared/header.html'); 
 	    $this->template->content = $content;
	    $this->template->footer = new View('/shared/footer.html');
		
	}
	 

	public function thankyou() {
		$content = new View('/thankyou.html');
		$this->template->header = new View('/shared/header.html'); 
 	    $this->template->content = $content;
	    $this->template->footer = new View('/shared/footer.html');
	}

    
    
	
}