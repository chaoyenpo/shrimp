@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">修改活動</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="{{ url('shrimpFarmEvent') }}/{{ $record['id'] }}" @submit="onSubmit" @reset="onReset" v-if="show">
      @csrf
      <input type="hidden" name="_method" value="PUT">
      <div class="row mb-3">
        <div class="col-md-2 text-right">釣蝦場：</div>
        <div class="col-md-6">
          <b-form-select name="shrimp_farm_id" v-model="form.selected" :options="form.options" required></b-form-select>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">活動內容：</div>
        <div class="col-md-8">
          <b-form-textarea id="textarea-content" name="content" v-model.trim="form.content" placeholder="Content" rows="12" required></b-form-textarea>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">活動圖片：</div>
        <div class="col-md-8">
          <b-form-textarea id="textarea-images" name="images" v-model.trim="form.images" placeholder="http://example.jpg&#13;&#10;http://example2.png" rows="5" max-rows="10"></b-form-textarea>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">結束時間：</div>
        <div class="col-md-8">
          <input type="text" name="end_at" id="end_at" v-model.trim="form.end_at">
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
    initial: @json($record),
    form: @json($record),
    show: true
  },
  methods: {
    onSubmit(evt) {
    },
    onReset(evt) {
      evt.preventDefault()
      initialData = this.$data.initial;

      this.form.selected = initialData.selected
      this.form.content = initialData.content
      this.form.images = initialData.images
      this.form.end_at = initialData.end_at

      // Trick to reset/clear native browser form validation state
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    }
  }
});


$(function () {
    $('#end_at').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
});
</script>

@endsection