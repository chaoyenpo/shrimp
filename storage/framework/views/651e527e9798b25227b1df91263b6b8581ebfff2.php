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
            <div class="col-md-6">贊助廠商：<?php echo e($game->sponsor); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽人數：<?php echo e($game->people_num); ?>（保留名額：<?php echo e($game->host_quota); ?>）</div>
            <div class="col-md-6">指定用餌：<?php echo e($game->bait); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">比賽日期：<?php echo e($game->begin_at); ?></div>
            <div class="col-md-6">報名人數：<?php echo e($game->members(['ok','waiting','host_quota'])->count()); ?></div>
        </div>
        <div class="row">
            <div class="col-md-6">賽事備註：<?php echo e($game->note); ?></div>
            <div class="col-md-6">比賽狀態：<?php echo e($game->statusText()); ?></div>

        </div>
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
參賽人員名單
<?php if($user): ?>
    <?php if($game->members(['host_main_personnel'], $user->id)->first()): ?>
        <?php
            $host_main_personnel = true;
        ?>
        <?php if($game->members(['ok','waiting'])->count() < ($game->people_num-$game->host_quota)): ?>
<!--             <button type="button" class="btn btn-primary" id="btn-test-signup">自動一般報名（測試用）</button>
            <button type="button" class="btn btn-primary" id="btn-test-signup-reset">清除所有報名（測試用）</button> -->
        <?php else: ?>
            <!-- <button type="button" class="btn btn-primary d-none" id="btn-test-signup">自動一般報名（測試用）</button>
            <button type="button" class="btn btn-primary" id="btn-test-signup-reset">清除所有報名（測試用）</button> -->
        <?php endif; ?>
    <?php elseif($game->status == 'sign_up'): ?>
        <?php if($game->members(['ok','waiting'], $user->id)->first()): ?>
            <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/signup?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>&type=quit" class="btn btn-primary" role="button" aria-pressed="true">取消報名</a>
        <?php elseif($game->members(['host_quota'], $user->id)->first()): ?>
            <span class="small text-primary">保留名額，不可退賽。</span>
        <?php elseif($can_sugnup): ?>
            <a href="<?php echo e(url('api/game')); ?>/<?php echo e($game->identifier); ?>/signup?api_token=<?php echo e($user->api_token); ?>&imei=<?php echo e($user->imei); ?>&type=join" class="btn btn-primary" role="button" aria-pressed="true">我要報名</a>
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

<?php echo $__env->make('activities.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/games/mobile/show.blade.php ENDPATH**/ ?>