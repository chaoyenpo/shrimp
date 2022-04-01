<?php $__env->startSection('content'); ?>

<h2>比賽內容</h2>

<div class="row mb-3">
    <div class="col-md-3">比賽資訊</div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">比賽場地：<?php echo e($game->shrimpFarm->name); ?></div>
            <div class="col-md-6">比賽分區：<?php echo e($game->location_catrgory); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽名稱：<?php echo e($game->name); ?></div>
            <div class="col-md-6">比賽編號：<?php echo e($game->identifier); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">協辦社團：<?php echo e($game->community); ?></div>
            <div class="col-md-6">贊助商：<?php echo e($game->sponsor); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽人數：<?php echo e($game->people_num); ?>（保留名額：<?php echo e($game->host_quota); ?>）</div>
            <div class="col-md-6">報名人數：<?php echo e($game->members(['ok','waiting'])->count()); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽日期：<?php echo e($game->begin_at); ?></div>
            <div class="col-md-6">比賽狀態：<?php echo e($game->statusText()); ?></div>
        </div>
        <div class="row">
            <div class="col-md-12">備註：<?php echo e($game->note); ?></div>
        </div>
    </div>
</div>

<?php if($game->members(['host_main_personnel','host_personnel'], $user->id)->first()): ?>

    <h2>報名比賽</h2>

    <div class="alert alert-warning" role="alert">工作人員不能報名比賽。</div>
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
        </div>
    </div>

<?php elseif($game->members(['ok','waiting'], $user->id)->first()): ?>

    <h2>取消報名比賽</h2>

    <?php if(!empty($result)): ?>
        <div class="alert alert-warning" role="alert"><?php echo e($result); ?></div>
    <?php endif; ?>
    <div class="row mb-3">
        <div class="col-md-12">確定要取消報名嗎？將扣除 100 點手續費，退還 <?php echo e(env('SIGNUP_PRICE')-100); ?> 點報名費</div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <form method="POST">
                <input type="hidden" name="api_token" value="<?php echo e($user->api_token); ?>">
                <input type="hidden" name="imei" value="<?php echo e($user->imei); ?>">
                <input type="hidden" name="type" value="quit">
                <button type="submit" class="btn btn-primary">確定</button>
                <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
            </form>
        </div>
    </div>

<?php else: ?>

    <h2>報名比賽</h2>

    <?php if(!empty($result)): ?>
        <div class="alert alert-warning" role="alert"><?php echo e($result); ?></div>
    <?php endif; ?>
    <div class="row mb-3">
        <div class="col-md-12">確定要報名嗎？將扣除 <?php echo e(env('SIGNUP_PRICE')); ?> 點報名費</div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <form method="POST">
                <input type="hidden" name="api_token" value="<?php echo e($user->api_token); ?>">
                <input type="hidden" name="imei" value="<?php echo e($user->imei); ?>">
                <input type="hidden" name="type" value="join">
                <button type="submit" class="btn btn-primary">確定</button>
                <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>" class="btn btn-primary" role="button" aria-pressed="true">返回</a>
            </form>
        </div>
    </div>

<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('activities.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/games/mobile/signup.blade.php ENDPATH**/ ?>