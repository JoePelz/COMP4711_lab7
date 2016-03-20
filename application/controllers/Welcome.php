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
		$bingo = false;
		
		if ($this->input->post('searchbutton') != null) {
			$this->data['searchresult'] = "<h2>Search Result</h2>\n";
			$search = true;
			$days_search = $this->input->post('days');
			$courses_search = $this->input->post('courses');
			$slots_search = $this->input->post('slots');
			
			$times = (array)$this->timetable->get_bookings_by_time($slots_search);
			$days = (array)$this->timetable->get_bookings_by_day($days_search);
			$courses = (array)$this->timetable->get_bookings_by_course($courses_search);
			
			// Check for search 'bingo' in a long, protracted, drawn-out and ugly way
			if (sizeof($courses[$courses_search]) == 1 && sizeof($times[$slots_search]) == 1 
				&& sizeof($days[$days_search]) == 1
				&& $courses[$courses_search][0]->course == $times[$slots_search][0]->course
				&& $courses[$courses_search][0]->course == $days[$days_search][0]->course) {
					// Well hot-damn ma; we gots us a bingo on our hands
					$bingo = true;
				}
		} else {
                    //times is an array of arrays of Bookings objects
                    $this->data['facets'][] = array("facet_name" => "Days Facet", "groups" => (array)$this->timetable->get_bookings_by_day(null));
                    $this->data['facets'][] = array("facet_name" => "Times Facet", "groups" => (array)$this->timetable->get_bookings_by_time(null));
                    $this->data['facets'][] = array("facet_name" => "Courses Facet", "groups" => (array)$this->timetable->get_bookings_by_course(null));
		}
        
        	
        $this->create_searchform();

        if ($bingo) {
                $this->data['searchresult'] .= "<div class = 'bingo'>BINGO</div><br>\n";
                $this->data['searchresult'] .= $timeFacet[0]['time'];
        }

        //$this->parser->parse('welcome', $this->data);
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

        $times = (array)$this->timetable->get_bookings_by_time($slots_search);
        $days = (array)$this->timetable->get_bookings_by_day($days_search);
        $courses = (array)$this->timetable->get_bookings_by_course($courses_search);

        // Check for search 'bingo' in a long, protracted, drawn-out and ugly way
        if (sizeof($courses[$courses_search]) == 1 
            && sizeof($times[$slots_search]) == 1 
            && sizeof($days[$days_search]) == 1
            && $courses[$courses_search][0]->course == $times[$slots_search][0]->course
            && $courses[$courses_search][0]->course == $days[$days_search][0]->course) 
        {
            // Well hot-damn ma; we gots us a bingo on our hands
            $bingo = true;
            $this->data['facets'][] = array("facet_name" => "Bingo", "groups" => $courses);
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
        $searchform .= form_dropdown('courses', $this->timetable->get_courses());
        $searchform .= form_dropdown('slots', $this->timetable->get_timeslots());
        $searchform .= form_submit('searchbutton', 'Search');
        $searchform .= form_close();

        $this->data['searchform'] = $searchform;
    }
}