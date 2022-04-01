<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Illustration\Forms\IllustrationFormRequest;
use App\Models\Illustration\Repositories\IllustrationRepository;

class IllustrationController extends Controller
{
    private $repository;

    public function __construct(
        IllustrationRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->repository->list();

        return view('illustrations.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('illustrations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Illustration\Forms\IllustrationFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IllustrationFormRequest $request)
    {
        $data = $request->validated();
        $data['data'] = [];
        $lengths = count($request->weight);
        for ($i=0; $i<$lengths; $i++) {
            array_push($data['data'], [
                'weight'     => $request->weight[$i],
                'head_in'    => $request->head_in[$i],
                'head_out'   => $request->head_out[$i],
                'footer_in'  => $request->footer_in[$i],
                'footer_out' => $request->footer_out[$i]
            ]);
        }
        $this->repository->save($data);

        return redirect('illustration');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);
        $record = ['id'           => $record->id,
                   'name'         => $record->name,
                   'steps'        => $record->steps,
                   'lengths'      => $record->lengths,
                   'data'         => $record->data,
                   'photo1'       => $record->photo1,
                   'photo2'       => $record->photo2,
                   'reviews'      => $record->reviews,
                   'price'        => $record->price,
                   'manufacturer' => $record->manufacturer,
                   'brand'        => $record->brand,
                   'youtube'      => $record->youtube];

        return view('illustrations.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Illustration\Forms\IllustrationFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(IllustrationFormRequest $request, $id)
    {
        $data = $request->validated();
        $record = $this->repository->getById($id); RTIfEntityNotFound($record);

        $data['data'] = [];
        $lengths = count($request->weight);
        for ($i=0; $i<$lengths; $i++) {
            array_push($data['data'], [
                'weight'     => $request->weight[$i],
                'head_in'    => $request->head_in[$i],
                'head_out'   => $request->head_out[$i],
                'footer_in'  => $request->footer_in[$i],
                'footer_out' => $request->footer_out[$i]
            ]);
        }
        $record->update($data);

        return redirect('illustration');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $record = $this->repository->getById($request->id); RTIfEntityNotFound($record);
        $record->delete();

        return 1;
    }
}
