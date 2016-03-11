<?php

/**
 * 
 * @author Joe
 */
class Timetable extends CI_Model {
    protected $xml;
    protected $courseFacet;
    protected $dayFacet;
    protected $timeFacet;
    
    public function __construct() {
        $this->xml = simplexml_load_file(DATAPATH . 'master.xml');
        $this->timeFacet = array();
        $this->courseFacet = array();
        $this->dayFacet = array();
        
        
        foreach($this->xml->timeslot as $slot) {
            $this->timeFacet[(string)$slot['time']] = array();
            foreach($slot->class as $cls) {
                $this->timeFacet[(string)$slot['time']][] = new Booking($cls);
            }
        }

        foreach($this->xml->course as $course) {
            $this->courseFacet[(string)$course['coursecode']] = array();
            foreach($course->class as $cls) {
                $this->courseFacet[(string)$course['coursecode']][] = new Booking($cls);
            }
        }
        
        foreach($this->xml->dayofweek as $day) {
            $this->dayFacet[(string)$day['day']] = array();
            foreach($day->class as $cls) {
                $this->dayFacet[(string)$day['day']][] = new Booking($cls);
            }
        }
    }
    
    public function get_bookings_by_day() {
        return $this->dayFacet;
    }
    
    public function get_bookings_by_time() {
        return $this->timeFacet;
    }
    
    public function get_bookings_by_course() {
        return $this->courseFacet;
    }
}

class Booking {
    public $day;
    public $starttime;
    public $endtime;
    public $room;
    public $instructor;
    public $course;
    
    public function __construct($class) {
        $this->day = (string) $class['day'];
        $this->starttime = (string) $class['starttime'];
        $this->endtime = (string) $class['endtime'];
        $this->room = (string) $class->room;
        $this->instructor = (string) $class->instructor;
        $this->course = (string) $class['coursecode'];
    }
}

