<?php
namespace exussum12\Ical;

use DateTimeImmutable;

class Event {
    private $processed = [];

    public function __construct(array $lines) {
        $last = "";
        for($i=1,$count = count($lines) -1;$i< $count; $i++){
            if($lines[$i][0] === " ") { //append to last line
                if (is_array($this->$last)) {
                    $tmp = array_pop($this->$last);
                    $tmp .= substr($lines[$i], 1);
                    array_push($this->$last, $tmp);
                } else {
                    $this->$last .= substr($lines[$i], 1);
                }
            } else if(strpos($lines[$i], ":") !== false) {
                list($tok, $value) = explode(":", $lines[$i],2);
                $tok = explode(";", $tok,2);
                $tok = strtolower($tok[0]);
                $value = ltrim($value);
                $this->$tok = $value;
                $last = $tok;
            } else {
                //its likely to be ";" seperated, take the first part as the array
                list($tok, $value) = explode(";", $lines[$i],2);
                $tok = strtolower($tok);
                if (!isset($this->$tok) || !is_array($this->$tok)){
                    $this->$tok = array();
                }
                array_push($this->$tok , $value);
                $last = $tok;
            }
        }

    }

    public function getStart() {
        if(!empty($this->processed['dtstart'])){
            return $this->processed['dtstart'];
        }

        return $this->processed['dtstart'] =
            new DateTimeImmutable($this->dtstart);
    }

    public function getEnd() {
        if(isset($this->processed['dtend'])){
            return $this->processed['dtend'];
        }
        return $this->processed['dtend'] =
            new DateTimeImmutable($this->dtend);
    }

    public function getCreated() {

        if(isset($this->processed['dtstamp'])){
            return $this->processed['dtstamp'];
        }
        return $this->processed['dtstamp'] =
            new DateTimeImmutable($this->dtstamp);
    }
}

