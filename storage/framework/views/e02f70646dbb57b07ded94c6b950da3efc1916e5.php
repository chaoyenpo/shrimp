<?php $__env->startSection('content'); ?>

<?php
    $request = \Illuminate\Support\Facades\Request::instance();
    $api_token = $request->get('api_token');
    $imei = $request->get('imei');
    $user = \App\Models\Profile\Entities\User
                ::where('api_token', $api_token)
                ->where('imei', $imei)
                ->first();
//$user = \App\Models\Profile\Entities\User::find(973);
?>

<h2><?php echo e($title); ?></h2>

<div style="margin: 0 10px;">
    <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <hr size="1px" align="center" width="100%">
        <?php if($record['id'] == $user['id']): ?>
        <div class="row align-items-center bg-warning">
        <?php else: ?>    
        <div class="row align-items-center">
        <?php endif; ?>
            <div class="rank">
                <?php if($type == 'integral'): ?>
                    <?php if($record['integral'] == 0): ?>
                        <h1>--</h1>
                    <?php else: ?>
                        <?php switch($record['rank']):
                            case (1): ?>
                                <img src="<?php echo e(asset('rank1.png')); ?>" alt="">
                                <?php break; ?>
                            <?php case (2): ?>
                                <img src="<?php echo e(asset('rank2.png')); ?>" alt="">
                                <?php break; ?>
                            <?php case (3): ?>
                                <img src="<?php echo e(asset('rank3.png')); ?>" alt="">
                             <?php break; ?>
                            <?php default: ?>
                                <h1><?php echo e($record['rank']); ?></h1>
                            <?php break; ?>
                        <?php endswitch; ?>
                    <?php endif; ?>
                <?php elseif($type == 'point'): ?>
                    <?php if($record['point'] == 0): ?>
                        <h1>--</h1>
                    <?php else: ?>
                    <?php switch($record['rank']):
                            case (1): ?>
                                <img src="<?php echo e(asset('rank1.png')); ?>" alt="">
                                <?php break; ?>
                            <?php case (2): ?>
                                <img src="<?php echo e(asset('rank2.png')); ?>" alt="">
                                <?php break; ?>
                            <?php case (3): ?>
                                <img src="<?php echo e(asset('rank3.png')); ?>" alt="">
                             <?php break; ?>
                            <?php default: ?>
                                <h1><?php echo e($record['rank']); ?></h1>
                            <?php break; ?>
                        <?php endswitch; ?>
                    <?php endif; ?>
                <?php elseif($type == 'champion'): ?>
                    <?php if($record['championCount'] == 0): ?>
                        <h1>--</h1>
                    <?php else: ?>
                    <?php switch($record['rank']):
                            case (1): ?>
                                <img src="<?php echo e(asset('rank1.png')); ?>" alt="">
                                <?php break; ?>
                            <?php case (2): ?>
                                <img src="<?php echo e(asset('rank2.png')); ?>" alt="">
                                <?php break; ?>
                            <?php case (3): ?>
                                <img src="<?php echo e(asset('rank3.png')); ?>" alt="">
                             <?php break; ?>
                            <?php default: ?>
                                <h1><?php echo e($record['rank']); ?></h1>
                            <?php break; ?>
                        <?php endswitch; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="col text-center" id="photo_wrap">
                <img src="<?php echo e($record['photo']); ?>"  class="rounded-circle avatar">
            </div>
            <div class="col">
                <?php if($user): ?>
                    <a href="<?php echo e(url('api/game/profile')); ?>/<?php echo e($record['id']); ?>?api_token=<?php echo e($api_token); ?>&imei=<?php echo e($imei); ?>"><?php echo e($record['nickname']); ?></a>
                <?php else: ?>
                    <a href="<?php echo e(url('api/game/profile')); ?>/<?php echo e($record['id']); ?>"><?php echo e($record['nickname']); ?></a>
                <?php endif; ?>
                <div>
                    <span><?php echo e($record['note']); ?></span>
                </div>
            </div>
            <div class="col">
                <?php if($type == 'integral'): ?>
                    <h1><?php echo e($record['integral']); ?>分</h1>
                <?php elseif($type == 'point'): ?>
                    <h1><?php echo e($record['point']); ?>斬</h1>
                <?php elseif($type == 'champion'): ?>
                    <h1><?php echo e($record['championCount']); ?>勝</h1>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <hr size="1px" align="center" width="100%">
        <div>
            目前尚無紀錄
        </div>
    <?php endif; ?>
</div>

<style>
#photo_wrap {
    max-height: 200px;
    max-width: 200px;
}
#photo_wrap img {
    max-height: 100%;
    max-width: 100%;
}
@media (min-width: 768px) {
    #photo_wrap {
        max-height: 400px;
        max-width: 400px;
    }
}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('activities.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/activities/game/rank.blade.php ENDPATH**/ ?>