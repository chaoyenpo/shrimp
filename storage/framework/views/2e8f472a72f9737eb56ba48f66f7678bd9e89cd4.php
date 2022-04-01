<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Redirect to ECPay...</title>
</head>
<body>
<form action="<?php echo e($apiUrl); ?>" id="pay-form" method="post">
<?php $__currentLoopData = $postData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($val); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</form>
</body>
<script>
    document.getElementById('pay-form').submit();
</script>
</html>
<?php /**PATH C:\xampp\htdocs\shrimp\vendor\tsaiyihua\laravel-ecpay\src/../resources/views/send.blade.php ENDPATH**/ ?>