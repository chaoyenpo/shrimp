<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <title>儲值點數</title>

    <style>
        select {
            appearance:none;
            -moz-appearance:none; /* Firefox */
            -webkit-appearance:none;
        }
    </style>

    <link type="text/css" rel="stylesheet" href="<?php echo e(asset('/css/app.css')); ?>"/>
    <script src="<?php echo e(asset('/js/app.js')); ?>"></script>
    <script>
        $(document).ready(function(){
            $("#back").click(function( event ) {
                location.href = "<?php echo e(url('api/ecpay/payBack')); ?>";
            });
        });
    </script>
</head>

<body style="font-size: 18pt;">
    <div>
        <img style="width: 30%; margin: 20px;" src="<?php echo e(asset('pay.png')); ?>">
        <div style="display: inline-block; height: 100%; position: relative; color: #0F2E4E;">
            <h1>儲值</h1>
        </div>
    </div>

    <form method="POST" action="<?php echo e(url('api/ecpay')); ?>">
        <input type="hidden" name="api_token" value="<?php echo e($api_token); ?>">
        <input type="hidden" name="imei" value="<?php echo e($imei); ?>">

        <div style="padding: 0px 20px;">
            <p style="background: gray; color: white;">超商代碼</p>
            <div class="row">
                <div class="col-md-2 pl-5">
                   <input type="radio" class="form-check-input" name="point" value="200_130_CVS" id="btn1" checked/>
                   <label for="btn1" class="form-check-label">200蝦幣/NT130</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 pl-5">
                   <input type="radio" class="form-check-input" name="point" value="2600_1330_CVS" id="btn2"/>
                   <label for="btn2" class="form-check-label">2600蝦幣/NT1330</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 pl-5">
                   <input type="radio" class="form-check-input" name="point" value="12000_6000_CVS" id="btn3"/>
                   <label for="btn3" class="form-check-label">12000蝦幣/NT6000</label>
                </div>
            </div>

            <p style="background: gray; color: white;">ATM購點</p>
            <div class="row">
                <div class="col-md-2 pl-5">
                   <input type="radio" class="form-check-input" name="point" value="200_115_ATM" id="btn1" checked/>
                   <label for="btn1" class="form-check-label">200蝦幣/NT115</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 pl-5">
                   <input type="radio" class="form-check-input" name="point" value="2600_1315_ATM" id="btn2"/>
                   <label for="btn2" class="form-check-label">2600蝦幣/NT1315</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 pl-5">
                   <input type="radio" class="form-check-input" name="point" value="12000_6000_ATM" id="btn3"/>
                   <label for="btn3" class="form-check-label">12000蝦幣/NT6000</label>
                </div>
            </div>
            <div class="my-5">
                <button style="padding: 10px 0px; border-radius: 10px; border: none; background-color: #E94C4A; color: white; height: 10%; float: right; font-size: 18pt;width: 48%;">確認</button>
                <button id="back" style="padding: 10px 0px; border-radius: 10px; border: none; background-color: #0F2E4E; color: white; height: 10%; font-size: 18pt; width: 48%;">上一頁</button>
            </div>
        </div>
    </form>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\shrimp\resources\views/selectPoint.blade.php ENDPATH**/ ?>