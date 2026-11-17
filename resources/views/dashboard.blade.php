<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                <div class="mt-4 flex gap-4">
                    <a class="underline" href="{{ route('customers.index') }}">Customer Accounts</a>
                    <a class="underline" href="{{ route('users.index') }}">User Accounts</a>
                    <a class="underline" href="{{ route('home') }}">Home</a>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
