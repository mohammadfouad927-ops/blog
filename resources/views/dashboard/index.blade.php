<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Users -->
            <div class="bg-white p-6 shadow-sm rounded-lg hover:bg-gray-300 hover:cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-700">Users</h3>
                <p class="text-3xl font-bold text-blue-600">
                    {{ $users }}
                </p>
            </div>

            <!-- Admins -->
            <div class="bg-white p-6 shadow-sm rounded-lg hover:bg-gray-300 hover:cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-700">Admins</h3>
                <p class="text-3xl font-bold text-green-600">
                    {{ $admins }}
                </p>
            </div>

            <!-- Posts -->
            <div class="bg-white p-6 shadow-sm rounded-lg hover:bg-gray-300 hover:cursor-pointer" onclick="window.location.replace(window.location.href+'/posts')">
                <h3 class="text-lg font-semibold text-gray-700">Posts</h3>
                <p class="text-3xl font-bold text-purple-600">
                    {{ $posts }}
                </p>
            </div>

        </div>


    </div>
</x-app-layout>
