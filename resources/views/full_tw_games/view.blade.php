<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>蝦霸後台</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <link type="text/css" rel="stylesheet" href="{{ asset('/css/app.css') }}"/>
        <script src="{{ asset('/js/app.js') }}"></script>


        <link type="text/css" rel="stylesheet" href="{{ asset('/vendor/fontawesome-free-5.12.0-web/css/all.min.css') }}"/>
        <script src="{{ asset('/vendor/fontawesome-free-5.12.0-web/js/all.min.js') }}"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

        <script type = "text/javascript" src = "https://code.jquery.com/jquery-3.4.1.min.js" ></script>
        <link type="text/css" rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"/>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css"/>
        <script src="https://trentrichardson.com/examples/timepicker/jquery-ui-timepicker-addon.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    </head>
    <body>
      <div class="container">
          <div class="row">
              <div class="col-md-12 my-3">
                  <span class="lead">全台比賽列表</span>
                  <!-- <a href="{{ url('full-tw-game') }}/create" class="btn btn-primary float-right" role="button" aria-pressed="true">新增比賽</a> -->
              </div>
          </div>
          <div id="app">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th class="text-left" width="25%">比賽日期</th>
                    <th class="text-left" width="25%">主辦社團</th>
                    <th class="text-left" width="25%">比賽場地</th>
                    <th class="text-left" width="25%">比賽規格</th>

                  </tr>
                </thead>
                <tbody v-if="records.length">
                  <tr v-for="(record,index) in records" v-bind:data-id="record.id">
                    <td class="text-left">@{{record.begin_at}}</td>
                    <td class="text-left">@{{record.vendor}}</td>
                    <td class="text-left">@{{record.shrimp_farm.name}}</td>
                    <td class="text-left">@{{record.people_num}}人</td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <td colspan="6" class="text-center">目前無任何資料</td>
                  </tr>
                </tbody>
              </table>
          </div>
      </div>

    <script>
    var app = new Vue({
      el: '#app',
      data: {
          records: @json($records)
      }
    })
    </script>

    </body>
</html>