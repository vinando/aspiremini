<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupController extends Controller
{
    //
    function index() {
        /* Create admin user */
        $admin = new User;
        $admin->name = 'Administrator';
        $admin->email = 'admin@aspire-cap.com';
        $admin->password = Hash::make('admin123');
        $admin->save();

        /* Create default test user */
        $testUser = new User;
        $testUser->name = 'Test User';
        $testUser->email = 'testuseru@aspire-cap.com';
        $testUser->password = Hash::make('testuser123');
        $testUser->save();

        /* Create Admin Role admin permissions */
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'approve-loan']);
        $adminRole->givePermissionTo($permission);
        $permission = Permission::create(['name' => 'reject-loan']);
        $adminRole->givePermissionTo($permission);
        $permission = Permission::create(['name' => 'verify-payment']);
        $adminRole->givePermissionTo($permission);

        $admin = User::find(1);
        $admin->assignRole('admin');
        return response(json_encode(['status'=>'setup completed', 'users'=>['Administrator'=>$admin, 'Test User'=>$testUser]]), 200)->header('Content-Type', 'application/json');
    }
}
