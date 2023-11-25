<?php
/** Timeline class
 * Timeline is divided into Progress Bar Units (PBUnits) 
 *  p PBUnits form a Tick, thus
 *  1 Timeline = m Ticks = m * p PBUnits
 *  Event length is  to 1 Event = k PBUnits
************************************************/
class Timeline{
  private $start_date; // start datetime, Datetime object
  private $end_date;  // end datetime, Datetime object
  private $tk_unit;  // tick unit time in hours, int
  private $pb_unit;  // PBUnit time in minutes, int 

  function __construct($start, $end, $tk_unit_h, $pb_unit_m=0)
  {
    if ($pb_unit_m==0) $pb_unit_m = $tk_unit_h * 60;
    $this->start_date = $this->createDatetime($start);
    $this->end_date =  $this->createDatetime($end);
    $this->tk_unit = $this->createDateInterval('PT'.$tk_unit_h.'H');
    $this->pb_unit = $this->createDateInterval('PT'.$pb_unit_m.'M');
  }
  ////////////// MODEL //////////////
  function createDatetime($date)
  {
    if ($date instanceof DateTimeImmutable){ 
      return $date;
    }
    return new DateTimeImmutable($date);
  }
 
  function createDateInterval($period)
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
    $diff = $this->tk_unit;
    return round($diff->d * 24 * 60 + $diff->h * 60 + $diff->i);
  }

  /** getPBUnit(): return PBUnit time in minutes 
   **/ 
  function getPBUnit()
  {
    $diff = $this->pb_unit;
    return round($diff->d * 24 * 60 + $diff->h * 60 + $diff->i);
  }

  /** getTicks(): return a list of formatted datetime strings, e.g.,
   *  ['1:00', '3:00', ..., '23:00'], ['11/5(Sun)','11/6(Mon)',...] 
   **/
  function getTicks($format='H:i')
  {
    $ticks = [];
    $period = new DatePeriod($this->start_date, $this->tk_unit ,$this->end_date);
    foreach($period as $p){
      array_push($ticks, $p->format($format));
    }
    return $ticks;
  }
  ////////////// VIEW //////////////
  function timetick($format = 'H:i')
  {
    $width = round($this->getTKUnit() / $this->getPBUnit());
    $attr= sprintf('class="pl-0" colspan="%d"', $width);
    return self::tag('td', $this->getTicks($format), $attr);
  }
  /**
   * draw(): draw an ordered list of events as progress bars in a timeline
   * @param
   *   $events, list of events, ordered by 'start_time'. 
   *     each item is a key-value pair 'start_time'=>['end_time', content]
   *     ['2023-11-2 9:00'=>[2023-11-2 17:20, 'school'],'2023-12-12'=>[2023-12-13, 'trip']]
   *   $allow_partial, boolean, whether allow partial PBUnit 
   */
  function timeline($events, $format='H:i', $allow_partial=false)
  {
    $bar = ''; 
    $last_pbu = $partial_size = 0;
    foreach($events as $start => $data){
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
          $bar .= self::bar($_width, '', $cls) . PHP_EOL;
          $last_pbu += $_width;
          $partial_size = 0;
        }

        if ($date1_pbu > $last_pbu){ 
          // output a blank bar for a gap
          $_width = $date1_pbu - $last_pbu;
          $bar .= self::bar($_width, null, 'bg-blank');
        }
        // output the event bar
        $event = $data[1];
        $cls = self::cls();
        $content = $date1->format($format) . ' - ' . $date2->format($format) ;
        $bar .= self::bar($width, $content, $cls) . PHP_EOL;
        $last_pbu = $date2_pbu;
      }
    }
    return $bar;
  }
  
  static function bar($width, $content, $class='')
  {
    if ($width < 1) return '';
    $tooltip = $content ? sprintf('data-toggle="tooltip" title="%s"', $content): ''; 
    $pattern = '<div role="progressbar" class="progress-bar full-length %s" %s>%s</div>';
    $bar = sprintf($pattern, $class, $tooltip, $content);
    $bar = self::tag('div', $bar, 'class="progress"');
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