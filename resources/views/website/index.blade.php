@extends('layouts.website')

@section('title', 'TechBlog')

@section('content')

    {{-- Create Post --}}
        @auth
            <div class="bg-white rounded-xl shadow p-4 mb-8">
                <form method="POST" action="{{ route('blog.post.store',[Auth::user()->name]) }}">
                    @csrf


                    {{-- Content --}}
                    <div class="flex flex-row flex-wrap gap-2 ">

                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-semibold">
                            {{ strtoupper(auth()->user()->name[0]) }}
                        </div>

                        {{-- Title --}}
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Post title"
                            class="basis-5/6 mb-3 px-4 py-2 rounded-full bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >

                        <textarea
                            name="description"
                            rows="3"
                            placeholder="What's on your mind, {{ auth()->user()->name }}?"
                            class="w-full resize-none rounded-xl px-4 py-2 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-between items-center mt-3">
                        <div class="flex gap-4 text-sm text-gray-500">
                            <span class="cursor-pointer hover:text-blue-600">📷 Photo</span>
                            <span class="cursor-pointer hover:text-blue-600">😊 Feeling</span>
                        </div>

                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-5 py-1.5 rounded-full font-medium hover:bg-blue-700"
                        >
                            Post
                        </button>
                    </div>

                    {{-- Errors --}}
                    @error('title')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        @endauth



        <h1 class="text-3xl font-bold mb-6">Latest Posts</h1>

    <div class="grid gap-6">
        @foreach($posts as $post)
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-semibold">
                    <a href="{{route('blog.post.show', [$post->user->name, $post->slug])}}" class="hover:text-blue-600">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="text-gray-600 mt-2">
                    {{ Str::limit($post->description, 150) }}
                </p>
                <p class="text-lg mt-2">
                    <a href="{{route('blog.profile.show', $post->user)}}" class="hover:text-blue-600">
                        {{ $post->user->name }}
                    </a>
                </p>
            </div>
        @endforeach
    </div>
@endsection
