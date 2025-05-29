<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder para popular a tabela de roles com os papéis padrão da aplicação.
 */
class RoleSeeder extends Seeder
{
    /**
     * Executa o seeder para o banco de dados.
     *
     * Cria os papéis (roles) padrão para a aplicação, incluindo papéis para
     * usuários autenticados localmente e papéis administrativos/operacionais.
     * Todos os papéis são associados ao guard 'web'.
     *
     * Limpa o cache de permissões antes de criar os papéis para garantir consistência.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Papéis base para tipos de usuários (CotaG L12 / AC3 da Issue #2)
        Role::firstOrCreate(['name' => 'usp_user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'external_user', 'guard_name' => 'web']);

        // Papéis administrativos e operacionais (CotaG / AC2 da Issue #2)
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']); // Papel genérico para usuários locais
    }
}