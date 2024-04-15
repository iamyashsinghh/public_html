<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $roles_and_permissions = [
            "Admin" => [
                "lead" => ["add", "edit", "view", "forward", "rm-msg"],
            ],
            "Manager" => [
                "lead" => ["view"],
            ],
            "NVRM" => [
                "lead" => ["add", "edit", "view", "forward", "rm-msg"]
            ],
            "RM" => [
                "lead" => ["add", "view"],
                "task" => ["add", "edit"],
                "visit" => ["add", "edit"],
                "note" => ["add", "edit"],
            ],
            "VM" => [
                "lead" => ["add", "view"],
                "task" => ["add", "edit"],
                "visit" => ["add", "edit"],
                "note" => ["add", "edit"],
            ]
        ];

        foreach ($roles_and_permissions as $key_name => $list) {
            $role = new Role();
            $role->name = $key_name;
            $role->permissions = json_encode($list);
            $role->save();
        }
    }
}
