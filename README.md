# BsTimeline: A pure HTML timeline PHP library

# Terminology
- *Progress Bar Unit (PBUnit)*
- *Tick Unit*

## Display Units
```
Tick = p PBUnits
Row  = m Ticks  = p x m PBUnits
Event= q PBUnits 
```

Output: 
```html
<table class="table table-bordered" style="width: 100%; table-layout:fixed;">
<tr class="text-left"><td class="pl-0" colspan="12">01:00</td>
<td class="pl-0" colspan="12">03:00</td>
<td class="pl-0" colspan="12">05:00</td>
<td class="pl-0" colspan="12">07:00</td>
<td class="pl-0" colspan="12">09:00</td>
<td class="pl-0" colspan="12">11:00</td></tr>
<tr>
<td class="pl-0 pr-0" colspan=4>
    <div class="progress">
        <div role="progressbar" class="progress-bar full-length bg-blank" ></div>
    </div>
</td>
<td class="pl-0 pr-0" colspan=5>
    <div class="progress">
        <div role="progressbar" class="progress-bar full-length bg-primary" data-toggle="tooltip" title="hello, world!">37%</div>
    </div>
</td>
...
</tr>
</table>
```