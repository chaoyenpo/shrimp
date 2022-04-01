<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title>蝦霸後台</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <link type="text/css" rel="stylesheet" href="<?php echo e(asset('/css/app.css')); ?>"/>
        <script src="<?php echo e(asset('/js/app.js')); ?>"></script>


        <link type="text/css" rel="stylesheet" href="<?php echo e(asset('/vendor/fontawesome-free-5.12.0-web/css/all.min.css')); ?>"/>
        <script src="<?php echo e(asset('/vendor/fontawesome-free-5.12.0-web/js/all.min.js')); ?>"></script>
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
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="#">蝦霸後台</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
        
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" id="nav-shrimpFarmEvent" href="<?php echo e(url('shrimpFarmEvent')); ?>">釣蝦場活動 <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-shrimpFarm" href="<?php echo e(url('shrimpFarm')); ?>">釣蝦場管理</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-fishingTackleShop" href="<?php echo e(url('fishingTackleShop')); ?>">釣具店管理</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-ad" href="<?php echo e(url('ad')); ?>">廣告管理</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-illustration" href="<?php echo e(url('illustration')); ?>">圖鑑管理</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-game" href="<?php echo e(url('game')); ?>">比賽管理</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-full-tw-game" href="<?php echo e(url('full-tw-game')); ?>">全台比賽管理</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-point" href="<?php echo e(url('point/add')); ?>">儲值管理</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php echo $__env->yieldContent('content'); ?>
            <script>
            let pathname = window.location.pathname;
            if (pathname.includes("shrimpFarmEvent"))
                document.getElementById("nav-shrimpFarmEvent").classList.add("active");
            else if (pathname.includes("shrimpFarm"))
                document.getElementById("nav-shrimpFarm").classList.add("active");
            else if (pathname.includes("fishingTackleShop"))
                document.getElementById("nav-fishingTackleShop").classList.add("active");
            else if (pathname.includes("shrimpFarmEvent"))
                document.getElementById("nav-shrimpFarmEvent").classList.add("active");
            else if (pathname.includes("point"))
                document.getElementById("nav-point").classList.add("active");
            else if (pathname.includes("ad"))
                document.getElementById("nav-ad").classList.add("active");
            else if (pathname.includes("illustration"))
                document.getElementById("nav-illustration").classList.add("active");
            else if (pathname.includes("full-tw-game"))
                document.getElementById("nav-full-tw-game").classList.add("active");
            else if (pathname.includes("game"))
                document.getElementById("nav-game").classList.add("active");
            </script>
        </div>
        <!-- The core Firebase JS SDK is always required and must be listed first -->
        <script src="https://www.gstatic.com/firebasejs/7.17.1/firebase-app.js"></script>

        <!-- TODO: Add SDKs for Firebase products that you want to use
             https://firebase.google.com/docs/web/setup#available-libraries -->
        <script src="https://www.gstatic.com/firebasejs/7.17.1/firebase-analytics.js"></script>

        <script>
          // Your web app's Firebase configuration
          var firebaseConfig = {
            apiKey: "AIzaSyC-G9b7r42WLBQJNzOicSlhkYJ2i9bLhyk",
            authDomain: "shrimp-king.firebaseapp.com",
            databaseURL: "https://shrimp-king.firebaseio.com",
            projectId: "shrimp-king",
            storageBucket: "shrimp-king.appspot.com",
            messagingSenderId: "420016129100",
            appId: "1:420016129100:web:2fe4b010456f63e261e0e9",
            measurementId: "G-Z0E01JKQGZ"
          };
          // Initialize Firebase
          firebase.initializeApp(firebaseConfig);
          firebase.analytics();
        </script>
    </body>
</html>
<?php /**PATH /volume/project/Shrimp/resources/views/welcome.blade.php ENDPATH**/ ?>