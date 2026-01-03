@extends('layouts.website')
@section('title', 'Search Results')

@section('content')
    <div class="max-w-3xl mx-auto mt-8">


        @if($query !== null)
            <h2 class="text-2xl font-semibold mb-4">Search Results for "{{ $query }}"</h2>
        <div class="max-w-3xl mx-auto mt-8">
            <div class="flex gap-4 border-b border-gray-200 mb-4">
                <a href="{{ route('blog.search', ['q' => request('q')]) }}"
                   class="px-3 py-2 font-medium {{ request()->routeIs('blog.search') ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Posts
                </a>

                <a href="{{ route('blog.search.users', ['q' => request('q')]) }}"
                   class="px-3 py-2 font-medium {{ request()->routeIs('blog.search.users') ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Users
                </a>
            </div>

            @if($users->isEmpty())
                <p class="text-gray-600">No users found matching your search criteria.</p>
            @else
                <div class="grid gap-4">
                    @foreach($users as $user)
                        <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4 hover:shadow-md transition">
                            {{-- Avatar --}}
                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center font-bold text-lg text-gray-700">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            {{-- User info --}}
                            <div class="flex-1">
                                <a href="{{ route('blog.profile.show', $user->name) }}" class="text-lg font-semibold text-gray-900 hover:underline">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </a>
                                <p class="text-gray-500 text-sm">
                                    {{$user->about_me?? 'Description Description Description Description Description Description Description Description Description Description Description Description'}}</p>
                            </div>

                            {{-- Optional: action button --}}
                            <a href="{{ route('blog.profile.show', $user->name) }}" class="text-blue-600 hover:underline text-sm">
                                View Profile
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        @else
            <p class="text-gray-500">No search query provided.</p>
        @endif
    </div>

@endsection
