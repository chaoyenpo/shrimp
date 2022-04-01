<?php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');
?>
<div class="table-responsive">
<table class="table table-bordered sigupListTable">
    <thead>
        <tr>
            <th width="5%" class="text-center">#</th>
            <th width="25%" class="text-center">電話</th>
            <th class="text-left">協辦負責人</th>
            <th class="text-left">職位</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $game->members(['host_main_personnel'])->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr <?php if($user && $member->user_id == $user->id): ?> class="bg-self" <?php elseif($member->user_id == $user_id): ?> class="bg-target" <?php endif; ?>>
                <td class="text-center small"><?php echo e($loop->iteration); ?></td>
                <td class="text-center small">
                    <?php echo e($member->user->phone); ?>

                </td>
                <td class="text-left">
                    <div class="sigupList">
                        <div class="avatarContainer">
                           <img src="<?php echo e($member->user->photo); ?>"  class="rounded-circle avatar"> 
                        </div>
                        <div class="userInfo">
                            <p>
                                <?php echo e($member->user->nicknameWithPhone()); ?>

                            </p>
                        </div>
                    </div>
                </td>
                <td>
                    主工作人員
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
</div><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/games/mobile/show_host.blade.php ENDPATH**/ ?>