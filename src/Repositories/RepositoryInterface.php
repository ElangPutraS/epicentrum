<?php

namespace Laravolt\Epicentrum\Repositories;

use Illuminate\Http\Request;

/**
 * Interface UserRepository
 * @package namespace App\Repositories;
 */
interface RepositoryInterface
{
    public function findById(int $id);

    public function paginate(Request $request);

    public function createByAdmin(array $attributes, $roles = null);

    public function updateAccount($id, $account, $roles);

    public function updatePassword($password, $id);

    public function delete($id);

    public function availableStatus();
}
