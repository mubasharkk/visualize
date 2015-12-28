<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test
 *
 * @author Mubashar Khokhar
 */
class Test extends CI_Controller{
    
    function __construct() {
        parent::__construct();
        
        $this->load->library(array('cluster'));
    }
    
    function index(){        
        
        $points = array();
        
        for($i=0; $i < 12; $i++){
            $x = rand(10,200);
            $y = rand(10,200);
            $value = rand(2,100);
            $this->cluster->addNode($x, $y, $value);
        }
        
        $this->cluster->buildRelation();
        
        $this->cluster->traceAll();
    }
    
    
    function json(){
        
        
        $points = array();
        
        for($i=0; $i < 12; $i++){
            $x = rand(10,550);
            $y = rand(30,800);
            $value = rand(2,100);
            $this->cluster->addNode($x, $y, $value);
        }
        
        $this->cluster->addNode(200,300, NULL, array('type' => Node::N_TYPE_SOURCE));
        
        $this->cluster->buildHierarchy();
        
        $data['source'] = $this->cluster->getSourceNode(TRUE);
        $data['nodes'] = $this->cluster->getNodes(TRUE);
        $data['relation'] = $this->cluster->connections;
        
        $this->output
                ->set_header("Access-Control-Allow-Origin: *")
                ->set_content_type('application/json')
                ->set_output(json_indent($data));
    }
    
    function sample () {
        
        $data = array();
        for($i=0; $i < 12; $i++){
            $x = rand(20,550);
            $y = rand(30,800);
            $value = rand(2,100);
            
            $this->cluster->addNode($x, $y, $value);
        }
        
        
        
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_indent($data));        
    }
}
