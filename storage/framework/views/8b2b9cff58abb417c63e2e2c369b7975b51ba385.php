<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">新增比賽</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="<?php echo e(url('full-tw-game')); ?>" @submit="onSubmit" @reset="onReset">
      <?php echo csrf_field(); ?>
      <div class="row mb-3">
        <div class="col-md-2 text-right">主辦單位：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="vendor" name="vendor" v-model.trim="form.vendor" required></b-form-input>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">賽事名稱：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="name" name="name" v-model.trim="form.name" required></b-form-input>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">總參加人數：</div>
        <div class="col-md-6">
            <b-form-input type="number" id="people_num" name="people_num" v-model.trim="form.people_num" min="0" step="1" value="0" required></b-form-input>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">場地：</div>
        <div class="col-md-6">
            <b-form-select id="shrimp_farm_id" name="shrimp_farm_id" v-model="form.shrimp_farm_id">
                <option v-for="(value,index) in farms" :key="index" :value="value.id">{{ value.name }}</option>
            </b-form-select>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-2 text-right">模式：</div>
        <div class="col-md-6">
            <b-form-select id="status" name="mode" v-model="form.mode" required>
                <option value="single" selected="single">個人</option>
                <option value="three">三人團體</option>
                <option value="four">四人團體</option>
                <option value="six">六人團體</option>
                <option value="eight">八人團體</option>
            </b-form-select>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-2 text-right">狀態：</div>
        <div class="col-md-6">
            <b-form-select id="status" name="status" v-model="form.status" required>
                <option value="ing">進行中</option>
                <option value="cancel">取消</option>
            </b-form-select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">日期：</div>
        <div class="col-md-8">
          <input type="text" id="begin_at" name="begin_at" v-model.trim="form.begin_at" required>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 text-center">
          <b-button type="submit" variant="primary">確定</b-button>
          <b-button type="reset" variant="primary">重置</b-button>
        </div>
      </div>
    </b-form>
</div>

<script>
var app = new Vue({
  el: '#app',
  data() {
      let data = {
          shrimp_farm_id: '',
          identifier: '',
          name: '',
          location_catrgory: '',
          people_num: 48,
          host_quota: '',
          note: '',
          community: '',
          sponsor: '',
          bait: '',
          status: 'create',
          mode: 'single',
          begin_at: ''
      }
      return {
          farms: <?php echo json_encode($farms, 15, 512) ?>,
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
    $('#shrimp_farm_id').select2();


    $('#begin_at').datepicker({
        dateFormat: 'yy-mm-dd'
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /volume/project/Shrimp/resources/views/full_tw_games/create.blade.php ENDPATH**/ ?>