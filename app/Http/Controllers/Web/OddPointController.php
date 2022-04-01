<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\Forms\OddPointFormRequest;
use App\Models\System\Repositories\PointRecordRepository;
use App\Models\Profile\Repositories\UserRepository;

class OddPointController extends Controller
{
    private $repository;
    private $repository_user;

    public function __construct(PointRecordRepository $repository, UserRepository $repository_user)
    {
        $this->repository = $repository;
        $this->repository_user  = $repository_user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('points.add');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AddPointFormRequest $request)
    {
        $data = $request->validated();

        $user = $this->repository_user->getByPhone($data['mobile']);

        $this->repository->save([
            'category'     => 'manually',
            'user_id'      => $user->id,
            'point'        => (int)$data['point'],
            'formData'     => json_encode($data),
            'is_confirmed' => 1
         ]);

        $user->increment('point', $data['point']);
        RTMsgString('儲值成功');
        return view('points.add');
    }
}