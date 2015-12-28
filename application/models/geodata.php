<?php

/**
 * Description of GeoData
 *
 * @author Mubashar Khokhar
 */
class GeoData extends CI_Model{
    
    function __construct() {
        parent::__construct();
    }
    
    function getUS_States(){
        $res = $this->db->get('usa_states');
        
        $result = array();
        foreach ($res->result() as $row){
            $result[] = $row;
        }
        
        return $result;
    }
    
    function updateStates($id, $data){
        $this->db->where(array('id' => $id));
        $this->db->update('usa_states', $data);
        
        return $this->db->affected_rows();
    }
    
    function getUSACoords(){
        $res = $this->db->get('usa_states');
        $temp = array();
        foreach ($res->result() as $row){
            if ($row->top != NULL){
                $temp[] = $row;
            }
        }
        
        return $temp;
            
    }
    
    
}
