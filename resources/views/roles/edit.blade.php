@extends(config('modules.users.view.layout'))

@section('content')
    <section class="ui container page">
        <div class="ui centered grid">
            <div class="ten wide column">
                <div class="ui segment very padded">

                    <h2 class="ui header text centered"><span>Edit</span> Role</h2>

                    {!! SemanticForm::open()->put()->action(route('admin.roles.update', $role['id'])) !!}
                    <div class="field required">
                        <label>@lang('roles.name')</label>
                        {!! SemanticForm::text('name', old('name', $role['name'])) !!}
                    </div>

                    <table class="ui table">
                        <thead>
                        <tr>
                            <th>
                                <div class="ui checkbox" data-toggle="checkall" data-selector=".checkbox[data-type='check-all-child']">
                                    <input type="checkbox">
                                    <label><strong>Hak Akses</strong></label>
                                    <input type="hidden" name="permissions[]" value="0">
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($permissions as $permission)
                            <tr>
                                <td>
                                    <div class="ui checkbox" data-type="check-all-child">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ (in_array($permission->id, $assignedPermissions))?'checked=checked':'' }}>
                                        <label>{{ $permission->name }}</label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="ui divider hidden"></div>

                    <button class="ui button primary" type="submit" name="submit" value="1">@lang('button.save')</button>
                    <a href="{{ route('admin.roles.index') }}" class="ui button">@lang('button.cancel')</a>
                    {!! SemanticForm::close() !!}

                </div>

                <div class="ui segment very padded red">
                    <h3 class="">Hapus Role</h3>
                    <p>Saat ini ada <strong>{{ $role->users->count() }} pengguna</strong> yang memiliki role
                        <strong>{{ $role->name }}</strong>.</p>
                    <p>Dengan menghapus role ini, maka para pengguna tersebut otomatis juga akan kehilangan hak akses yang dimilikinya dari role ini.</p>
                    {!! SemanticForm::open()->delete()->action(route('admin.roles.destroy', $role['id'])) !!}
                    <button class="ui button red" type="submit" name="submit" value="1">@lang('button.delete')</button>
                    {!! SemanticForm::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection