<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShrimpFarm\Forms\ShrimpFarmEventFormRequest;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmEventRepository;
use App\Models\System\Services\CrawlerService;
use App\Models\System\Services\FCMService;
use App\Models\Profile\Repositories\UserRepository;
use App\Notifications\SomeNotification;
use Carbon\Carbon;

class LoginController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('login');
    }
}
