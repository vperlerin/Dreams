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

		if(empty($_SESSION['dream'])):
			redirect('/');
		else:
			$this->input['dream'] = $_SESSION['dream'];
		endif;

	 
		if(!empty($this->input['sub'])):

		endif;


		$content->input = $this->input;
		$this->template->header = new View('/shared/header.html'); 
 	    $this->template->content = $content;
	    $this->template->footer = new View('/shared/footer.html');
		
	}
	 
    
    
	
}