<?php

/**
 * Description of api
 *
 * @author Mubashar Khokhar
 */
class API extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        
    }

    function dimensions($type) {

        $data = array();
        switch ($type) {
            case 'usa-states':
                $data = $this->GeoData->getUSACoords();
                $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_indent($data));

                break;
        }
    }

    function sample($type) {

        $type = intval($type);

        switch ($type) {
            case 1:
                if (($handle = fopen("./data/test.csv", "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        debug_arr($data);
                    }
                    fclose($handle);
                }
                break;
            default:
                $data = array();
        }
    }

}
