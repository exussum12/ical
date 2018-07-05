<?php

use exussum12\Ical\Ics;

class icsUrlCached extends ics
{
    public $cacheTime = 60*60;
    private $update = true;
    public function __construct($calendar)
    {
        $cache = md5($calendar);
        $cacheFile = sys_get_temp_dir() . $cache;

        $calendar = $this->getCalendar($cacheFile, $calendar);

        parent::__construct($calendar);

        $this->updateCache($cacheFile, $calendar);
    }

    protected function getCalendar($cacheFile, $calendar)
    {

        if (file_exists($cacheFile) && filemtime($cacheFile) > (time() - ($this->cacheTime))) {
            $update = false;
           return file_get_contents($cacheFile);
        }

        return file_get_contents($calendar);
    }

    protected function updateCache($cacheFile, $calendar)
    {
        if ($this->update) {
            file_put_contents($cacheFile, $calendar);
        }
    }

}
