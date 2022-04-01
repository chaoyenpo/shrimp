<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game\Forms\GameFormRequest;
use App\Models\Game\Repositories\GameRepository;
use App\Models\Game\Repositories\GameMemberRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;

class ActivityController extends Controller
{
    private $repository;
    private $repository_member;

    private $data;
    private $leaderboard;
    private $user;

    public function __construct(
        GameRepository $repository,
        GameMemberRepository $repository_member,
        ShrimpFarmRepository $shrimpFarmRepository
    ) {
        $this->repository = $repository;
        $this->repository_member = $repository_member;
        $this->shrimpFarmRepository = $shrimpFarmRepository;
        $leaderboard = array(
            0 => array(
                'name' => '圈圈圈',
                'photo' => 'https://www.google.com.tw/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png',
                'nickname' => '圈醬',
                'title' => '斬蝦隊',
                'point' => 0,
                'ranking' => 101
            ),
            1 => array(
                'name' => '叉叉叉',
                'photo' => 'https://www.google.com.tw/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png',
                'nickname' => '叉醬',
                'title' => '斬蝦隊',
                'point' => 1,
                'ranking' => 100
            ),
        );
        $this->leaderboard = $leaderboard;
        $user = array(
            'name' => '我自己',
            'photo' => 'https://www.google.com.tw/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png',
            'nickname' => '腸鏡人',
            'title' => '斬蝦隊',
            'point' => -1,
            'ranking' => 102
        );
        $this->user = $user;

    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->game('single');
    }

    public function rule($page)
    {
        switch ($page)
        {
            // case 'regular':
            //     return view('activities.rule.regular');
            //     break;
            // case 'year_end':
            //     return view('activities.rule.yearEnd');
            //     break;
            // case 'achievement':
            //     return view('activities.rule.achievement');
            //     break;
            // case 'registration':
            //     return view('activities.rule.registration');
            //     break;
            //     case 'depositRefund':
            //         return view('activities.rule.depositRefund');
            //         break;
            default:
                $games = $this->repository->listForFrontend('end');
                $shrimpFarms = $this->shrimpFarmRepository->hasOpen();
                return view('activities.rule.index', compact('games', 'shrimpFarms'));
                break;
        }
    }

    public function game($type)
    {
        $games = $this->repository->listForFrontend($_GET['status'] ?? 'notend', array_filter(request()->only(['location_catrgory', 'type'])));
        switch ($type)
        {
            case 'single':
                return view('activities.game.single', compact('status', 'games'));
                break;
            case 'year_end':
                return view('activities.game.year_end', compact('status', 'games'));
                break;
        }
    }

    public function leaderboard($type)
    {
        switch ($type)
        {
            case 'point':
                return view('activities.leaderboard.point', ['leaderboards' => $this->leaderboard, 'user' => $this->user]);
                break;
            case 'champion':
            case 'takeover':
                return view('activities.leaderboard.takeover', ['leaderboards' => $this->leaderboard, 'user' => $this->user]);
                break;
            case 'cut':
                return view('activities.leaderboard.cut', ['leaderboards' => $this->leaderboard, 'user' => $this->user]);
                break;
        }
    }
}
