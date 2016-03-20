<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
			$times = (array)$this->timetable->get_bookings_by_time(null);
			$days = (array)$this->timetable->get_bookings_by_day(null);
			$courses = (array)$this->timetable->get_bookings_by_course(null);
			$this->data['searchresult'] = "";
		}
        
        $timeFacet = array();
        foreach($times as $time => $classes) {
            $timeclasses = array();
            $timeclasses['blocktype'] = $time;
            $timeclasses['manyClasses'] = array();
            foreach($classes as $booking) {
                $timeclasses['manyClasses'][] = array("specificClass" => $this->parser->parse('_class', (array)$booking, true));
            }
            $timeFacet[] = array("time" => $this->parser->parse('_classes', $timeclasses, true));
        }
        $this->data['times'] = $timeFacet;
        
        $dayFacet = array();
        foreach($days as $day => $classes) {
            $dayclasses = array();
            $dayclasses['blocktype'] = $day;
            $dayclasses['manyClasses'] = array();
            foreach($classes as $booking) {
                $dayclasses['manyClasses'][] = array("specificClass" => $this->parser->parse('_class', (array)$booking, true));
            }
            $dayFacet[] = array("day" => $this->parser->parse('_classes', $dayclasses, true));
        }
        $this->data['days'] = $dayFacet;
        
        $courseFacet = array();
        foreach($courses as $course => $classes) {
            $courseclasses = array();
            $courseclasses['blocktype'] = $course;
            $courseclasses['manyClasses'] = array();
            foreach($classes as $booking) {
                $courseclasses['manyClasses'][] = array("specificClass" => $this->parser->parse('_class', (array)$booking, true));
            }
            $courseFacet[] = array("course" => $this->parser->parse('_classes', $courseclasses, true));
        }
        $this->data['courses'] = $courseFacet;
        
		
		// Display form
		$searchform = form_open('welcome');
		$searchform .= form_dropdown('days', $this->timetable->get_days());
		$searchform .= form_dropdown('courses', $this->timetable->get_courses());
		$searchform .= form_dropdown('slots', $this->timetable->get_timeslots());
		$searchform .= form_submit('searchbutton', 'Search');
		$searchform .= form_close();
		
		$this->data['searchform'] = $searchform;
		
		if ($bingo) {
			$this->data['searchresult'] .= "<div class = 'bingo'>BINGO</div><br>\n";
			$this->data['searchresult'] .= $timeFacet[0]['time'];
		}
		
		$this->parser->parse('welcome', $this->data);
    }
	
	public function search() { // $course, $day, $slot
		echo "holler";
	}
}