<?php
namespace exussum12\Ical;

use InvalidArgumentException;

class Ics {
    protected $events;
    protected $validCalString = "begin:vcalendar";

    function __construct(string $calendar)
    {
        //check if its a val string
        if (stripos(trim($calendar), $this->validCalString) !== 0) {
            throw new InvalidArgumentException();
        }

        $this->parse($calendar);
    }

    function sort($sortby = "dtstart")
    {
        $sortBy = array();
        foreach ($this->events as $event) {
            $sortBy[] = $event->$sortby;
        }

        return array_multisort($sortBy, $this->events);
    }

    /**
     * @return Event[]
     */
    function getEvents()
    {
        return $this->events;
    }

    function linerizeCalendar(){
        $this->sort();
        $count = count($this->events);
        for($i = 1, $j = 0; $i < $count; $j = $i++){

            if (
                //check for dates overlap
                $this->events[$i]->dtstart <= $this->events[$j]->dtend &&
                $this->events[$i]->dtend >= $this->events[$j]->dtstart
            ){
                $this->events[$i]->dtstart = min($this->events[$i]->dtstart, $this->events[$j]->dtstart);
                $this->events[$i]->dtend = max($this->events[$i]->dtend, $this->events[$j]->dtend);
                unset($this->events[$j]);
            }
        }

        //reset the numbering
        $this->events = array_values($this->events);
    }

    protected function endsWith($haystack, $needle)
    {
        return strcasecmp(substr($haystack, -strlen($needle)), $needle) === 0;
    }

    protected function parse($calendar)
    {
        $offset = 0;
        $calendar = $this->getLines($calendar);
        $tmp = array();
        foreach ($calendar as $line => $calpart) {
            if (stripos($calpart, "begin") === 0) {
                $tmp[] = $line - $offset;
            } else if (stripos($calpart, "end") === 0) {
                //found that matching end brace, group them
                $start = array_pop($tmp);
                $count = $line - $offset - $start + 1;
                $section = array_splice($calendar, $start, $count);
                if ($this->endsWith($calpart, 'vevent')) {
                    $this->addEvent($section);
                } else if ($this->endsWith($calpart, "vcalendar")) {
                    $count = $this->addCalendarAttributes($section);
                }
                $offset += $count;
            }
        }
    }

    protected function addCalendarAttributes($section): int
    {
        for ($i = 1, $count = count($section) - 1; $i < $count; $i++) {
            list($tok, $value) = explode(":", $section[$i], 2);
            $tok = strtolower($tok);
            $this->$tok = $value;
        }
        return $count;
    }

    protected function addEvent($section)
    {
        $event = new Event($section);
        $this->events[] = $event;
    }

    /**
     * @param $calendar
     * @return array|mixed
     */
    protected function getLines($calendar)
    {
        $calendar = str_replace("\r\n", "\n", $calendar);
        $calendar = explode("\n", $calendar);
        return $calendar;
    }
}

