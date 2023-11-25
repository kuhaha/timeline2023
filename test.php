<?php 
date_default_timezone_set('Asia/Tokyo');
require 'lib/Timeline.php';
include 'dev/database.php';

include 'pg_header.php';
?>

<h3>Daily Timeline</h3>
<?php
    $start =new DateTimeImmutable('2023-12-21 1:00');
    $end = $start->add(new DateInterval('P1D'));
    $tbl = new Timeline($start, $end, 2, 10); // 2Hours :: 10Minutes
?>
<table class="table table-bordered" style="width: 100%; table-layout:fixed;">
<tr class="text-left"><?= $tbl->timetick() ?></tr>
<tr><?= $tbl->timeline($daily_events)?></tr>
</table>

<h3>Weekly Timeline</h3>
<?php 
    $start =new DateTimeImmutable('2023-11-26');
    $end = $start->add(new DateInterval('P1W'));
    $tbl =new Timeline($start, $end,24, 12*60); // 1Day :: 12Hours
?>
<table class="table table-bordered" style="width: 100%; table-layout:fixed;">
<tr class="text-left"><?= $tbl->timetick('n/d(D)') ?></tr>
<tr><?= $tbl->timeline($weekly_events,'n/d H:i', true)?></tr>
</table>

<h3>Monthly Timeline</h3>
<?php 
    $start =new DateTimeImmutable('2023-11-1');
    $end = $start->add(new DateInterval('P32D'));
    $tbl = new Timeline($start, $end,72, 12*60); // 3Days::12Hours
?>
<table class="table table-bordered" style="width: 100%; table-layout:fixed;">
<tr class="text-left"><?= $tbl->timetick('n/d(D)') ?></tr>
<tr><?= $tbl->timeline($monthly_events,'n/d H:i', true)?></tr>
</table>


<?php
include 'pg_footer.php';