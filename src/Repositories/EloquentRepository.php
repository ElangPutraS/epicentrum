<?php

namespace Laravolt\Epicentrum\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class EloquentRepository implements RepositoryInterface
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
        $this->model = app(config('auth.providers.users.model'));
        $this->fieldSearchable = config('laravolt.epicentrum.repository.searchable', []);
    }

    public function findById(int $id)
    {
        return $this->model->query()->findOrFail($id);
    }

    public function paginate(Request $request)
    {
        $query = $this->model->autoSort()->autoFilter()->latest();
        if (($search = $request->get('search')) !== null) {
            $query->whereLike($this->fieldSearchable, $search);
        }

        return $query->paginate();
    }

    /**
     * Save a new entity in repository
     *
     * @param  array  $attributes
     * @param  null  $roles
     * @return mixed
     * @throws \Exception
     */
    public function createByAdmin(array $attributes, $roles = null)
    {
        $attributes['password_last_set'] = new Carbon();
        if (array_has($attributes, 'must_change_password')) {
            $attributes['password_last_set'] = null;
        }

        $attributes['password'] = bcrypt($attributes['password']);

        $user = $this->model->fill($attributes);
        $user->save();
        $user->syncRoles($roles);

        return $user;
    }

    public function updateAccount($id, $account, $roles)
    {
        $user = $this->findById($id);
        $user->update($account);

        if (config('laravolt.epicentrum.role.editable')) {
            $user->roles()->sync($roles);
        }

        return $user;
    }

    public function updatePassword($password, $id)
    {
        $user = $this->skipPresenter()->find($id);
        $user->setPassword($password);

        return $user->save();
    }

    public function delete($id)
    {
        $model = $this->model->query()->findOrFail($id);

        if (in_array(SoftDeletes::class, class_uses($this->model))) {
            $model->email = sprintf("[deleted-%s]%s", $model->id, $model->email);
            $model->save();
        }

        return $model->delete();
    }

    public function availableStatus()
    {
        return config('laravolt.epicentrum.user_available_status');
    }
}
