<?php
namespace exussum12\Ical;

class IcsUrl extends Ics
{
    public function __construct($calendar)
    {
       if (strpos($calendar, "http") !== 0) {
           throw new InvalidArgumentException();
       }

        $calendar = file_get_contents($calendar);

        parent::__construct($calendar);
    }

}
