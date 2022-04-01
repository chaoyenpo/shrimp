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



<div class="row mb-3">
    <div class="col-6 text-center">
        <img src="<?php echo e($profile->photo); ?>" class="rounded-circle profileAvatar">
    </div>
    <div class="col-6">
        <div class="row mb-3">
            <div class="col">
                <h2>
                    <?php echo e($profile->nicknameWithPhone()); ?>

                </h2>
                <p style="color: slategray"><?php echo e($profile->note); ?></p>
            </div>
            
        </div>
        <div class="row">
            
            <div class="col">有效積分：<?php echo e($profile->gameIntegral(52)); ?></div>
            <!-- <div class="col-md-9"><?php echo e($profile->gameIntegral(52)); ?></div> -->
        </div>
        <div class="row">
            <div class="col">累計積分：<?php echo e($profile->gameIntegral()); ?></div>
            <!-- <div class="col-md-9"><?php echo e($profile->gameIntegral()); ?></div> -->
        </div>
        <div class="row">
            <div class="col">斬蝦數：<?php echo e($profile->gamePoint()); ?></div>
            <!-- <div class="col-md-9"><?php echo e($profile->gamePoint()); ?></div> -->
        </div>
    </div>
</div>


<style>
#photo_wrap {
    max-height: 200px;
    max-width: 200px;
    padding-left: 30px;
}
#photo_wrap img {
    max-height: 100%;
    max-width: 100%;
}
@media (min-width: 768px) {
    #photo_wrap {
        max-height: 400px;
        max-width: 400px;
        padding-left: 30px;
    }
}
</style>

<h2>備戰賽事</h2>

<div class="row mb-3">
    <div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">賽事編號</th>
                    <th class="text-left">場地</th>
                    <th class="text-center">比賽日期</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $profile->gameMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center">
                        <a href="<?php echo e(url('api/game')); ?>/<?php echo e($record->game->identifier); ?>?api_token=<?php echo e($api_token); ?>&imei=<?php echo e($imei); ?>&user_id=<?php echo e($record->user_id); ?>"><?php echo e($record->game->identifier); ?></a>
                    </td>
                    <td class="text-left"><?php echo e($record->game->shrimpFarm->name); ?></td>
                    <td class="text-center"><div><?php echo e(explode(' ', $record->game->beginAtWithWeek())[0]); ?></div></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center">未參加任何賽事</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
</div>
    </div>
</div>

<h2>戰績</h2>

<div class="row mb-3">
    <div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">賽事編號</th>
                    <th class="text-left">場地</th>
                    <th class="text-center">成績</th>
                    <th class="text-center">斬蝦</th>
                    <th class="text-center">積分</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $profile->gameResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class='clickable-row' data-href="<?php echo e(url('api/game')); ?>/<?php echo e($record->game->identifier); ?>?api_token=<?php echo e($api_token); ?>&imei=<?php echo e($imei); ?>&user_id=<?php echo e($record->user_id); ?>">
                    <td class="text-center">
                       <?php echo e($record->game->identifier); ?>

                    </td>
                    <td class="text-left"><?php echo e($record->game->shrimpFarm->name); ?></td>
                    <td class="text-center"><?php echo e($record->result ?? '未得名'); ?></td>
                    <td class="text-center"><?php echo e($record->member()->results()->sum('point')); ?></td>
                    <td class="text-center"><?php echo e($record->integral); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center">未參加任何賽事</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
</div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('activities.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /volume/project/Shrimp/resources/views/games/mobile/profile.blade.php ENDPATH**/ ?>