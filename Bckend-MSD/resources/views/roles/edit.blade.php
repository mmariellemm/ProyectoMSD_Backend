<h1>Editar permisos para: {{ $role->name }}</h1>

<form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf
    @method('PUT')

    @foreach ($permissions as $permission)
        <div>
            <label>
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                {{ $permission->name }}
            </label>
        </div>
    @endforeach

    <button type="submit">Guardar</button>
</form>
