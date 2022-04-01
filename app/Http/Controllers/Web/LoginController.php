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
use App\Models\Profile\Entities\User;

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
        // $user = User::where('email', 'a00959988@gmail.com')->first();
        // dd($user->delete());
        return view('login');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request)
    {
        if ($request->account == '18420621' && $request->password == 'ji394su3') {
            $request->session()->put('isLogin', true);
            return redirect('/shrimpFarmEvent');
        }
    }
}
