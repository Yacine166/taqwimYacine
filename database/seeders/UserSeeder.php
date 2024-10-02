<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    //create admin
    User::create([
      'name'              => 'admin',
      'email'             => env('ADMIN_EMAIL',"admin@admin.app"),
      'password'          => bcrypt(env('ADMIN_PASSWORD',"admin")),
      'phone'             => env('ADMIN_PHONE', '0123456789'),
      'locale'            => 'en',
      'email_verified_at' => now(),
      'is_admin'          => true
    ]);

  }
}
