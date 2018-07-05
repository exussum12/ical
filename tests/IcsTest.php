<?php

use exussum12\Ical\Ics;
use exussum12\Ical\IcsFile;
use PHPUnit\Framework\TestCase;

class IcsTest extends TestCase
{
    public function testSampleIcal()
    {
        $ics = new IcsFile(__DIR__ . '/fixtures/sample.ics');
        $this->assertSame("2.0", $ics->version);

        $events = $ics->getEvents();
        $this->assertCount(2, $ics->getEvents());

        $this->assertSame(
            "Access-A-Ride to 900 Jay St.\, Brooklyn",
            $events[0]->description
        );

    }

    public function testExceptionThrownIfFileDoesntExist()
    {
        $this->expectException(InvalidArgumentException::class);
        new IcsFile('doesntExist.ics');
    }

    public function testInvalidFile()
    {
        $this->expectException(InvalidArgumentException::class);
        new Ics('Some Random String');
    }

    public function testNoOverlap()
    {
        $ics = new IcsFile(__DIR__ . '/fixtures/overlap.ics');
        $ics->linerizeCalendar();
        $events = $ics->getEvents();

        $this->assertSame('2013-08-02', $events[0]->getStart()->format('Y-m-d'));
        $this->assertSame('2013-08-05', $events[0]->getEnd()->format('Y-m-d'));
        $this->assertSame(
            "To see detailed information for automatically created events like" .
            " this one\, use the official Google Calendar app.",
            $events[0]->description);

    }
}
