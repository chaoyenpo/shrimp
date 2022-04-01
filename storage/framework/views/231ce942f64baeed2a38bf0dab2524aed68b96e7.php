<?php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');
    $bet_result = $game->results()->select('user_id', \DB::raw('SUM(point) as sum_point'))
    ->orderBy('sum_point', 'DESC')
    ->groupBy('user_id')
    ->get();

    $mvps = [];
    foreach ($bet_result as $result) {
        if (empty($mvps)) {
            $mvps = [$result];
        } else {
            if ($mvps[0]->sum_point == $result->sum_point) {
                $mvps[] = $result;
            }
        }
    }

    $pre_champions = [];
    foreach ([[1,$game->people_num/2], [$game->people_num/2+1,$game->people_num]] as $number) {
        $pre_champion = $game->results('round1', $number)
        ->where(function ($query)
         {
              $query->where('is_pk_win', 1)
                    ->where('result', '冠軍PK')
                    ->orWhere('result', 1);
         })
        ->first();
        $pre_champions[] = $pre_champion;
    }

?>


<div class="table-responsive">
<h4>比賽結果</h4>

<?php $__currentLoopData = $game->results('final', [1,$game->people_num/2])->where('result', '<=', 5)->orderBy('result', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<h6><?php echo e($result->resultText('final')); ?>: <?php echo e($result->user->nicknameWithPhone()); ?></h6>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<h6>MVP: 
    <?php $__currentLoopData = $mvps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mvp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo e($mvp->user->nicknameWithPhone()); ?><?php if(count($mvps) - 1 != $key): ?>、<?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</h6>

<?php $__currentLoopData = $pre_champions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pre_champion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<h6>預賽冠軍: <?php echo e($pre_champion->user->nicknameWithPhone()); ?></h6>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<br>
<h6>總斬蝦數: <?php echo e($game->results()->sum('point')); ?></h6>
</div><?php /**PATH /volume/project/Shrimp/resources/views/games/mobile/show_mvp.blade.php ENDPATH**/ ?>