<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">新增比賽</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="<?php echo e(url('game')); ?>" @submit="onSubmit" @reset="onReset">
      <?php echo csrf_field(); ?>
      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽場地：</div>
        <div class="col-md-6">
            <select class="w-100" id="shrimp_farm_id" name="shrimp_farm_id" v-model="form.shrimp_farm_id">
                <option v-for="(value,index) in farms" :key="index" :value="value.id">{{ value.name }}</option>
            </select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽編號：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="identifier" name="identifier" v-model.trim="form.identifier" required></b-form-input>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽名稱：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="name" name="name" v-model.trim="form.name" required></b-form-input>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽分類：</div>
        <div class="col-md-6">
            <!-- <b-form-input type="text" id="location_catrgory" name="location_catrgory" v-model.trim="form.location_catrgory" required></b-form-input> -->
            <b-form-select id="type" name="type" v-model="form.type" required>
              <option>蝦王爭霸積分賽</option>
              <option>社團比賽</option>
            </b-form-select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽分區：</div>
        <div class="col-md-6">
            <!-- <b-form-input type="text" id="location_catrgory" name="location_catrgory" v-model.trim="form.location_catrgory" required></b-form-input> -->
            <b-form-select id="location_catrgory" name="location_catrgory" v-model="form.location_catrgory" required>
              <option>Ａ區（北海岸）</option>
              <option>Ｂ區（風城）</option>
              <option>Ｃ區（夜都）</option>
              <option>Ｄ區（南灣）</option>
              <option>Ｅ區（霹靂）</option>
            </b-form-select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽模式：</div>
        <div class="col-md-6">
            <!-- <b-form-input type="text" id="location_catrgory" name="location_catrgory" v-model.trim="form.location_catrgory" required></b-form-input> -->
            <b-form-select id="mode" name="mode" v-model="form.mode" required>
              <option value="2">預賽2場</option>
              <option value="3">預賽3場</option>
            </b-form-select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">報名費：</div>
        <div class="col-md-6">
            <!-- <b-form-input type="text" id="location_catrgory" name="location_catrgory" v-model.trim="form.location_catrgory" required></b-form-input> -->
            <b-form-select id="fee" name="fee" v-model="form.fee" required>
              <?php for($i = 2000; $i <= 4000; $i+=100): ?>
              <option><?php echo e($i); ?></option>
              <?php endfor; ?>
            </b-form-select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">卡毛：</div>
        <div class="col-md-6">
            <!-- <b-form-input type="text" id="location_catrgory" name="location_catrgory" v-model.trim="form.location_catrgory" required></b-form-input> -->
            <b-form-select id="bet" name="bet" v-model="form.bet" required>
              <option>0</option>
              <option>600</option>
              <option>800</option>
              <option>1000</option>
            </b-form-select>
        </div>
      </div>

      <input type="hidden" name="people_num" v-model="form.people_num">
      <div class="row mb-3">
        <div class="col-md-2 text-right">規格：</div>
        <div class="col-md-6">
            <b-form-select id="people_num" name="people_num" v-model="form.people_num" readonly required>
                <option value="40">40 人</option>
                <option value="44">44 人</option>
                <option value="48" selected="48">48 人</option>
                <option value="52">52 人</option>
                <option value="54">54 人</option>
            </b-form-select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">保留名額：</div>
        <div class="col-md-6">
            <b-form-input type="number" id="host_quota" name="host_quota" v-model.trim="form.host_quota" min="0" step="1" value="0" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">備註：</div>
        <div class="col-md-6">
          <b-form-textarea id="note" name="note" v-model.trim="form.note" placeholder="" rows="3" max-rows="6"></b-form-textarea>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">協辦社團：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="community" name="community" v-model.trim="form.community"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">贊助商：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="sponsor" name="sponsor" v-model.trim="form.sponsor"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">指定用餌：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="bait" name="bait" v-model.trim="form.bait"></b-form-input>
        </div>
      </div>
      <input type="hidden" name="status" v-model="form.status">
      <!--
      <div class="row mb-3">
        <div class="col-md-2 text-right">目前狀態：</div>
        <div class="col-md-6">
            <b-form-select id="status" name="status" v-model="form.status" required>
                <option value="create" selected="create">建立比賽</option>
                <option value="sign_up">開放報名</option>
                <option value="prepare">賽前整備</option>
                <option value="ing">比賽進行中</option>
                <option value="end">比賽結束</option>
            </b-form-select>
        </div>
      </div>-->
      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽日期：</div>
        <div class="col-md-8">
          <input type="text" id="begin_at" name="begin_at" v-model.trim="form.begin_at" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">開版時間：</div>
        <div class="col-md-8">
          <input type="text" id="start_at" name="start_at" v-model.trim="form.start_at" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-2 text-right">獎盃獎金：</div>
        <div class="col-md-6">
          <b-form-textarea id="bonus" name="bonus" v-model.trim="form.bonus" placeholder="" rows="3" max-rows="6"></b-form-textarea>
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
$(function () {
    $('#begin_at').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('#start_at').datepicker({
        dateFormat: 'yy-mm-dd',
    });

    $('#shrimp_farm_id').select2();
});
  
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
          begin_at: '',
          start_at: '',
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
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /volume/project/Shrimp/resources/views/games/create.blade.php ENDPATH**/ ?>