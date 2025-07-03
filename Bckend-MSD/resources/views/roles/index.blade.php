<h1>Lista de Roles</h1>
<ul>
    @foreach ($roles as $role)
        <li>
            {{ $role->name }}
            <a href="{{ route('roles.edit', $role->id) }}">Editar permisos</a>
        </li>
    @endforeach
</ul>
