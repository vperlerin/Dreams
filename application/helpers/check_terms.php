<?php
 
 
Class Check_terms {

    // Return a list of interpretation IDs
    public static function checkterms($string) {

        // List of inter_id/term to return
        $res = array();

        // Get the list of terms
        $terms = Dream_model::get_all_terms();

        // Remove the quotations marks and the new lines
        $string = strtolower(str_replace('"', "",str_replace("'", "", trim(preg_replace('/\s+/', ' ', $string)))));
        $string = preg_replace("#[[:punct:]]#", " ", $string);
 
        // If we don't have any space, add a space at the end 
        // so we can test toward the [term] + space
        if( strpos($string, " ") !== true):
            $string .= " ";
        endif;
        
        foreach($terms as $t):
 
            $test = strpos( $string ,$t['term']. ' ');

            if($test !== false) { 
                // Get the generic term for the interpretation
                $generic_term =  Dream_model::get_generic(array('inter_id'=>$t['inter_id'],'term'=>$t[term]));

                if(!in_array(array('inter_id'=>$t['inter_id'],'term'=>$generic_term),$res)):
                    $res[]= array('inter_id'=>$t['inter_id'],'term'=>$generic_term);
                endif;
            }

        endforeach;
 
   
        return $res;
    }


}
	