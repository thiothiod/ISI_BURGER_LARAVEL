<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $gestionnaire = Role::create(['name' => 'gestionnaire']);
        $client       = Role::create(['name' => 'client']);

        // Créer le compte gestionnaire
        $admin = User::create([
            'name'     => 'Gestionnaire ISI',
            'email'    => 'admin@isiburger.com',
            'password' => Hash::make('password'),
            'phone'    => '+221 77 000 00 00',
            'address'  => 'Dakar, Sénégal',
        ]);

        $admin->assignRole('gestionnaire');

        // Créer un compte client de test
        $clientUser = User::create([
            'name'     => 'Client Test',
            'email'    => 'client@isiburger.com',
            'password' => Hash::make('password'),
            'phone'    => '+221 76 123 45 67',
            'address'  => 'Dakar, Plateau',
        ]);

        $clientUser->assignRole('client');

        $this->command->info('✅ Rôles et utilisateurs créés !');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Gestionnaire', 'admin@isiburger.com',  'password'],
                ['Client',       'client@isiburger.com', 'password'],
            ]
        );
    }
}