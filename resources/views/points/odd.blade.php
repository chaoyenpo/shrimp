@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">退款</span>
        <a href="/point/add" class="btn btn-primary float-right" role="button" aria-pressed="true">儲值</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" @submit="onSubmit" @reset="onReset">
      @csrf
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
</div>

<script>
var app = new Vue({
  el: '#app',
  data() {
      let data = {,
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
});
</script>

@endsection