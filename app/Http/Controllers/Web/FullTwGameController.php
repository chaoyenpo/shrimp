<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game\Forms\GameFormRequest;
use App\Models\Game\Forms\FullTwGameFormRequest;
use App\Models\Game\Repositories\FullTwGameRepository;
use App\Models\Game\Repositories\GameMemberRepository;
use App\Models\ShrimpFarm\Repositories\ShrimpFarmRepository;
use App\Models\Profile\Repositories\UserRepository;

class FullTwGameController extends Controller
{
    private $repository;
    private $repository_member;
    private $repository_farm;
    private $repository_user;

    public function __construct(
        FullTwGameRepository $repository,
        GameMemberRepository $repository_member,
        ShrimpFarmRepository $repository_farm,
        UserRepository $repository_user
    ) {
        $this->repository = $repository;
        $this->repository_member = $repository_member;
        $this->repository_farm = $repository_farm;
        $this->repository_user = $repository_user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository->listForBackend();

        return view('full_tw_games.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $farms = $this->repository_farm->listForWeb();

        return view('full_tw_games.create', compact('farms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Game\Forms\GameFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FullTwGameFormRequest $request)
    {
        $data = $request->validated();
        $this->repository->save($data);

        return redirect('full-tw-game');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $farms = $this->repository_farm->listForWeb();
        $game = $this->repository->getById($id); RTIfEntityNotFound($game);
        if ($game->status == 'cancel')
            RTErrorString('比賽已取消');

        $host_main_personnel = $game->members('host_main_personnel')->first();
        $host_personnel = $game->members('host_personnel')->get();
        $record = $game->toArray();

        return view('full_tw_games.edit', compact('farms', 'record', 'host_main_personnel', 'host_personnel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Game\Forms\GameFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(GameFormRequest $request, $id)
    {
        $data = $request->validated();
        $game = $this->repository->getById($id); RTIfEntityNotFound($game);
        if ($game->status == 'cancel')
            RTErrorString('比賽已取消');

        $game->update($data);

        return redirect('game');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $game = $this->repository->getById($request->id); RTIfEntityNotFound($game);
        if ($game->status == 'cancel')
            RTErrorString('比賽已取消');

        $game->delete();

        return 1;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editPersonnel(Request $request, $id)
    {
        $game = $this->repository->getById($id); RTIfEntityNotFound($game);
        if ($game->status == 'cancel')
            RTErrorString('比賽已取消');

        return view('games.editPersonnel', compact('game'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePersonnel(Request $request, $id)
    {
        $game = $this->repository->getById($id); RTIfEntityNotFound($game);
        if ($game->status == 'cancel')
            RTErrorString('比賽已取消');

        $user = $this->repository_user->getByPhone($request->phone); RTIfEntityNotFound($user);
        $member = $this->repository_member->updatePersonnel($user->id, $id, $request->status);

        return view('games.editPersonnel', compact('game', 'member'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $records = $this->repository->listForBackend();

        return view('full_tw_games.view', compact('records'));
    }
}
