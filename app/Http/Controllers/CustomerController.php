<?php

namespace App\Http\Controllers;

class CustomerController extends Controller
{
    private array $customers = [
        ['id' => 1, 'name' => 'Maria Santos', 'email' => 'maria@example.com', 'phone' => '0917 123 4567'],
        ['id' => 2, 'name' => 'Juan Dela Cruz', 'email' => 'juan@example.com', 'phone' => '0918 234 5678'],
        ['id' => 3, 'name' => 'Ana Reyes', 'email' => 'ana@example.com', 'phone' => '0919 345 6789'],
        ['id' => 4, 'name' => 'Paolo Garcia', 'email' => 'paolo@example.com', 'phone' => '0920 456 7890'],
        ['id' => 5, 'name' => 'Lea Mendoza', 'email' => 'lea@example.com', 'phone' => '0921 567 8901'],
    ];

    public function index()
    {
        return view('customers', ['customers' => $this->customers]);
    }
}