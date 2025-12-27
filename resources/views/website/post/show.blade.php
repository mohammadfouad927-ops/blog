@extends('layouts.website')

@section('title', $post->title)

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Post Card --}}
        <div class="bg-white rounded-xl shadow p-5">

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-semibold text-lg">
                    {{ strtoupper($user->name[0]) }}
                </div>

                <div>
                    <p class="font-medium">
                        <a href="{{route('blog.profile.show',$user->name)}}" class="hover:text-blue-600">
                            {{ $user->name }}
                        </a>
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $post->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-xl font-semibold mb-3">
                {{ $post->title }}
            </h1>

            {{-- Content --}}
            <p class="text-gray-800 leading-relaxed mb-2 text-base whitespace-pre-line">
                {!! nl2br(e($post->description)) !!}
            </p>

            {{-- Divider --}}
            <hr class="my-4">

            {{-- Actions --}}
            <div class="mt-2 flex gap-2 justify-around text-gray-500 text-base">
                <button class="hover:text-blue-600">👍 Like</button>
                <button class="hover:text-blue-600">💬 Comment</button>
                <button class="hover:text-blue-600">🔗 Share</button>
            </div>
        </div>

        {{-- Back Button --}}
        <a href="{{ route('blog') }}"
           class="block text-center text-blue-600 hover:underline">
            ← Back to posts
        </a>

    </div>
@endsection
