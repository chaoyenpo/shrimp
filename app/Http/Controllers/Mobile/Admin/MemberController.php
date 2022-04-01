<?php

namespace App\Http\Controllers\Mobile\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Profile\Forms\UserForm;
use App\Models\Profile\Forms\UserFormRequest;
use App\Models\Profile\Repositories\UserRepository;

class MemberController extends Controller
{
    private $repository;
    private $form;

    public function __construct(UserRepository $repository,
                                UserForm $form)
    {
        $this->repository = $repository;
        $this->form = $form;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    public function changePower(Request $request)
    {
        $user = $request->user();
        if ($user->firebase_uid !== 'Z9ZbvnvN8EOngcf93ggSeuZiGwB3')
            RTErrorString('權限不足');

        $record = $this->repository->getByPhone($request->phone); RTIfEntityNotFound($record);

        if ($request->type == "shrimper"){
            if ($request->action == "add")        $data = ['is_shrimper' => 1];
            elseif ($request->action == "remove") $data = ['is_shrimper' => 0];
        }elseif ($request->type == "vendor"){
            if ($request->action == "add")        $data = ['is_vendor' => 1];
            elseif ($request->action == "remove") $data = ['is_vendor' => 0];
        }elseif ($request->type == "recycler"){
            if ($request->action == "add")        $data = ['is_recycler' => 1];
            elseif ($request->action == "remove") $data = ['is_recycler' => 0];
        }
        if (empty($data))
            return response()->json(['success' => false]);

        $record->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Models\Profile\Forms\UserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Models\Profile\Forms\UserFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }
}