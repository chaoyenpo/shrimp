@extends('welcome')
@section('content')

<div class="row">
    <div class="col-md-12 my-3">
        <span class="lead">釣蝦場活動列表</span>
        <a href="{{ url('shrimpFarmEvent') }}/create" class="btn btn-primary float-right" role="button" aria-pressed="true">新增活動</a>
    </div>
</div>

<div id="app">
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th class="text-left">釣蝦場</th>
          <th class="text-left">活動內容</th>
          <th class="text-center">結束時間</th>
          <th class="text-center">更新時間</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody v-if="records.length">
        <tr v-for="(record,index) in records" v-bind:data-id="record.id">
          <td class="text-center" width="5%">@{{record.id}}</td>
          <td class="text-left" width="20%">@{{record.name}}</td>
          <td class="text-left">@{{record.content}}</td>
          <td class="text-center" width="16%">@{{record.end_at}}</td>
          <td class="text-center" width="16%">@{{record.updated_at}}</td>
          <td class="text-center" width="10%">
            <a :href="`{{ url('shrimpFarmEvent') }}/${record.id}/edit`" class="btn btn-primary" role="button" aria-pressed="true">修改</a>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="6" class="text-center">目前無任何資料</td>
        </tr>
      </tbody>
    </table>
</div>

<script>
var app = new Vue({
  el: '#app',
  data: {
      records: @json($records)
  }
})
</script>

@endsection