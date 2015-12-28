<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {
    
    function __construct() {
        parent::__construct();      
        
        $tmpl = $this->config->item('table_template');
        $this->table->set_template($tmpl);
    }
    
    /**
     * Index Page for this controller.
     */
    public function index() {
        
        $this->load->view('header');

        $data = array();
        $USstates = $this->GeoData->getUS_States();
                    
        $this->form_validation->set_rules('source', 'Source','requried');
        if ($this->form_validation->run() == TRUE || TRUE){
            foreach ($USstates as $state){
                $data['states_options'][$state->id] = "{$state->name} - {$state->abbr}";
            }
            
            $this->load->view('flowmap', $data);            
        }else {
           
            $this->table->set_heading('State Name', 'Abbreviation', 'Value');
            foreach ($USstates as $state){
                $data['states_options'][$state->id] = "{$state->name} - {$state->abbr}";
                $this->table->add_row(array(
                    $state->name,
                    $state->abbr,
                    form_input(array('name' => "dest[{$state->id}]",'id' => "state-{$state->id}", 'placeholder' => 'enter a value', 'class' => 'form-control'))
                ));
            }
//            debug_arr($states, true);
            $this->load->view('dashboard', $data);
        }

        $this->load->view('footer');
    }
    
    function test() {
        
//        return;
        
        $data['top'] = $this->input->post('top'); 
        $data['left'] = $this->input->post('left'); 
        
        $this->GeoData->updateStates($this->input->post('state_id'),$data);
        
        echo 1;
    }
    
    function points () {
        $points = array();
        
        for($i=0; $i < 12; $i++){
            $points[] = array(
                'x' => rand(10,200),
                'y' => rand(10,200),
            );
        }
        
        $distance = array();
        
        foreach($points as $index => $point){
            foreach($points as $idx => $pt){
                if ($idx != $index && empty($distance["{$idx}-{$index}"])){
                    $distance_calc = pow(($point['x'] - $pt['x']),2) + pow(($point['y'] - $pt['y']),2);
                    $distance_calc = sqrt($distance_calc);
//                    echo "<div>({$point['x']} - {$pt['x']})^2 + ({$point['y']} - {$pt['y']})^2 = {$distance_calc}</div>";
                    $distance["{$index}-{$idx}"] = round($distance_calc,3);
                }
            }
        }
        
        asort($distance);
        debug_arr($points);
        debug_arr($distance);
//        echo json_indent($points);
    }
    
    function test2 (){
        $this->load->view('test');
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */