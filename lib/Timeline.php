<?php
/** Timeline class
 * Timeline is divided into Progress Bar Units (PBUnits) 
 *  p PBUnits form a Tick, thus
 *  1 Timeline = m Ticks = m * p PBUnits
 *  1 Event = k PBUnits
************************************************/

class Timeline{
  private $start_date; // start datetime, Datetime object or string
  private $end_date;  // end datetime, Datetime object  or string
  private $tk_unit_h;  // Tick unit in hours, int, default 2
  private $pb_unit_m;  // PBUnit in minutes, int. default 0, means $pb_unit = 20 * $tk_unit
  private $event_lists; // list of event_lists, each event_list corresponds to a timeline
 
  function __construct($start, $end, $tk_unit_h=2, $pb_unit_m=0)
  {
    $this->start_date = self::createDatetime($start);
    $this->end_date =  self::createDatetime($end);
    if ($pb_unit_m == 0) $pb_unit_m = 20 * $tk_unit_h;
    $this->tk_unit_h = $this->createDateInterval('PT' . $tk_unit_h . 'H');
    $this->pb_unit_m = $this->createDateInterval('PT' . $pb_unit_m . 'M');
    $this->event_lists = [];
  }
 
  ////////////// MODEL //////////////
  function addEventList($event_list)
  {
    $this->event_lists += [$event_list];
  }

  static function createDatetime($date)
  {
    if ($date instanceof DateTimeImmutable){ 
      return $date;
    }
    return new DateTimeImmutable($date);
  }
 
  static function createDateInterval($period)
  {
    if ($period instanceof DateInterval) {
      return $period;
    }
    return  new DateInterval($period);
  }

  function getStartDate(){
    return $this->start_date;
  }

  function getEndDate(){
    return $this->end_date;
  }

  /** getMinutes(): return MINUTEs between two dates  
   * @param $diff, DateInterval object  
   **/ 
  function getMinutes($date1, $date2 = null)
  {
    if (!$date2) $date2 = $this->start_date;
    $diff= abs($date1->getTimestamp() - $date2->getTimestamp()) / 60.0;
    return round($diff);
  }

  /** getPBUnits(): return PBUnits between two dates
   * @param
   *   $date1, $date2, Datetime objects 
   *   $fullness, int, threshold of partial PBUnit
   **/ 
  function getPBUnits($date1, $date2 = null)
  {
    return round(0.0 + $this->getMinutes($date1, $date2) / $this->getPBUnit());  
  }

  /** getTKUnit(): return Tick unit time in minutes 
   **/ 
  function getTKUnit()
  {
    $diff = $this->tk_unit_h;
    return round($diff->d * 24 * 60 + $diff->h * 60 + $diff->i);
  }

  /** getPBUnit(): return PBUnit time in minutes 
   **/ 
  function getPBUnit()
  {
    $diff = $this->pb_unit_m;
    return round($diff->d * 24 * 60 + $diff->h * 60 + $diff->i);
  }

  /** getTicks(): return a list of formatted datetime strings, e.g.,
   *  ['1:00', '3:00', ..., '23:00'], ['11/5(Sun)','11/6(Mon)',...] 
   **/
  function getTicks($format='H:i')
  {
    $ticks = [];
    $period = new DatePeriod($this->start_date, $this->tk_unit_h ,$this->end_date);
    foreach($period as $p){
      array_push($ticks, $p->format($format));
    }
    return $ticks;
  }
  ////////////// VIEW //////////////
  function draw($tick_format='H:i', $pbu_format='H:i', $allow_partial=false){
    $tbl_css = 'class="table table-bordered" style="width: 100%; table-layout:fixed;"';
    $tbl = self::tag('tr', $this->timetick($tick_format), 'class="text-left"');
    foreach ($this->event_lists as $event_list){
      $tbl.= self::tag('tr', $this->timeline($event_list, $pbu_format, $allow_partial));
    }
    return self::tag('table', $tbl, $tbl_css);

  }
  function timetick($format = 'H:i')
  {
    $width = round($this->getTKUnit() / $this->getPBUnit());
    $attr= sprintf('class="pl-0" colspan="%d"', $width);
    return self::tag('td', $this->getTicks($format), $attr);
  }
  /**
   * timeline(): build a timeline for the event list
   * @param
   *   $event_list, array, of events, ordered by 'start_time'. 
   *     each item is a key-value pair 'start_time'=>['end_time', content], such as
   *     ['2023-11-2 9:00'=>[2023-11-2 17:20, 'school'],'2023-12-12'=>[2023-12-13, 'trip']]
   *   $format, string, time format
   *   $allow_partial, boolean, whether allow partial PBUnit 
   */
  function timeline($event_list, $format='H:i', $allow_partial=false)
  {
    $bar = ''; 
    $last_pbu = $partial_size = 0;
    foreach($event_list as $start => $data){
      $date1 = new DateTimeImmutable($start); 
      $date2 = new DateTimeImmutable($data[0]);
      if ($date1 > $this->end_date or $date2 < $this->start_date or $date1>=$date2){
        continue;
      }
      $date1_pbu = $this->getPBUnits($date1);
      $date2_pbu = $this->getPBUnits($date2);
      $date1_minute = $this->getMinutes($date1);
      $date2_minute = $this->getMinutes($date2);      
      $width = $date2_pbu - $date1_pbu;
  
      // process partial PBUnits
      $width_minute = $date2_minute - $date1_minute;
      if ($width == 0 and $width_minute > 0 and $allow_partial){
        $partial_size += $width_minute;
      } 
      
      if ($width > 0){
        if ($partial_size > 0){ 
          // output event bar for the partial PBUnits
          $_width = ceil($partial_size / $this->getPBUnit());
          $cls = self::cls() . ' ' . 'progress-bar-striped';
          $bar .= self::bar($_width, $cls, '△', '一部予約有り') . PHP_EOL;
          $last_pbu += $_width;
          $partial_size = 0;
        }

        if ($date1_pbu > $last_pbu){ 
          // output a blank bar
          $_width = $date1_pbu - $last_pbu;
          $bar .= self::bar($_width, 'bg-blank', '〇', '予約可能');
        }
        // output the event bar
        $event = $data[1];
        $cls = self::cls();
        $msg = $date1->format($format) . ' - ' . $date2->format($format) ;
        $bar .= self::bar($width, $cls, $event, $msg) . PHP_EOL;
        $last_pbu = $date2_pbu;
      }
    }
    $end_pbu = $this->getPBUnits($this->end_date);
    if ($end_pbu > $last_pbu){ // output a blank bar
      $_width = $end_pbu - $last_pbu;
      $bar .= self::bar($_width, 'bg-blank', '〇', '予約可能');
    }
    return $bar;
  }
  
  static function bar($width, $class='', $content, $tooltip=null)
  {
    if ($width < 1) return '';
    $tooltip = $tooltip ? sprintf('data-toggle="tooltip" title="%s"', $tooltip): ''; 
    $pattern = '<div role="progressbar" class="progress-bar rounded full-length %s" %s>%s</div>';
    $bar = sprintf($pattern, $class, $tooltip, $content);
    // $bar = self::tag('div', $bar, 'class="progress"');
    return self::tag('td', $bar, 'class="pl-0 pr-0" colspan='.$width) . PHP_EOL; 
  }

  /** cls(): define color class of progress bar (for Bootst rap 4+)   
   *  Currently just choose the next color in the list
   *  TODO: define color based on event type, status, ...
   *  For Bootstrap 3, $prefix is 'progress-bar-' instead of 'bg-' 
   **/
  private static function cls($prefix = 'bg-', $start='', $end='', $content='')
  {
    static $id = 0;
    $class = ['primary', 'success', 'danger', 'info']; 
    $k = count($class);
    return $prefix . $class[$id++ % $k];
  }

  private static function _q($value){
    return '"'. $value .'"';
  }
  /** tag(): enclose content by tags 
   **/
  public static function tag($tagname, $content, $attributes = null)
  {
    $stag = '<' . $tagname . ' ' . $attributes . '>';
    $ctag = '</' . $tagname .'>' . PHP_EOL;
    $elements = '';
    if (is_array($content)) {
      foreach ($content as $k => $v) {
        $elements .= $stag. $v . $ctag;  
      }
    }else{
      return $stag . $content . $ctag;
    }
    return $elements;
  }
}