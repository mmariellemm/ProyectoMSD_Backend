use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

public function run()
{
    // Crear roles
    $admin = Role::create(['name' => 'Administrador']);
    $employee = Role::create(['name' => 'Empleado']);

    // Crear permisos
    $viewReports = Permission::create(['name' => 'ver reportes']);
    $editUsers = Permission::create(['name' => 'editar usuarios']);

    // Asignar permisos a roles
    $admin->givePermissionTo([$viewReports, $editUsers]);
    $employee->givePermissionTo([$viewReports]);
}
