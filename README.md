#iCal Parser

A basic iCal parser. 

## Install
preferred method is Composer

composer require exussum12/ical

## Use

in your PHP file, there are a few ways to call, depending on where your iCal is stored.

Eg iCal over HTTP

```
$cal = new exussum12\Ical\IcsUrl('http://yourdomain/ical.ics')
foreach ($cal->getEvents() as $event) {
    var_dump ($event->getStart());
}  
```
