<?php

use Pingu\Core\Seeding\DisableForeignKeysTrait;
use Pingu\Core\Seeding\MigratableSeeder;
use Pingu\Permissions\Entities\Permission;

class S2019_09_30_184926941877_InstallField extends MigratableSeeder
{
    use DisableForeignKeysTrait;

    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        Permission::findOrCreate(['name' => 'view revisions', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'restore revisions', 'helper' => 'Will also need the create permission for each entity', 'section' => 'Core']);
    }

    /**
     * Reverts the database seeder.
     */
    public function down(): void
    {
        // Remove your data
    }
}
