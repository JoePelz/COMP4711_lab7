<?php

/**
 * core/MY_Controller.php
 *
 * Default application controller, rendering the website template
 *
 * @author		JoePelz
 * @copyright           2016, Joe Pelz
 * ------------------------------------------------------------------------
 */
class MY_Controller extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('timetable');
        $this->load->helper('form');
    }
    
    function render()
    {
        $result = array();

        foreach ($this->data['facets'] as $facet) {
            $temp = array();
            $temp['facet_name'] = $facet["facet_name"];
            $temp['facet_groups'] = array();
            foreach($facet["groups"] as $groupname => $group) {
                $temp2 = array();
                $temp2['group_name'] = $groupname;
                $temp2['group_classes'] = array();
                foreach($group as $booking){
                    $temp2['group_classes'][] = array("specificClass" => $this->parser->parse('_class', (array)$booking, true));
                }
                $temp['facet_groups'][] = array("group" => $this->parser->parse('_groups', $temp2, true));
            }
            $result[] = array("facet" => $this->parser->parse('_facets', $temp, true));
            
        }
        
        $this->data['timetable_facets'] = $result;
        $this->parser->parse('welcome', $this->data);
    }
}