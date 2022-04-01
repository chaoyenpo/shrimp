<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">儲值</span>
        <a href="/point/sub" class="btn btn-primary float-right" role="button" aria-pressed="true">退款</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" @submit="onSubmit" @reset="onReset">
      <?php echo csrf_field(); ?>
      <div class="row mb-3">
        <div class="col-md-2 text-right">末四碼：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="last_4" name="last_4" v-model.trim="form.last_4" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">電話：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="mobile" name="mobile" v-model.trim="form.mobile" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">點數：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="point" name="point" v-model.trim="form.point" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">時間：</div>
        <div class="col-md-8">
          <input type="text" id="time" name="time" v-model.trim="form.time" data-date-format="yyyy-mm-dd hh:ii" required>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-center">
          <b-button type="submit" variant="primary">確定</b-button>
          <b-button type="reset" variant="primary">重置</b-button>
        </div>
      </div>

      <?php if(!empty($message)): ?>
      <div class="alert alert-primary mt-4" role="alert">
        <?php echo e($message); ?>

      </div>
      <?php endif; ?>
    </b-form>
</div>

<script>
var app = new Vue({
  el: '#app',
  data() {
      let data = {
          time: '',
          last_4: '',
          mobile: '',
          point: '',
      }
      return {
          initial: Object.assign({}, data),
          form: Object.assign({}, data)
      }
  },
  methods: {
    onSubmit(evt) {
    },
    onReset(evt) {
      this.$data.form = Object.assign({}, this.$data.initial)
    }
  }
});

$(function () {
    $('#time').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss'
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\shrimp\resources\views/points/add.blade.php ENDPATH**/ ?>