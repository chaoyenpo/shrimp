@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">修改釣具店</span>
        <a href="#" onclick="history.back()" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    <b-form method="POST" action="{{ url('fishingTackleShop') }}/{{ $record['id'] }}" @submit="onSubmit" @reset="onReset" v-if="show">
      @csrf
      <input type="hidden" name="_method" value="PUT">
      <div class="row mb-3">
        <div class="col-md-2 text-right">名稱：</div>
        <div class="col-md-8">
          <b-form-input type="text" id="input-name" name="name" v-model.trim="form.name" placeholder="Name" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">地址：</div>
        <div class="col-md-8">
          <b-form-input type="text" id="input-address" name="address" v-model.trim="form.address" placeholder="Address" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right"></div>
        <div class="col-md-8">
          <div class="form-group form-inline">
            <label for="input-location_lat">經度：</label>
            <b-form-input type="number" id="input-location_lat" name="location_lat" v-model.number="form.location_lat" min="-90" max="90" step="0.00000001"></b-form-input>
            <label for="input-location_lng" class="ml-3">緯度：</label>
            <b-form-input type="number" id="input-location_lng" name="location_lng" v-model.number="form.location_lng" min="-180" max="180" step="0.00000001"></b-form-input>
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">電話：</div>
        <div class="col-md-8">
          <b-form-input type="text" id="input-phone" name="phone" v-model.trim="form.phone" placeholder="Phone"></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">不再營業：</div>
        <div class="col-md-10">
          <b-form-radio-group id="is_close" name="is_close" v-model="form.is_close">
            <b-form-radio value="1">是</b-form-radio>
            <b-form-radio value="0">否</b-form-radio>
          </b-form-radio-group>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">營業時間：</div>
        <div class="col-md-10">
          <div class="mb-2 form-group form-inline" v-for="(item,index) in form.bussiness_hour">
              <b-form-checkbox :id="`checkbox[${item.day}]`" name="day[]" v-model="form.selected" :value="`${index}`">@{{days[item.day]}}</b-form-checkbox>
              <label :for="`input-${item.day}-begin_at`" class="ml-3">開始時間：</label>
              <b-form-input type="text" :id="`input-${item.day}-begin_at`" name="begin_at[]" v-model.trim="form.bussiness_hour[index].begin_at" minlength="8" maxlength="8" placeholder="00:00:00"></b-form-input>
              <label :for="`input-${item.day}-end_at`" class="ml-3">結束時間：</label>
              <b-form-input type="text" :id="`input-${item.day}-end_at`" name="end_at[]" v-model.trim="form.bussiness_hour[index].end_at" minlength="8" maxlength="8" placeholder="00:00:00"></b-form-input>
          </div>
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
    days: ['日', '一', '二', '三', '四', '五', '六'],
    show: true
  },
  methods: {
    onSubmit(evt) {
    },
    onReset(evt) {
      evt.preventDefault()
	    initialData = this.$data.initial;

      this.form.name = initialData.name
      this.form.address = initialData.address
      this.form.location_lat = initialData.location_lat
      this.form.location_lng = initialData.location_lng
      this.form.phone = initialData.phone
      this.form.content = initialData.content
      this.form.news = initialData.news
      this.form.is_close = initialData.is_close
      this.form.selected = initialData.selected

      this.$data.form.bussiness_hour.forEach(function(element, index) {
        element.begin_at = initialData.bussiness_hour[index].begin_at;
        element.end_at = initialData.bussiness_hour[index].end_at;
      });
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