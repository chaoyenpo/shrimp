<?php
    $request = \Illuminate\Support\Facades\Request::instance();
    $user_id = $request->get('user_id');

    $can_edit = $game->results('final')->get()->isEmpty();
    $chinese = ['一', '二', '三', '四', '五'];
?>


<h3 class="mt-5">預賽</h3>

<div class="row">
    <?php for($i = 1; $i <= $game->mode; $i++): ?>
    <?php
    switch ($i) {
        case 1:
            $pre_game = 'a';
            break;
        case 2:
            $pre_game = 'b';
            break;
    }
    ?>

    <div class="wrap col-xl-12 pre-game-desktop" data-prev="" data-level="round1" data-group="A" data-min="<?php echo e(($i - 1) * $game->people_num/$game->mode + 1); ?>" data-max="<?php echo e($i * $game->people_num/$game->mode); ?>">
        <h3 class="mt-3">第<?php echo e($chinese[$i-1]); ?>場
            <?php if($host_main_personnel && $game->status == 'ing'): ?>
                <?php if(empty($game->progress) || !in_array('round1-lock-number', $game->progress)): ?>
                    <button type="button" class="btn btn-primary random-round">抽籤</button>
                    <?php if(is_array($game->progress) && in_array('round1-random', $game->progress)): ?>
                        <button type="button" class="btn btn-danger lock-number">鎖定號次</button>
                    <?php endif; ?>
                <?php else: ?>
                    <button type="button" class="btn btn-primary random-point">自動產生成績（測試用）</button>
                    <button type="button" class="btn btn-primary rank">排名</button>
                <?php endif; ?>
            <?php endif; ?>

            <?php
                $threshold = $game->people_num/($game->mode*2);
                $pk_input = $game->results('round1', [($i - 1) * $game->people_num/$game->mode + 1, $i * $game->people_num/$game->mode], -1, '晉級PK')->count();
                $pk_output = $threshold - $game->enters('round1', [($i - 1) * $game->people_num/$game->mode + 1, $i * $game->people_num/$game->mode], $threshold);

                $pre_game_a_order_number = $game->results('round1', [($i - 1) * $game->people_num/$game->mode + 1, $i * $game->people_num/$game->mode]);
                $pre_game_a_order_point = $game->results('round1', [($i - 1) * $game->people_num/$game->mode + 1, $i * $game->people_num/$game->mode]);

                $pre_game_a_order_number = $pre_game_a_order_number->orderBy('number', 'asc')->get();
                $pre_game_a_order_point = array_flip($pre_game_a_order_point->orderBy('integral', 'desc')->get()->pluck('user_id')->toArray());


            ?>
            <?php if($pk_input): ?>
                <span class="small text-primary">晉級PK：<?php echo e($pk_input); ?> 取 <?php echo e($pk_output); ?></span><br/>
            <?php endif; ?>
        </h3>
        <div class="row">
            <div class="col-md-12">
                <div class="row userLists">
                    <div class="col-12 col-lg-6">
                        <div class="row userCard header">
                            <div class="col-2 nums">
                            <div>
                                號次
                            </div>
                            </div>
                            <div class="col-3">
                            <div>參賽者</div>
                            </div>
                            <div class="col-7 resultContainer">
                            <div>成績</div>
                            <div>結果</div>
                            <div>是否晉級</div>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-lg-6 secondHeader">
                        <div class="row userCard header">
                            <div class="col-2 nums">
                            <div>
                                號次
                            </div>
                            </div>
                            <div class="col-3">
                            <div>參賽者</div>
                            </div>
                            <div class="col-7 resultContainer">
                            <div>成績</div>
                            <div>結果</div>
                            <div>是否晉級</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row userLists">
                <?php $__empty_1 = true; $__currentLoopData = $pre_game_a_order_number; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $image = '';
                            $key = $rank_list[$result->user_id] ?? 99 + 1;

                            if ($key == 0) {
                                $image = '閃電黑';
                            } else if ($key >= 1 && $key <= 12) {
                                $image = '閃電紅';
                            } else if ($key >= 14 && $key <= 29) {
                                $image = '閃電紫';
                            }
                        ?>
                        <?php if($loop->index +1 == 1): ?>
                            <div class="col-12 col-lg-6">
                        <?php endif; ?>
                            <div style="background-image: url('/<?php echo e($image); ?>.gif')" class="row userCard <?php if($result->result == 1 || ($result->result == '冠軍PK' && $result->is_pk_win)): ?> bg-highlight <?php elseif($user && $result->user_id == $user->id): ?> bg-self <?php elseif($result->user_id == $user_id): ?> bg-target <?php endif; ?>" data-result_id="<?php echo e($result->id); ?>">
                                <div class="col-2 nums">
                                    <div>
                                        <h3><?php echo e($result->numberText()); ?></h3>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="user" style="font-size: 17px;">
                                        <img src="<?php echo e($result->user->photo); ?>"  class="rounded-circle avatar">
                                        <?php if($user): ?>
                                            <a href="<?php echo e(url('api/game')); ?>/profile/<?php echo e($result->user_id); ?>?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>"><?php echo e($result->user->nickname); ?></a>
                                        <?php else: ?>
                                            <a href="<?php echo e(url('api/game')); ?>/profile/<?php echo e($result->user_id); ?>"><?php echo e($result->user->nickname); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-7 resultContainer">
                                    <div data-type="pre-first-<?php echo e($pre_game); ?>" data-pre-first-<?php echo e($pre_game); ?>=<?php echo e($result->numberText()); ?> class="<?php if($can_edit): ?> point <?php endif; ?>"><?php echo e($result->point ?? '-'); ?></div>
                                    <div class="result"><?php echo e($result->resultText()); ?></div>
                                    <div><?php echo e($result->canAdvance() ? '是' : ''); ?></div>
                                </div>
                            </div>
                        <?php if($loop->index +1 == round($loop->count/2)): ?>
                            </div>
                            <div class="col-12 col-lg-6">
                        <?php elseif($loop->index +1 == $loop->count): ?>
                            </div>
                         <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                
                <div class="col-12 text-center" style="margin-top: 20px"><h3>尚未抽籤</h3></div>
                    
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>
</div>


<h3 class="mt-5">決賽</h3>

<div class="wrap" data-prev="round1" data-level="final" data-min="1" data-max="<?php echo e($game->people_num/$game->mode); ?>">
    <h3 class="mt-3">
        <?php if($host_main_personnel && $game->status == 'ing'): ?>
            <?php if(empty($game->progress) || !in_array('final-lock-number', $game->progress)): ?>
                <button type="button" class="btn btn-primary random-round">抽籤</button>
                <?php if(is_array($game->progress) && in_array('final-random', $game->progress)): ?>
                    <button type="button" class="btn btn-danger lock-number">鎖定號次</button>
                <?php endif; ?>
            <?php else: ?>
                <button type="button" class="btn btn-primary random-point">自動產生成績（測試用）</button>
                <button type="button" class="btn btn-primary rank">排名</button>
                <button type="button" class="btn btn-primary" id="btn-integral">計算積分</button>
                <?php if(in_array('final-integral', $game->progress)): ?>
                    <button type="button" class="btn btn-danger" id="btn-end">比賽結束</button>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </h3>
    <div class="row">
        <div class="col-md-12">
            <div class="row userLists">
                <div class="col-12 col-lg-6">
                    <div class="row userCard header">
                        <div class="col-2 nums">
                        <div>
                            號次
                        </div>
                        </div>
                        <div class="col-3">
                        <div>參賽者</div>
                        </div>
                        <div class="col-7 resultContainer">
                        <div>成績</div>
                        <div>結果</div>
                        <div>積分</div>
                        </div>
                    </div>
                    </div>
                    <div class="col-12 col-lg-6 secondHeader">
                    <div class="row userCard header">
                        <div class="col-2 nums">
                        <div>
                            號次
                        </div>
                        </div>
                        <div class="col-3">
                        <div>參賽者</div>
                        </div>
                        <div class="col-7 resultContainer">
                        <div>成績</div>
                        <div>結果</div>
                        <div>積分</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row userLists">

            <?php
                $final_number = $game->results('final', [1,$game->people_num/$game->mode]);
                $final_point = $game->results('final', [1,$game->people_num/$game->mode]);

                $final_number = $final_number->orderBy('number', 'asc')->get();
                $final_point = array_flip($final_point->orderBy('integral', 'desc')->get()->pluck('user_id')->toArray());
            ?>

            <?php $__empty_1 = true; $__currentLoopData = $game->results('final', [1,$game->people_num/$game->mode])->orderBy('number', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $image = '';
                        $key = $rank_list[$result->user_id] ?? 99 + 1;

                        if ($key == 0) {
                            $image = '閃電黑';
                        } else if ($key >= 1 && $key <= 12) {
                            $image = '閃電紅';
                        } else if ($key >= 14 && $key <= 29) {
                            $image = '閃電紫';
                        }
                    ?>

                    <?php if($loop->index +1 == 1): ?>
                        <div class="col-12 col-lg-6">
                    <?php endif; ?>   
                        <div style="background-image: url('/<?php echo e($image); ?>.gif')" class="row userCard <?php if($result->result == 1 || ($result->result == '冠軍PK' && $result->is_pk_win)): ?> bg-highlight <?php elseif($user && $result->user_id == $user->id): ?> bg-self <?php elseif($result->user_id == $user_id): ?> bg-target <?php endif; ?>" data-result_id="<?php echo e($result->id); ?>" >
                            <div class="col-2 nums">
                                <div>
                                    <h3><?php echo e($result->numberText()); ?></h3>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="user" style="font-size: 17px;">
                                    <img src="<?php echo e($result->user->photo); ?>"  class="rounded-circle avatar">
                                    <?php if($user): ?>
                                        <a href="<?php echo e(url('api/game')); ?>/profile/<?php echo e($result->user_id); ?>?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>"><?php echo e($result->user->nickname); ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo e(url('api/game')); ?>/profile/<?php echo e($result->user_id); ?>"><?php echo e($result->user->nickname); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-7 resultContainer">
                                <div data-type="complete" data-complete=<?php echo e($result->numberText()); ?> class="point"><?php echo e($result->point ?? '-'); ?></div>
                                <div class="result" data-result="<?php echo e($result->result); ?>"><?php echo e($result->resultText('final')); ?></div>
                                <div><?php echo e($result->user->gameIntegral(null, $result->game_id) ?? ''); ?></div>
                            </div>
                        </div>
                    
                    <?php if($loop->index +1 == round($loop->count/2)): ?>
                        </div>
                        <div class="col-12 col-lg-6">
                    <?php elseif($loop->index +1 == $loop->count): ?>
                        </div>
                     <?php endif; ?>
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                
                <div class="col-12 text-center" style="margin-top: 20px"><h3>尚未抽籤</h3></div>
                
            <?php endif; ?>
            </div>
        
        </div>
    </div>
</div><?php /**PATH /volume/project/Shrimp/resources/views/games/mobile/show_round.blade.php ENDPATH**/ ?>