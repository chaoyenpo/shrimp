@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">新增圖鑑</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="{{ url('illustration') }}" @submit="onSubmit" @reset="onReset">
      @csrf
      <div class="row mb-3">
        <div class="col-md-2 text-right">釣竿名稱：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="name" name="name" v-model.trim="form.name" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">調數評估：</div>
        <div class="col-md-6">
            <b-form-input type="number" id="steps" name="steps" v-model.trim="form.steps" min="1" max="255" step="1" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">可選擇節數：</div>
        <div class="col-md-6">
            <b-form-input type="number" id="lengths" name="lengths" v-model.trim="form.lengths" min="1" max="10" step="1" placeholder="1-10" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3" v-show="form.lengths">
        <div class="col-md-2 text-right">各節數內容：</div>
        <div class="col-md-10">
          <div class="row" v-for="index in Number(form.lengths)" :key="index">
            <div class="col-md-2 text-center">第 @{{ index }} 節</div>
            <div class="col-md-10">
              <div class="row">
                <div class="col-md-2 text-right">重量：</div>
                <div class="col-md-4">
                  <b-form-input type="number" name="weight[]" min="0.01" step="0.01"></b-form-input>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 text-right">頭內徑：</div>
                <div class="col-md-4">
                  <b-form-input type="number" name="head_in[]" min="0.01" step="0.01"></b-form-input>
                </div>
                <div class="col-md-2 text-right">頭外徑：</div>
                <div class="col-md-4">
                  <b-form-input type="number" name="head_out[]" min="0.01" step="0.01"></b-form-input>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 text-right">尾內徑：</div>
                <div class="col-md-4">
                  <b-form-input type="number" name="footer_in[]" min="0.01" step="0.01"></b-form-input>
                </div>
                <div class="col-md-2 text-right">尾外徑：</div>
                <div class="col-md-4">
                  <b-form-input type="number" name="footer_out[]" min="0.01" step="0.01"></b-form-input>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">照片1：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="photo1" name="photo1" v-model.trim="form.photo1"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">照片2：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="photo2" name="photo2" v-model.trim="form.photo2"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">評價：</div>
        <div class="col-md-6">
          <b-form-textarea id="reviews" name="reviews" v-model.trim="form.reviews" placeholder="" rows="3" max-rows="6"></b-form-textarea>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">定價：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="price" name="price" v-model.trim="form.price"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">製造商：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="manufacturer" name="manufacturer" v-model.trim="form.manufacturer"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">出品：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="brand" name="brand" v-model.trim="form.brand"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">Youtube：</div>
        <div class="col-md-6">
            <b-form-input type="url" id="youtube" name="youtube" v-model.trim="form.youtube"></b-form-input>
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
          name: '',
          steps: '',
          lengths: '',
          data: '',
          photo1: '',
          photo2: '',
          reviews: '',
          price: '',
          manufacturer: '',
          brand: '',
          youtube: ''
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
})
</script>

@endsection