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
    public function index()
    {   
        $this->load->model('timetable');
        //times is an array of arrays of Bookings objects
        $times = (array)$this->timetable->get_bookings_by_time();
        $days = (array)$this->timetable->get_bookings_by_day();
        $courses = (array)$this->timetable->get_bookings_by_course();
        
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
        
        $this->parser->parse('welcome', $this->data);
        
    }
}