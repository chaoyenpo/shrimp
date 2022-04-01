<?php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');
?>
<div class="table-responsive">
<table class="table table-bordered sigupListTable">
    <thead>
        <tr>
            <th width="5%" class="text-center">#</th>
            <th width="25%" class="text-center">報名時間</th>
            <th class="text-left">報名者</th>
            <?php if($host_personnel || $host_main_personnel): ?>
                <th width="10%" class="text-center">功能</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $status = ['ok','host_quota','pending'];
            if (in_array($game->status, ['sign_up', 'pay_up']))
                array_push($status, 'waiting');
        ?>
        <?php $__empty_1 = true; $__currentLoopData = $game->members($status)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $image = '';
                $key = $rank_list[$member->user_id] ?? 99 + 1;
                $key += 1;
                $image = '';
                if ($key == 1) {
                    $image = '閃電黑';
                } else if ($key >= 2 && $key <= 13) {
                    $image = '閃電紅';
                } else if ($key >= 14 && $key <= 30) {
                    $image = '閃電紫';
                }
            ?>
            <tr style="background-image: url('/<?php echo e($image); ?>.gif')" <?php if($user && $member->user_id == $user->id): ?> class="bg-self" <?php elseif($member->user_id == $user_id): ?> class="bg-target" <?php elseif(in_array($member->status, ['host_quota'])): ?> class="bg-success" <?php elseif(in_array($member->status, ['waiting'])): ?> style="background: #E0E0E0" <?php endif; ?>>
                <td class="text-center small"><?php echo e($loop->iteration); ?></td>
                <td class="text-center small">
                    <div>
                        <?php echo e(explode(' ', $member->register_at)[0]); ?>

                    </div>
                    <div>
                        <?php echo e(explode(' ', $member->register_at)[1]); ?>

                    </div>
                </td>
                <td class="text-left">
                    <div class="sigupList">
                        <div class="avatarContainer">
                           <img src="<?php echo e($member->user->photo); ?>"  class="rounded-circle avatar"> 
                        </div>
                        <div class="userInfo" style="font-size: 15px">
                            <p>
                                <?php if($user): ?>
                                    <a href="<?php echo e(url('api/game')); ?>/profile/<?php echo e($member->user_id); ?>?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>"><?php echo e(($host_personnel || $host_main_personnel) ? $member->user->nicknameWithFullPhone() : $member->user->nicknameWithPhone()); ?></a><br>
                                <?php else: ?>
                                    <a href="<?php echo e(url('api/game')); ?>/profile/<?php echo e($member->user_id); ?>"><?php echo e($member->user->nickname); ?></a><br>
                                <?php endif; ?>
                                <?php if($member->status == 'ok'): ?>
                                    (繳費成功)
                                <?php elseif($member->status == 'pending'): ?>
                                    (待繳費)
                                <?php elseif($member->status == 'waiting'): ?>
                                    (候補)
                                <?php elseif($member->status == 'host_quota'): ?>
                                    <?php if($member->is_pay): ?>
                                    (保留繳費成功)
                                    <?php else: ?>
                                    (保留名額)
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        
                            <p style="color: gray;"><?php echo e($member->user->note); ?></p>
                        </div>
                    </div>
                    
      
                </td>
                <?php if($host_personnel || $host_main_personnel): ?>
                    <td class="text-center">
                        <?php if(in_array($game->status, ['create','sign_up','prepare','pay_up'])): ?>
                            <?php if($member->status == 'waiting'): ?>
                                <button class="btn btn-primary btn-sm hostquota" data-member_id="<?php echo e($member->id); ?>" data-type="add">移入保留</button>
                                <!-- <button class="btn btn-primary btn-sm hostquota" data-member_id="<?php echo e($member->id); ?>" data-type="remove">移出保留</button> -->
                            <?php else: ?>
                                <?php if($member->is_lock == 1): ?>
                                <button class="btn btn-primary btn-sm hostquota" data-member_id="<?php echo e($member->id); ?>" data-type="unlock">解鎖退賽</button>
                                    <?php if($member->is_pay != 1): ?>
                                        <button class="btn btn-danger btn-sm hostquota" data-member_id="<?php echo e($member->id); ?>" data-type="forceCancel">強迫退賽</button>
                                    <?php endif; ?>
                                <?php else: ?>
                                <button class="btn btn-primary btn-sm hostquota" data-member_id="<?php echo e($member->id); ?>" data-type="lock">鎖定</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif($game->status == 'ing'): ?>
                            <?php
                                $type = ($member->is_check_in) ? 0 : 1;
                                $text = ($member->is_check_in) ? '取消報到' : '報到';
                            ?>
                            <button class="btn btn-primary btn-sm checkin" data-member_id="<?php echo e($member->id); ?>" data-type="<?php echo e($type); ?>"><?php echo e($text); ?></button>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <?php if($host_personnel || $host_main_personnel): ?>
                    <td colspan="4" class="text-center">目前無人報名</td>
                <?php else: ?>
                    <td colspan="3" class="text-center">目前無人報名</td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div><?php /**PATH /volume/project/Shrimp/resources/views/games/mobile/show_signup.blade.php ENDPATH**/ ?>