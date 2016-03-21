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
    
    public function get_bookings_by_day($day, $slot) {
		if ($day == null || $slot == null) {
			return $this->dayFacet;
		}
                $result = array();
                foreach($this->dayFacet as $key => $val) {
                        if ($key == $day) {
                                foreach($val as $booking) {
                                    if (strcmp($booking->starttime, $slot) == 0) {
                                        $result[] = $booking;
                                    }
                                }
                        }
                }
                $temp = array();
                if (sizeof($result) > 0) {
                    $temp[$result[0]->day] = $result;
                }
                return $temp;
    }
    
    public function get_bookings_by_time($day, $slot) {
		if ($slot == null || $day == null) {
			return $this->timeFacet;
		}
                $result = array();
                foreach($this->timeFacet as $key => $val) {
                        //$matches = array();
                        if ($key == $slot) {
                                foreach($val as $booking) {
                                    if (strcmp($booking->day, $day) == 0) {
                                        $result[] = $booking;
                                    }
                                }
                        }
                }
                $temp = array();
                if (sizeof($result) > 0) {
                    $temp[$result[0]->starttime] = $result;
                }
                return $temp;
    }
    
    public function get_bookings_by_course($day, $slot) {
		if ($day == null || $slot == null) {
			return $this->courseFacet;
		} 
                $result = array();
                foreach($this->courseFacet as $val) {
                        foreach($val as $booking) {
                            if (strcmp($booking->day, $day) == 0 && strcmp($booking->starttime, $slot) == 0) {
                                $result[] = $booking;
                            }
                        }
                }
                $temp = array();
                if (sizeof($result) > 0) {
                    $temp[$result[0]->course] = $result;
                }
                return $temp;
		
    }
	
	
	// Get codes for days/courses/timeslots for use in search drop-downs
	public function get_days() {
		$result = array();
		foreach($this->dayFacet as $day => $contents) {
			$result[(string)$day] = (string)$day;
		}
		return $result;
	}
	
        public function get_courses() {
		$result = array();
		foreach($this->courseFacet as $course => $contents) {
			$result[(string)$course] = (string)$course;
		}
		return $result;
	}
	
        public function get_timeslots() {
		$result = array();
		foreach($this->timeFacet as $timeslot => $contents) {
			$result[(string)$timeslot] = (string)$timeslot;
		}
		return $result;
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

