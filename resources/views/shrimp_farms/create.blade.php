@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">新增釣蝦場</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="{{ url('shrimpFarm') }}" @submit="onSubmit" @reset="onReset" v-if="show">
      @csrf
      <div class="row mb-3">
        <div class="col-md-2 text-right">目標網址：</div>
        <div class="col-md-6">
          <b-form-input type="url" id="input-url" name="url" v-model.trim="form.url" placeholder="URL" required></b-form-input>
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
  data: {
    initial: {
      url: ''
    },
    form: {
      url: ''
    },
    show: true
  },
  methods: {
    onSubmit(evt) {
    },
    onReset(evt) {
      evt.preventDefault();
	    initialData = this.$data.initial;

      this.form.url = initialData.url
      // Trick to reset/clear native browser form validation state
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    }
  }
})
</script>

@endsection