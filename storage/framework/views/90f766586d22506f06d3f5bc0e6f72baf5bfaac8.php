<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">新增廣告</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="<?php echo e(url('ad')); ?>" @submit="onSubmit" @reset="onReset">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="type" value="confirm">
      <div class="row mb-3">
        <div class="col-md-2 text-right">分類：</div>
        <div class="col-md-6">
            <b-form-input type="number" id="category" name="category" v-model.trim="form.category" min="0" max="8" step="1" placeholder="0-8" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">廣告名稱：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="name" name="name" v-model.trim="form.name" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">連結網址：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="url" name="url" v-model.trim="form.url" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">圖片種類：</div>
        <div class="col-md-2">
            <b-form-input type="text" id="image_type" name="image_type" v-model.trim="form.image_type" required></b-form-input>
        </div>
        <div class="col-md-2 text-right">圖片網址：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="image" name="image" v-model.trim="form.image" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">高度：</div>
        <div class="col-md-4">
            <b-form-input type="number" id="height" name="height" v-model.trim="form.height" min="0" max="5" step="1" placeholder="0-5" required></b-form-input>
        </div>
        <div class="col-md-2 text-right">經度：</div>
        <div class="col-md-4">
            <b-form-input type="number" id="location_lat" name="location_lat" v-model.number="form.location_lat" min="-90" max="90" step="0.00000001"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">權重：</div>
        <div class="col-md-4">
            <b-form-input type="number" id="weight" name="weight" v-model.trim="form.weight" min="0" max="50" step="1" placeholder="0-50" required></b-form-input>
        </div>
        <div class="col-md-2 text-right">緯度：</div>
        <div class="col-md-4">
            <b-form-input type="number" id="location_lng" name="location_lng" v-model.number="form.location_lng" min="-180" max="180" step="0.00000001"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">蝦皮賣場：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="shopee" name="shopee" v-model.trim="form.shopee"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">IG：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="ig" name="ig" v-model.trim="form.ig"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">FB 社群：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="fb_group" name="fb_group" v-model.trim="form.fb_group"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">FB 粉絲頁：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="fb_page" name="fb_page" v-model.trim="form.fb_page"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">Youtube：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="youtube" name="youtube" v-model.trim="form.youtube"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">販售地點：</div>
        <div class="col-md-10 form-inline">
            <label for="begin_at`" class="ml-3">釣蝦場：</label>
            <b-form-select id="sales_farm" name="sales_farm[]" v-model="form.sales_farm" :select-size="10" multiple>
                <option v-for="(value,index) in farms" :key="index" :value="value.id">{{ value.name }}</option>
            </b-form-select>
            <label for="begin_at`" class="ml-3">釣具店：</label>
            <b-form-select id="sales_shop" name="sales_shop[]" v-model="form.sales_shop" :select-size="10" multiple>
                <option v-for="(value,index) in shops" :key="index" :value="value.id">{{ value.name }}</option>
            </b-form-select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">是否開啟：</div>
        <div class="col-md-10">
          <b-form-radio-group id="is_enabled" name="is_enabled" v-model="form.is_enabled">
            <b-form-radio value="1">是</b-form-radio>
            <b-form-radio value="0">否</b-form-radio>
          </b-form-radio-group>
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
          category: '',
          name: '',
          url: '',
          image_type: '',
          image: '',
          height: '',
          weight: '',
          location_lat: '',
          location_lng: '',
          shopee: '',
          fb_group: '',
          fb_page: '',
          ig: '',
          youtube: '',
          is_enabled: 1,
          sales_farm: [],
          sales_shop: []
      }
      return {
          farms: <?php echo json_encode($farms, 15, 512) ?>,
          shops: <?php echo json_encode($shops, 15, 512) ?>,
          initial: Object.assign({}, data),
          form: Object.assign({}, data)
      }
  },
  mounted() {
      this.form.sales_farm = Object.assign([], this.initial.sales_farm);
      this.form.sales_shop = Object.assign([], this.initial.sales_shop);
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
<?php echo $__env->make('welcome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /volume/project/Shrimp/resources/views/ads/create.blade.php ENDPATH**/ ?>