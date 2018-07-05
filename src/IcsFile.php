<?php
namespace exussum12\Ical;

use InvalidArgumentException;

class IcsFile extends Ics
{
    public function __construct($calendar)
    {

        if (!file_exists($calendar)) {
            throw new InvalidArgumentException();
        }
        $calendar = file_get_contents($calendar);
        parent::__construct($calendar);
    }
}
