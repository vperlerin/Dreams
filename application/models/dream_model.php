<?php
  /* 
    CREATE DATABASE dreams;
    CREATE USER 'dream_user'@'localhost' IDENTIFIED BY 'dr34m4remyr34lity';
    GRANT ALL PRIVILEGES ON * . * TO 'dream_user'@'localhost';
    FLUSH PRIVILEGES;

    // For the Terms
    CREATE TABLE terms (
        term      VARCHAR(50) COLLATE UTF8_GENERAL_CI,
        inter_id  INT(11)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE UTF8_GENERAL_CI
  
    // For the interpretation
    CREATE TABLE interpretation (
        inter_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        inter    VARCHAR(1000) COLLATE UTF8_GENERAL_CI 
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE UTF8_GENERAL_CI
  
    */

class Dream_Model {
    
    static public function get_terms() {
        $sql = "SELECT *
                FROM terms";
        return DBF::query($sql,array(),'num');
    } 

    // Test if we already have an interpretation 
    // for at least one term passed
    static public function test_inter($binds) {
        $terms = explode(',', $binds['terms']);
     
        // Build query
        $where = array();
        foreach($terms as $t):
            $where[] = "term collate UTF8_GENERAL_CI LIKE '".$t."'";
        endforeach;

        $sql = "SELECT inter_id
                FROM   terms
                WHERE  " . implode(' OR ', $where);
        
        return DBF::query($sql,array(),'num');
                 
    }

    static public function add_inter($binds) {

        $test = Dream_model::test_inter($binds);

        if(empty($test)):

            $terms = explode(',', $binds['terms']);
            
            // Add the interpretation
            $sql = " INSERT INTO interpretation  
                    SET inter = :inter  ";
            $inter_id = DBF::set($sql,$binds);

            foreach($terms as $t):
                // Link the terms
                $sql = "INSERT INTO terms
                        SET term     = '"  . rtrim(ltrim($t)) . "',
                            inter_id = '".$inter_id. "'
                ";

                DBF::set($sql,$binds);
            endforeach;    

            return true;

        else:
            return $test;
        endif;

        
    }

    static public function get_all_inter() {
        $sql = "SELECT *
                FROM interpretation";
        $inters = DBF::query($sql,array(),'num');
         
        foreach($inters as $k=>$inter) {
            $sql = "SELECT term
                    FROM   terms
                    WHERE  inter_id = " . $inter['inter_id'];
            $term  = DBF::query($sql,array(),'num');
          
            $inters[$k]['terms']  = implode(', ', array_map(function ($entry) {
                return $entry['term'];
              },  $term));
        } 

        return $inters;
    }

    // Return all terms with inter_id
    static public function get_all_terms() {
        $sql = "SELECT *
                FROM terms";
        return DBF::query($sql,array(),'num');
    }

    // Return all terms and interpration from an inter_id
    static public function get_inter_and_terms($binds) {
        $sql =  "SELECT *
                 FROM terms
                 WHERE inter_id = :inter_id";
        $res['terms'] = DBF::query($sql,$binds,'num');

        $sql = "SELECT *
                FROM  interpretation
                WHERE inter_id = :inter_id";
        $res['inter'] = DBF::query($sql,$binds,'num');

        return $res;

    }

}