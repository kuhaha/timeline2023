<?php 
date_default_timezone_set('Asia/Tokyo');
require 'lib/Timeline.php';
include 'dev/func.php';
include 'dev/database.php';

include 'pg_header.php';
?>

<h2>Timeline Test Cases</h2>
<h3>Daily Timeline</h3>

Create a Timeline for <code>['1:00', '23:00']</code> with <code>2H</code> hour tick unit<br> 
<code>$tbl = new Timeline('1:00', '23:00 ', 2);</code><br/>
<code>$tbl->addEventList($event_list);</code>
<p>
<code>echo $tbl->draw(); // Use defaults</code>
<?php
$event_list = $daily_events;
$start = new DateTimeImmutable('2023-12-21 1:00');
$end = $start->add(new DateInterval('P1D')); // period=1Day
$tbl = new Timeline($start, $end, 2);    // tk=2Hours pb=tk/3=40Minutes
$tbl->addEventList($event_list);
echo $tbl->draw();
_print($event_list);
?>

<h3>Weekly Timeline</h3>
Create a Timeline for <code>['2023-11-26', '2023-12-02']</code> with <code>24H=1D</code> hour tick unit<br> 
<code>$tbl = new Timeline('2023-11-26', '2023-12-02', 24);</code><br/>
<code>$tbl->addEventList($event_list);</code><br/>
<code>$allow_partial = true;</code><br/>
<code>$tk_format = 'n/d(D)'; // 12/1(Fri)</code><br/>
<code>$pb_format = 'n/d H:i';// 12/1 11:10</code><br/>
<p>
<code>echo $tbl->draw($tk_format, $pb_format, $allow_partial);</code>

<?php
$event_list = $weekly_events;
$start = new DateTimeImmutable('2023-11-26');
$end = $start->add(new DateInterval('P1W'));// period=1Week 
$tbl = new Timeline($start, $end, 24); // tk=1Day pb=tk/3=480Minutes
$tbl->addEventList($event_list);

$allow_partial = true;
$tk_format = 'n/d(D)'; // 12/1(Fri)
$pb_format = 'n/d H:i';// 12/1 11:10
echo $tbl->draw($tk_format, $pb_format, $allow_partial);
_print($event_list);
?>

<h3>Monthly Timeline</h3>
Create a Timeline for <code>['2023-11-01', '2023-12-01']</code> with <code>72H=3D</code> hour tick unit<br> 
<code>$tbl = new Timeline('2023-11-01', '2023-12-01', 72);</code><br/>
<code>$tbl->addEventList($event_list);</code><br/>
<code>$allow_partial = true;</code><br/>
<code>$tk_format = 'n/d(D)'; // 12/1(Fri)</code><br/>
<code>$pb_format = 'n/d';// 12/1</code><br/>
<p>
<code>echo $tbl->draw($tk_format, $pb_format, $allow_partial);</code>
<?php
$event_list = $monthly_events;
$start =new DateTimeImmutable('2023-11-1');
$end = $start->add(new DateInterval('P32D'));// period=32Days
$tbl = new Timeline($start, $end, 72); // tk=3Days, pb=tk/3=1440Minutes
$tbl->addEventList($event_list);

$allow_partial = true;
$tk_format = 'n/d(D)';
$pb_format = 'n/d';
echo $tbl->draw($tk_format, $pb_format, $allow_partial);
_print($event_list);

include 'pg_footer.php';