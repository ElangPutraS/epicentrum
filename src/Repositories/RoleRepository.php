<?php

namespace Laravolt\Epicentrum\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * Boot up the repository, pushing criteria
     */
    public function __construct()
    {
        $this->model = app('laravolt.epicentrum.role');
        $this->fieldSearchable = config('laravolt.epicentrum.repository.searchable', []);
    }

    public function findById(int $id)
    {
        return $this->model->query()->findOrFail($id);
    }

    public function all()
    {
        return $this->model->all();
    }

    /**
     * Save a new entity in repository
     *
     * @param  array  $attributes
     * @param  null  $roles
     * @return mixed
     * @throws \Exception
     */
    public function create(array $attributes)
    {
        $role = $this->model->create($attributes);
        $role->syncPermission($attributes['permissions'] ?? []);

        return $role;
    }

    public function update($id, array $attributes)
    {
        $role = $this->findById($id);
        $role->update($attributes);
        $role->syncPermission($attributes['permissions'] ?? []);

        return $role;
    }

    public function delete($id)
    {
        $model = $this->model->query()->findOrFail($id);

        return $model->delete($id);
    }
}
