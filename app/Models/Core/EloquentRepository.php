<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllPaginated($count)
    {
        return $this->model->paginate($count);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getAllById($id)
    {
        return $this->model->withTrashed()->find($id);
    }

    public function count()
    {
        return $this->model->count();
    }

    public function countAll()
    {
        return $this->model->withTrashed()->count();
    }

    public function getNew($attributes = [])
    {
        return $this->model->newInstance($attributes);
    }

    /**
     * @param String $identifier
     * @return Entity
     */
    public function findByIdentifier(String $identifier)
    {
        return $this->model->where('identifier', $identifier)
                           ->orderBy('updated_at', 'DESC')
                           ->first();
    }

    /**
     * @param String $column
     * @param String $operate
     * @param Any $value
     * @return Query
     */
    public function where($column, $operate, $value)
    {
        return $this->model->where($column, $operate, $value);
    }

    /**
     * @param String $column
     * @param Array $value
     * @return Query
     */
    public function whereIn($column, $value)
    {
        return $this->model->whereIn($column, $value);
    }

    /**
     * @param String $column
     * @param Array $value
     * @return Query
     */
    public function whereNotIn($column, $value)
    {
        return $this->model->whereNotIn($column, $value);
    }

    public function save($data)
    {
        if ($data instanceOf Model){
            return $this->update($data);
        } elseif (is_array($data)){
            return $this->storeArray($data);
        }
    }

    public function updateOrCreate($data)
    {
        return $this->model->updateOrCreate($data);
    }

    protected function storeEloquentModel($model)
    {
        if ($model->getDirty()){
            $model->save();
        } else {
            $model->touch();
        }
        return $model;
    }

    protected function storeArray($data)
    {
        $model = $this->getNew($data);
        return $this->storeEloquentModel($model);
    }

    public function deleteAll()
    {
        $this->model->query()->delete();
    }

    public function delIds($data)
    {
        return $this->model->whereIn('id', $data)
                           ->delete();
    }

    public function delExceptIds($data)
    {
        return $this->model->whereNotIn('id', $data)
                           ->delete();
    }
}