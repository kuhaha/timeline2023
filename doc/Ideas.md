# Terminology
- *Progress Bar Unit (PBUnit)*
- *Tick Unit*

## Display Units
```
Tick = p PBUnits
Row  = m Ticks
     = p x m PBUnits
Event= q PBUnits 
HTML:  <td colspan="p">...</td>
```

## Calendar Units
```
Year = 4 Quarters [1:4]
     = 12 Months [1:12], [4:12,1:3]
     = 52+ Weeks [1:52]
     = 365+ Days [1:365] 
Quarter = 3 Months {[1:3], [4:6], [7:9], [10:12]}

Month= d Days [1:d]
Week = u Days [0:6],[d:d+6], [0:u],[d:d+u-1]

Day  = 24 Hours [0:23]
  HlfDay  = 12 Hours = {AM, PM}
     AM [0:11], PM [12:23]

Hour = 60 Minutes
  HlfHour = 30 Minutes
  QtrHour = 15 Minutes 
  TenMinute = 10 Minutes, Decuple = 10 folds
```
# Design
## Daily

2023年
12月4日(月)
--------------------------
1:00  3:00  5:00 ... 23:00　ticks = 12
--------------------------
1 ROW = 48 PBUits 
1 PBUnit = 1 Hour

## Weekly
A week is a set of days (default 7 days), starting from a fixed weekday (default Sunday)

### Weekly - Per Day
2023年
------------------------------------
11/26(Sun)  11/27(Mon) ... 12/2(Sun)
------------------------------------
1 ROW =  14 PBUnits
1 PBUnit = 1/2 Day

### Weekly - Half Day
2023年
------------------------------------
11/26(Sun)  11/27(Mon) ... 12/2(Sun)
AM  PM      AM  PM         AM  PM
------------------------------------
1 ROW = 14 PBUnits
1 PBUnit = 1 HlfDay


# Monthly
2023年
12月
--------------------------
1(Fri)  2(Sat) ... 31(Sun)
--------------------------
1 PBUnit =  Day
