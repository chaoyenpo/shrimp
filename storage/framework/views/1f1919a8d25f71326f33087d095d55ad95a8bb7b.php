<?php $__env->startSection('content'); ?>

<?php
    $request = \Illuminate\Support\Facades\Request::instance();
    $api_token = $request->get('api_token');
    $imei = $request->get('imei');
    $user = \App\Models\Profile\Entities\User
                ::where('api_token', $api_token)
                ->where('imei', $imei)
                ->first();
?>

<h2>個人賽</h2>

<div class="row">
    <div class="col-md-12 my-3 px-4">
        <select class="form-control" id="status">
            <option value="end">已結束-已結束比賽</option>
            <option value="notend">進行中-未結束比賽</option>
        </select>
    </div>
</div>

<script>
$(function(){
    <?php if(isset($status)): ?>
        $("#status").val("<?php echo e($status); ?>");
    <?php endif; ?>
    $("#status").change(function(){
        location.href = "<?php echo e(url('/activities/game/single')); ?>/"+$(this).val();
    });
});
</script>

<div class="row">
    <div class="col-md-12 my-3 px-4">
        <div class="list-group gamesList">
        <?php $__empty_1 = true; $__currentLoopData = $games; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <!-- <hr size="1px" align="center" width="100%"> -->
            <?php if($user): ?>
                <?php if(in_array($user['id'], $game['host_main_personnel_ids']) || in_array($user['id'], $game['game_member_ids'])): ?>
                <a class="list-group-item bg-warning list-group-item-action" href="<?php echo e(url('/api/game')); ?>/<?php echo e($game['identifier']); ?>?api_token=<?php echo e($api_token); ?>&imei=<?php echo e($imei); ?>">
                <?php else: ?>
                <a class="list-group-item list-group-item-action" href="<?php echo e(url('/api/game')); ?>/<?php echo e($game['identifier']); ?>?api_token=<?php echo e($api_token); ?>&imei=<?php echo e($imei); ?>">
                <?php endif; ?>
                <p>編號：<?php echo e($game['identifier']); ?></p>
            <?php else: ?>
                <a class="list-group-item list-group-item-action" href="<?php echo e(url('/api/game')); ?>/<?php echo e($game['identifier']); ?>">
                <p>編號：<?php echo e($game['identifier']); ?></p>
            <?php endif; ?>
                <p>日期：<?php echo e(explode(' ', $game['begin_at'])[0]); ?></p>
                <p>名稱：<?php echo e($game['name']); ?></p>
                <p>場地：<?php echo e($game['shrimp_farm']); ?></p>
                <p>協辦：<?php echo e($game['community']); ?></p>
                <p>報名人數：<?php echo e($game['people_now']); ?> / <?php echo e($game['people_num']); ?></p>
                <p>分區：<?php echo e($game['location_catrgory']); ?></p>
                <p>狀態：<?php echo e($game['statusText']); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>目前無任何比賽</p>
        <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('activities.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/activities/game/single.blade.php ENDPATH**/ ?>