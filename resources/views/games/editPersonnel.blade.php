@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">任命工作人員</span>
        <a href="{{ url('game') }}/{{ $game->id }}/edit" class="btn btn-primary float-right" role="button" aria-pressed="true">返回</a>
    </div>
</div>

<div id="app">
    @isset($member)
      @if ($member->status == 'host_main_personnel')
        <b-alert show variant="warning">成功設定 {{ $member->user->nickname }}（{{ $member->user->phone }}）為唯一 {{ $member->statusText() }}</b-alert>
      @else
        <b-alert show variant="warning">成功設定 {{ $member->user->nickname }}（{{ $member->user->phone }}）為 {{ $member->statusText() }}</b-alert>
      @endif
    @endisset

    <b-form method="POST" action="{{ url('game') }}/{{ $game->id }}/personnel" @submit="onSubmit" @reset="onReset">
      @csrf
      <div class="row mb-3">
        <div class="col-md-2 text-right">比賽名稱：</div>
        <div class="col-md-6">
            <b-form-input type="text" value="{{ $game->name }}" readonly required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">人員電話：</div>
        <div class="col-md-6">
            <b-form-input type="text" id="phone" name="phone" required></b-form-input>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-2 text-right">是否為主工作人員：</div>
        <div class="col-md-10">
          <b-form-radio-group id="status" name="status" v-model="form.status" required>
            <b-form-radio value="host_main_personnel">是</b-form-radio>
            <b-form-radio value="host_personnel">否</b-form-radio>
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
        status: 'host_personnel'
      };
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

@endsection