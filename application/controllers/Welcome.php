<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
	 
    public function __construct() {
        parent::__construct();
        $this->load->model('timetable');
        $this->load->helper('form');
        $this->data['facets'] = array();
    }
	
    
    public function index()
    {
        $this->data['facets'][] = array("facet_name" => "Days Facet", "groups" => (array)$this->timetable->get_bookings_by_day(null, null));
        $this->data['facets'][] = array("facet_name" => "Times Facet", "groups" => (array)$this->timetable->get_bookings_by_time(null, null));
        $this->data['facets'][] = array("facet_name" => "Courses Facet", "groups" => (array)$this->timetable->get_bookings_by_course(null, null));

        $this->create_searchform();

        $this->render();
        
    }
    
    
    public function search()
    {
        if ($this->input->post('searchbutton') == null) {
            redirect(index_page());
        }
        $days_search = $this->input->post('days');
        $courses_search = $this->input->post('courses');
        $slots_search = $this->input->post('slots');

        $times = (array)$this->timetable->get_bookings_by_time($days_search, $slots_search);
        $days = (array)$this->timetable->get_bookings_by_day($days_search, $slots_search);
        $courses = (array)$this->timetable->get_bookings_by_course($days_search, $slots_search);

        // Check for search 'bingo' in a long, protracted, drawn-out and ugly way
        if (sizeof($courses) == 1
            && sizeof($times) == 1
            && sizeof($days) == 1
            && reset($courses)[0]->course == reset($times)[0]->course
            && reset($courses)[0]->course == reset($days)[0]->course) 
        {
            $this->data['facets'][] = array("facet_name" => "BINGO", "groups" => $courses);
        } else {
            $this->data['facets'][] = array("facet_name" => "Search Results - Days Facet", "groups" => $days);
            $this->data['facets'][] = array("facet_name" => "Search Results - Times Facet", "groups" => $times);
            $this->data['facets'][] = array("facet_name" => "Search Results - Courses Facet", "groups" => $courses);
        }
        
        $this->create_searchform();
        
        $this->render();
    }
    
    function create_searchform() {
        // Display form
        $searchform = form_open('/welcome/search');
        $searchform .= form_dropdown('days', $this->timetable->get_days());
        $searchform .= form_dropdown('slots', $this->timetable->get_timeslots());
        //We don't need to filter by courses. Just by time and day.
        //...but we could do so easily.
        //$searchform .= form_dropdown('courses', $this->timetable->get_courses());
        $searchform .= form_submit('searchbutton', 'Search');
        $searchform .= form_close();

        $this->data['searchform'] = $searchform;
    }
}