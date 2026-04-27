@if($progress <=  30 || $progress == NULL) 
   <strong>الوحدة 1- 3</strong>
 @elseif ($progress == 50) 
    <strong>الوحدة 3 - 6</strong>
@elseif ($progress == 75) 
    <strong>الوحدة 6 - 9</strong>
@else 
    <strong>الوحدة الاخيرة</strong>

@endif
<div class="progress">
    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?=  $progress != null ? $progress : 0?>% ;color: #FF9800;"
        aria-valuenow="<?=  $progress != null ? $progress : 0?>" aria-valuemin="0" aria-valuemax="100"><?=  $progress != null ? $progress : 0?>%</div>
</div>
