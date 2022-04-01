<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\Forms\SubPointFormRequest;
use App\Models\System\Repositories\PointRecordRepository;
use App\Models\Profile\Repositories\UserRepository;

class SubPointController extends Controller
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
        return view('points.sub');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SubPointFormRequest $request)
    {
        $data = $request->validated();

        $user = $this->repository_user->getByPhone($data['mobile']);

        $this->repository->save([
            'category'     => 'Manually',
            'user_id'      => $user->id,
            'point'        => (int)-$data['point'],
            'formData'     => $data,
            'is_confirmed' => 1
         ]);

        $user->decrement('point', $data['point']);
        return view('points.sub', [
            'message' => '退款成功'
        ]);
    }
}