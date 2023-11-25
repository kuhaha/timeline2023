<?php 
date_default_timezone_set('Asia/Tokyo');
require 'lib/Timeline.php';
include 'dev/func.php';
include 'dev/database.php';

include 'pg_header.php';

echo '<h3>Daily Timeline</h3>';

$start = new DateTimeImmutable('2023-12-21 1:00');
$end = $start->add(new DateInterval('P1D')); // period=1Day
$tbl = new Timeline($start, $end, 2);    // tk=2Hours pb=40Minutes
echo $tbl->draw($daily_events);
_print($daily_events);

echo '<h3>Weekly Timeline</h3>';

$start =new DateTimeImmutable('2023-11-26');
$end = $start->add(new DateInterval('P1W'));// period=1Week 
$tbl =new Timeline($start, $end, 24); // tk=1Day pb=8Hours
echo $tbl->draw($weekly_events, 'n/d(D)', 'n/d H:i', true);
_print($weekly_events);

echo '<h3>Monthly Timeline</h3>';

$start =new DateTimeImmutable('2023-11-1');
$end = $start->add(new DateInterval('P32D'));// period=32Days
$tbl = new Timeline($start, $end,72); // tk=3Days, pb=24Hours
echo $tbl->draw($monthly_events, 'n/d(D)', 'n/d', true);
_print($monthly_events);

include 'pg_footer.php';