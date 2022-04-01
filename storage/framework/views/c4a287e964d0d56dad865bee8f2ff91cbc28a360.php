<?php $__env->startSection('content'); ?>

<h2>比賽內容</h2>

<div class="row mb-3">
    <div class="col-md-9">
        <?php if($game->status == 'create'): ?>
        <div class="row">
            <?php
                $signup_at = $game->signupAt();
                $date_full = explode(' ', $game->signupAt())[0];
                $date = explode('-', $date_full);
            ?>
            <div class="col-md-12">開放報名：<?php echo e($date[1].'/'.$date[2].' 21:00'); ?></div>
        </div>
        <?php endif; ?>
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
            <div class="col-md-6">贊助廠商：<?php echo e($game['sponsor'] ?? '缺乾爹'); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽人數：<?php echo e($game->people_num); ?>（保留名額：<?php echo e($game->host_quota); ?>）</div>
            <div class="col-md-6">指定用餌：<?php echo e($game->bait); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽日期：<?php echo e($game->begin_at->format('Y-m-d')); ?></div>
            <div class="col-md-6">報名人數：<?php echo e($game->members(['ok','waiting','host_quota','pending'])->count()); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">賽事備註：<?php echo e($game->note); ?></div>
            <div class="col-md-6">比賽狀態：<?php echo e($game->statusText()); ?></div>
        </div>

        <div class="row">
            <div class="col-md-6">比賽模式：<a href="#" data-toggle="modal" data-target="#person<?php echo e($game->people_num); ?>Modal"><?php echo e($game->people_num); ?>人 預賽2場 決賽一場</a></div>
            <div class="col-md-6">報名費用：<?php echo e($game->fee); ?> 蝦幣（1300台幣）</div>
        </div>

        <div class="row">
            <div class="col-md-6" style="white-space: pre-line;">獎金獎品：<br/><?php echo e($game->bonus); ?></a></div>
        </div>
<!-- 
        <div class="row">
            <div class="col-md-6"><h4><a href="#" data-toggle="modal" data-target="#bonusModal">獎金獎品</a></h4></div>
        </div> -->

    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
            <?php echo $__env->make('games.mobile.show_host', [
                'game'                => $game,
                'user'                => $user
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    <?php if($game->status == 'end'): ?>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
            <?php echo $__env->make('games.mobile.show_mvp', [
                'game'                => $game,
                'user'                => $user
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="person40Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">40人賽</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="rank">
            <h2>積分</h2>
    <!--         <h6>預賽冠軍</h6>
            <ul class="ruleList">
            <li><p>取8晉級的場次24/2*(8/24)=8分</p></li>
            <li><p>取12晉級的24/2*(12/24)=6分</p></li>
            </ul> -->
            <div>
                <table class="table table-sm">
                    <tr>
                        <td>冠軍</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td>亞軍</td>
                        <td>14</td>
                    </tr>
                    <tr>
                        <td>季軍</td>
                        <td>10</td>
                    </tr>
                    <tr>
                        <td>殿軍</td>
                        <td>7</td>

                    </tr>
                    <tr>
                        <td>五名</td>
                        <td>5</td>
                    </tr>
                    <tr>
                        <td>決賽6~24名</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>預賽冠軍</td>
                        <td>5</td>
                    </tr>
                </table>
            </div>
        </div><!-- 
        <div id="prizetrophy">
            <h2>獎金獎盃</h2>
            <div>
            <table class="table table-sm">
                <tr>
                    <td>冠軍12000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>亞軍8000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>季軍5000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>殿軍2000＋獎牌</td>

                </tr>
                <tr>
                    <td>MVP1000＋獎牌</td>
                </tr>
                <tr>
                    <td>預賽冠軍1000+獎牌</td>
                </tr>
            </table>
        </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="person48Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">48人賽</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="rank">
            <h2>積分</h2>
    <!--         <h6>預賽冠軍</h6>
            <ul class="ruleList">
            <li><p>取8晉級的場次24/2*(8/24)=8分</p></li>
            <li><p>取12晉級的24/2*(12/24)=6分</p></li>
            </ul> -->
            <div>
                <table class="table table-sm">
                    <tr>
                        <td>冠軍</td>
                        <td>24</td>
                    </tr>
                    <tr>
                        <td>亞軍</td>
                        <td>16</td>
                    </tr>
                    <tr>
                        <td>季軍</td>
                        <td>12</td>
                    </tr>
                    <tr>
                        <td>殿軍</td>
                        <td>8</td>

                    </tr>
                    <tr>
                        <td>五名</td>
                        <td>6</td>
                    </tr>
                    <tr>
                        <td>決賽6~24名</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>預賽冠軍</td>
                        <td>6</td>
                    </tr>
                </table>
            </div>
        </div><!-- 
        <div id="prizetrophy">
            <h2>獎金獎盃</h2>
            <div>
            <table class="table table-sm">
                <tr>
                    <td>冠軍12000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>亞軍8000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>季軍5000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>殿軍2000＋獎牌</td>

                </tr>
                <tr>
                    <td>MVP1000＋獎牌</td>
                </tr>
                <tr>
                    <td>預賽冠軍1000+獎牌</td>
                </tr>
            </table>
        </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="person52Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">52人賽</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="rank">
            <h2>積分</h2>
    <!--         <h6>預賽冠軍</h6>
            <ul class="ruleList">
            <li><p>取8晉級的場次24/2*(8/24)=8分</p></li>
            <li><p>取12晉級的24/2*(12/24)=6分</p></li>
            </ul> -->
            <div>
                <table class="table table-sm">
                    <tr>
                        <td>冠軍</td>
                        <td>26</td>
                    </tr>
                    <tr>
                        <td>亞軍</td>
                        <td>17</td>
                    </tr>
                    <tr>
                        <td>季軍</td>
                        <td>13</td>
                    </tr>
                    <tr>
                        <td>殿軍</td>
                        <td>9</td>

                    </tr>
                    <tr>
                        <td>五名</td>
                        <td>7</td>
                    </tr>
                    <tr>
                        <td>決賽6~24名</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>預賽冠軍</td>
                        <td>7</td>
                    </tr>
                </table>
            </div>
        </div><!-- 
        <div id="prizetrophy">
            <h2>獎金獎盃</h2>
            <div>
            <table class="table table-sm">
                <tr>
                    <td>冠軍12000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>亞軍8000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>季軍5000＋獎盃＋獎牌</td>
                </tr>
                <tr>
                    <td>殿軍2000＋獎牌</td>

                </tr>
                <tr>
                    <td>MVP1000＋獎牌</td>
                </tr>
                <tr>
                    <td>預賽冠軍1000+獎牌</td>
                </tr>
            </table>
        </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<hr size="1px" align="center" width="100%">
<?php
    $host_personnel = false;
    $host_main_personnel = false;
?>
<?php if($user): ?>
    <?php if($game->members(['host_main_personnel'], $user->id)->first()): ?>
        <?php
            $host_main_personnel = true;
        ?>
    <?php endif; ?>
<?php endif; ?>
<?php if($game->status == 'ing' || $game->status == 'end'): ?>
    <div id="round">
        <?php echo $__env->make('games.mobile.show_round', [
            'game'                => $game,
            'user'                => $user,
            'host_main_personnel' => $host_main_personnel
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <script>
        var socket = io.connect(':3000');
        socket.on('connect', function(m) {
            console.log('connect success');
        });

        socket.on('<?php echo e($game->identifier); ?>', function(data){
            if (!$("#pointModal").data('bs.modal')) {
                window.location.reload();
            }
        });
    </script>
    
    <?php if($host_main_personnel && $game->status == 'ing'): ?>
    <script>
    $(function(){
        var type,
        number;
        $("#round").on("click", ".random-round", function(){
            let btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/random",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    prev: $(this).closest('.wrap').data('prev'),
                    level: $(this).closest('.wrap').data('level'),
                    min: $(this).closest('.wrap').data('min'),
                    max: $(this).closest('.wrap').data('max')
                },
                success: function(data){
                    socket.emit('game', {
                        game: '<?php echo e($game->identifier); ?>'
                    });
                    try {
                        let obj = JSON.parse(data);
                        alert(obj.error);
                    } catch (e) {
                        $("#round").html(data);
                    }
                    btn.prop('disabled', false);
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        });
        $("#round").on("dblclick", ".lock-number", function(){
            let btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/lockNumber",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: "PUT",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    prev: $(this).closest('.wrap').data('prev'),
                    level: $(this).closest('.wrap').data('level'),
                    min: $(this).closest('.wrap').data('min'),
                    max: $(this).closest('.wrap').data('max')
                },
                success: function(data){
                    $("#round").html(data);
                    btn.prop('disabled', false);
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        });
        $("#round").on("click", ".random-point", function(){
            let btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/autopoint",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: "PUT",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    level: $(this).closest('.wrap').data('level'),
                    min: $(this).closest('.wrap').data('min'),
                    max: $(this).closest('.wrap').data('max')
                },
                success: function(data){
                    $("#round").html(data);
                    btn.prop('disabled', false);
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        });
        $("#round").on("click", ".point", function(){
            let target = $(this);
            let numberText = $(this).parent().parent().children('.nums').text().trim();
            let name       = $('.user a', $(this).parent().parent()).text().trim();
            let level      = $(this).closest('.wrap').data('level');
            let prefix     = '';
            if (level == 'round1') {
                prefix = "預賽："+ $(this).closest('.wrap').data('group') +"　";
            } else if (level == 'round2') {
                prefix = "複賽："+ $(this).closest('.wrap').data('group') +"　";
            }
            $('#pointModal #pointModalLabel').text(prefix+"位置："+numberText);
            $("#pointInput").data('result_id', $(this).parent().parent().data('result_id'));
            $('#pointModal label').text("請輸入 "+name+" 的成績：");

            if (!$("#pointModal").hasClass('show')) {
                $('#pointModal').modal('toggle');
            };

            type = $(this).data('type');
            number = $(this).data(type);
console.log(type);
            setTimeout(function(){
                $("#pointModal").trigger('touchstart');
            },1000);

            // let point = prompt(prefix+"位置："+numberText+"\r\n請輸入 "+name+" 的成績：");
            // if (point != null && point.trim() != "" && !isNaN(parseFloat(point)) && isFinite(point)) {
            //     $.ajax({
            //         url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/point",
            //         type: "POST", dataType: "text",
            //         data: {
            //             _token: "<?php echo e(csrf_token()); ?>",
            //             _method: "PUT",
            //             api_token: "<?php echo e($user->api_token); ?>",
            //             imei: "<?php echo e($user->imei); ?>",
            //             result_id: $(this).parent().data('result_id'),
            //             point: point
            //         },
            //         success: function(data){
            //             target.text(data);
            //         },
            //         error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            //     });
            // } else {
            //     alert('請輸入數字。');
            // }
        });

        $("#pointModal").on("touchstart", function(){
            $('#pointInput').focus();
        })

        $("#pointModal").on('shown.bs.modal', function(){
            $(this).find('#pointInput').click();
        });
        $("#setPointbtn").on('click', function() {
            var point = $("#pointInput").val();
            var result_id = $("#pointInput").data('result_id');
            // console.log(point);
            // console.log($("#pointInput").data('result_id'))
            if (point && point != null && point.trim() != "" && !isNaN(parseFloat(point)) && isFinite(point)) {
                $.ajax({
                    url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/point",
                    type: "POST", dataType: "text",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
                        _method: "PUT",
                        api_token: "<?php echo e($user->api_token); ?>",
                        imei: "<?php echo e($user->imei); ?>",
                        result_id: result_id,
                        point: point
                    },
                    success: function(data){
                        $("div.userCard[data-result_id="+result_id+"] .point").text(data);
                        number++;

                        if ($('[data-' + type + '='+ number +']').length) {
                            $('[data-' + type + '='+ number +']').click();
                        } else {
                            $('#pointModal').modal('toggle');
                        }
                        $("#pointInput").val(null);
                        socket.emit('game', {
                            game: '<?php echo e($game->identifier); ?>'
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
                });
            } else {
                alert('請輸入數字。');
            }
        })
        $("#round").on("click", ".rank", function(){
            let target = $(this);
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/rank",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: "PUT",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    level: $(this).closest('.wrap').data('level'),
                    min: $(this).closest('.wrap').data('min'),
                    max: $(this).closest('.wrap').data('max')
                },
                success: function(data){
                    $("#round").html(data);
                    socket.emit('game', {
                        game: '<?php echo e($game->identifier); ?>'
                    });
                    try {
                        let obj = JSON.parse(data);
                        alert(obj.error);
                    } catch (e) {
                        $("#round").html(data);
                    }
                    btn.prop('disabled', false);
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        });
        $("#round").on("click", ".result", function(){
            let target = $(this);
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/pk",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: "PUT",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    level: $(this).closest('.wrap').data('level'),
                    min: $(this).closest('.wrap').data('min'),
                    max: $(this).closest('.wrap').data('max'),
                    result: $(this).data('result'),
                    result_id: $(this).parent().parent().data('result_id')
                },
                success: function(data){
                    socket.emit('game', {
                        game: '<?php echo e($game->identifier); ?>'
                    });
                    $("#round").html(data);
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        });

    });
    </script>
    <?php endif; ?>
<?php endif; ?>

<h3 style="margin-top: 30px">
參賽人員名單<br/>
<?php if(!empty($game['start_at']) && \Carbon\Carbon::parse($game['start_at']->format('Y-m-d 20:00:00'))->gte(\Carbon\Carbon::now())): ?>
<?php echo e($game->start_at->format('m-d 20:00:00')); ?> 開板
<?php endif; ?>
<?php if($user): ?>
    <?php if($game->members(['host_main_personnel'], $user->id)->first()): ?>
        <?php
            $host_main_personnel = true;
        ?>
        <?php if($game->members(['ok','waiting','pending'])->count() < ($game->people_num-$game->host_quota)): ?>
            <button type="button" class="btn btn-primary" id="btn-test-signup">自動一般報名（測試用）</button>
            <button type="button" class="btn btn-primary" id="btn-test-signup-reset">清除所有報名（測試用）</button>
        <?php else: ?>
            <button type="button" class="btn btn-primary d-none" id="btn-test-signup">自動一般報名（測試用）</button>
            <button type="button" class="btn btn-primary" id="btn-test-signup-reset">清除所有報名（測試用）</button>
        <?php endif; ?>
    <?php elseif(in_array($game->status, ['sign_up', 'pay_up']) && \Carbon\Carbon::parse($game->start_at->format('Y-m-d 20:00:00'))->lte(\Carbon\Carbon::now())): ?>
        <?php
            $member = $game->members(['pending','host_quota'], $user->id)->first();
        ?>
        <?php if($member): ?>
            <form method="POST" action="/api/game/<?php echo e($game->identifier); ?>/signup">

                <?php if(!$member->is_pay && $game->status == 'pay_up'): ?>
                <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/signup?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>&type=pay" class="btn btn-primary" role="button" aria-pressed="true">我要繳費</a>
                <?php endif; ?>
                <input type="hidden" name="api_token" value="<?php echo e($user->api_token); ?>">
                <input type="hidden" name="imei" value="<?php echo e($user->imei); ?>">
                <input type="hidden" name="type" value="quit">
                <button type="submit" class="btn btn-primary">取消報名</button>
            </form>
        <?php elseif($game->members(['ok','waiting'], $user->id)->first()): ?>
            <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/signup?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>&type=quit" class="btn btn-primary" role="button" aria-pressed="true">取消報名</a>
        <?php elseif($game->members(['host_quota'], $user->id)->first()): ?>
            <span class="small text-primary">保留名額，不可退賽。</span>
        <?php elseif($can_sugnup): ?>
            <?php if($user['phone']): ?>
            <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/signup?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>&type=join" class="btn btn-primary" role="button" id="sign_up_btn" aria-pressed="true">我要報名</a>
            <?php else: ?>
            <button onclick="window.alert('請先完成電話驗證');" class="btn btn-primary" role="button" id="sign_up_btn" aria-pressed="true">我要報名</a>
            <?php endif; ?>
        <?php elseif(!$can_sugnup): ?>
            <span class="small text-primary">本日已報名其他賽事</span>
        <?php endif; ?>
    <?php endif; ?>
    <?php if(!empty($result)): ?>
        <div class="alert alert-warning" role="alert"><?php echo e($result); ?></div>
    <?php endif; ?>
<?php endif; ?>
</h3>
<div class="row">
    <div class="col-md-12" id="signup">
        <?php echo $__env->make('games.mobile.show_signup', [
            'game'                => $game,
            'user'                => $user,
            'host_personnel'      => $host_personnel,
            'host_main_personnel' => $host_main_personnel
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<script>
$(function(){
    <?php if($host_personnel || $host_main_personnel): ?>
    $("#signup").on("click", ".hostquota", function(){
        let obj = $(this);

        if ($(this).data('type') == 'forceCancel') {
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/forceCancel",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    member_id: $(this).data('member_id'),
                    type: $(this).data('type')
                },
                success: function(data){
                    data = JSON.parse(data)
                    $("#signup").html(data.view);
                    if (data.result) {
                        alert(data.result);
                        window.location.reload();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        } else {
            $.ajax({
                url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/hostquota",
                type: "POST", dataType: "html",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    api_token: "<?php echo e($user->api_token); ?>",
                    imei: "<?php echo e($user->imei); ?>",
                    member_id: $(this).data('member_id'),
                    type: $(this).data('type')
                },
                success: function(data){
                    data = JSON.parse(data)
                    $("#signup").html(data.view);
                    if (data.result) {
                        alert(data.result);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
            });
        }
    });
    <?php endif; ?>

    <?php if($host_main_personnel): ?>
    $(".checkin").click(function(){
        let obj = $(this);
        $.ajax({
            url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/checkin",
            type: "POST", dataType: "text",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                _method: "PUT",
                api_token: "<?php echo e($user->api_token); ?>",
                imei: "<?php echo e($user->imei); ?>",
                member_id: $(this).data('member_id'),
                type: $(this).data('type')
            },
            success: function(data){
                if (data == 1) {
                    obj.data('type', 0);
                    obj.text('取消報到');
                } else if (data == 0) {
                    obj.data('type', 1);
                    obj.text('報到');
                }
            },
            error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
        });
    });
    $("#btn-test-signup").click(function(){
        $("#btn-test-signup").hide();
        $.ajax({
            url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/autosignup",
            type: "POST", dataType: "html",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                api_token: "<?php echo e($user->api_token); ?>",
                imei: "<?php echo e($user->imei); ?>"
            },
            success: function(data){
                $("#signup").html(data);
                $("#btn-test-signup-reset").show();
            },
            error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
        });
    });
    $("#btn-test-signup-reset").click(function(){
        $("#btn-test-signup-reset").hide();
        $.ajax({
            url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/resetSignup",
            type: "POST", dataType: "html",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                api_token: "<?php echo e($user->api_token); ?>",
                imei: "<?php echo e($user->imei); ?>"
            },
            success: function(data){
                location.reload();
            },
            error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
        });
    });
    $("#round").on("click", "#btn-integral", function(){
        $("#btn-integral").prop('disabled', true);
        $.ajax({
            url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/integral",
            type: "POST", dataType: "html",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                _method: "PUT",
                api_token: "<?php echo e($user->api_token); ?>",
                imei: "<?php echo e($user->imei); ?>",
                level: $(this).closest('.wrap').data('level')
            },
            success: function(data){
                try {
                    let obj = JSON.parse(data);
                    alert(obj.error);
                } catch (e) {
                    $("#round").html(data);
                }
                $("#btn-integral").prop('disabled', false);
            },
            error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
        });
    });
    $("#round").on("dblclick", "#btn-end", function(){
        $("#btn-end").prop('disabled', true);
        $.ajax({
            url: "<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/end",
            type: "POST", dataType: "html",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                _method: "PUT",
                api_token: "<?php echo e($user->api_token); ?>",
                imei: "<?php echo e($user->imei); ?>"
            },
            success: function(data){
                $("#round").html(data);
                $("#btn-end").prop('disabled', false);
            },
            error: function(xhr, ajaxOptions, thrownError){ ajaxError(); }
        });
    });
    <?php endif; ?>

    $('#bonusModal').on('shown.bs.modal', function(){
        console.log(12333);
        socket.emit('game', {
            game: '<?php echo e($game->identifier); ?>'
        });
    })

});
</script>


<!-- Modal -->
<div class="modal fade" id="pointModal" tabindex="-1" role="dialog" aria-labelledby="pointModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pointModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="pointInputLabel">Password</label>
            <input type="number" pattern="[0-9]*" class="form-control" id="pointInput" autofocus>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
        <button id="setPointbtn" type="button" class="btn btn-primary">確認</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bonusModal" tabindex="-1" role="dialog" aria-labelledby="bonusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bonusModalLabel">獎金獎品</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <p style="white-space: pre-line;"><?php echo e($game->bonus); ?></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">確認</button>
      </div>
    </div>
  </div>
</div>

<style>
.bg-highlight {
    background: #FF9;
}
.bg-self {
    background: #FFC;
}
.bg-target {
    background: #0FF;
}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('activities.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /volume/project/Shrimp/resources/views/games/mobile/show.blade.php ENDPATH**/ ?>