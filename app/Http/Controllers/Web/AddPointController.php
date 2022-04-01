<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\Forms\AddPointFormRequest;
use App\Models\System\Repositories\PointRecordRepository;
use App\Models\Profile\Repositories\UserRepository;

class AddPointController extends Controller
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
        if (!$user) {
            return view('points.add', [
                'message' => '查無此用戶'
            ]);
        }
        $this->repository->save([
            'category'     => 'Manually',
            'user_id'      => $user->id,
            'point'        => (int)$data['point'],
            'formData'     => $data,
            'is_confirmed' => 1
         ]);

        $user->increment('point', $data['point']);
        return view('points.add', [
            'message' => '儲值成功'
        ]);
    }
}