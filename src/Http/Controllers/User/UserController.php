<?php

namespace Laravolt\Epicentrum\Http\Controllers\User;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Contracts\Requests\Account\Delete;
use Laravolt\Epicentrum\Contracts\Requests\Account\Store;
use Laravolt\Epicentrum\Mail\AccountInformation;
use Laravolt\Epicentrum\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Mail;
use Laravolt\Epicentrum\Repositories\TimezoneRepository;
use Laravolt\Epicentrum\Table\UserTable;

class UserController extends Controller
{
    /**
     * @var UserRepositoryEloquent
     */
    protected $repository;

    /**
     * @var TimezoneRepositoryArray
     */
    protected $timezone;

    /**
     * UserController constructor.
     * @param  UserRepositoryEloquent  $repository
     * @param  TimezoneRepositoryArray  $timezone
     */
    public function __construct(RepositoryInterface $repository, TimezoneRepository $timezone)
    {
        $this->repository = $repository;
        $this->timezone = $timezone;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $users = $this->repository->paginate($request);

        return UserTable::make($users)->view('epicentrum::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $statuses = $this->repository->availableStatus();
        $roles = app('laravolt.epicentrum.role')->all()->pluck('name', 'id');
        $multipleRole = config('laravolt.epicentrum.role.multiple');

        return view('epicentrum::create', compact('statuses', 'roles', 'multipleRole'));
    }

    /**
     * Store the specified resource.
     *
     * @param  Store  $request
     * @return Response
     */
    public function store(Store $request)
    {
        // save to db
        $roles = $request->get('roles', []);
        $user = $this->repository->createByAdmin($request->except('_token'), $roles);
        $password = $request->get('password');

        // send account info to email
        if ($request->has('send_account_information')) {
            Mail::to($user)->send(new AccountInformation($user, $password));
        }

        return redirect()->route('epicentrum::users.index')->withSuccess(trans('epicentrum::message.user_created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return redirect(route('epicentrum::account.edit', $id));
    }

    public function destroy(Delete $request, $id)
    {
        try {
            $this->repository->delete($id);

            return redirect(route('epicentrum::users.index'))->withSuccess(trans('epicentrum::message.user_deleted'));
        } catch (QueryException $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
