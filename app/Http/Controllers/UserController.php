<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    private array $users = [
        ['id' => 1, 'name' => 'Sean Admin', 'email' => 'sean@altfpos.test', 'role' => 'Admin'],
        ['id' => 2, 'name' => 'Carla Ramos', 'email' => 'carla@altfpos.test', 'role' => 'Manager'],
        ['id' => 3, 'name' => 'Miguel Tan', 'email' => 'miguel@altfpos.test', 'role' => 'Cashier'],
        ['id' => 4, 'name' => 'Isa Cruz', 'email' => 'isa@altfpos.test', 'role' => 'Cashier'],
        ['id' => 5, 'name' => 'Rico Lim', 'email' => 'rico@altfpos.test', 'role' => 'Manager'],
    ];

    public function index()
    {
        return view('users', ['users' => $this->users]);
    }
}