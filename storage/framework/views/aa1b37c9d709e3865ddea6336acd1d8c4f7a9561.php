<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">退款</span>
        <a href="/point/add" class="btn btn-primary float-right" role="button" aria-pressed="true">儲值</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" @submit="onSubmit" @reset="onReset">
      <?php echo csrf_field(); ?>
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
      <div class="row">
        <div class="col-md-12 text-center">
          <b-button type="submit" variant="primary">確定</b-button>
          <b-button type="reset" variant="primary">重置</b-button>
        </div>
      </div>
    </b-form>

    <?php if(!empty($message)): ?>
    <div class="alert alert-primary mt-4" role="alert">
      <?php echo e($message); ?>

    </div>
    <?php endif; ?>
</div>

<script>
var app = new Vue({
  el: '#app',
  data() {
      let data = {
          mobile: '',
          point: ''
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

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /volume/project/Shrimp/resources/views/points/sub.blade.php ENDPATH**/ ?>