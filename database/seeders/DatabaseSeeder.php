<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\TeamMember;
use App\Models\VendorCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call([
            RoleSeeder::class
        ]);

        //insert admin member
        $member = new TeamMember();
        $member->role_id = Role::first()->id;
        $member->name = "Super User";
        $member->mobile = "9988776655";
        $member->email = "admin@gmail.com";
        $member->save();

        //insert vendors categories
        $category_names = ["Photography", "Makeup Artist", "Mehndi Artist"];
        foreach ($category_names as $name) {
            $category = new VendorCategory();
            $category->name = $name;
            $category->save();
        }
    }
}
