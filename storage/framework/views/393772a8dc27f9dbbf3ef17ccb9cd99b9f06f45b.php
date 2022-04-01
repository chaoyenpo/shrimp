<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">廣告列表</span>
        <a href="<?php echo e(url('ad')); ?>/create" class="btn btn-primary float-right" role="button" aria-pressed="true">新增廣告</a>
    </div>
</div>

<div id="app">
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th class="text-left">分類</th>
          <th class="text-left">廣告名稱</th>
          <th class="text-center">建立時間</th>
          <th class="text-center">更新時間</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody v-if="records.length">
        <tr v-for="(record,index) in records" v-bind:data-id="record.id">
          <td class="text-center" width="5%">{{record.id}}</td>
          <td class="text-left" width="20%">{{record.category}}</td>
          <td class="text-left">{{record.name}}</td>
          <td class="text-center" width="16%">{{record.begin_at}}</td>
          <td class="text-center" width="16%">{{record.end_at}}</td>
          <td class="text-center" width="10%">
            <a :href="`<?php echo e(url('ad')); ?>/${record.id}/edit`" class="btn btn-primary" role="button" aria-pressed="true">修改</a>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="6" class="text-center">目前無任何資料</td>
        </tr>
      </tbody>
    </table>
</div>

<script>
var app = new Vue({
  el: '#app',
  data: {
      records: <?php echo json_encode($records, 15, 512) ?>
  }
})
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/ads/index.blade.php ENDPATH**/ ?>