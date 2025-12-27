@extends('layouts.website')

@section('title', $user->name.' -- TechBlog')

@section('content')

    <div class="max-w-2xl mx-auto space-y-6">

        <h1 class="text-2xl font-semibold">{{$user->name}}</h1>

        @forelse($posts as $post)
            <div class="bg-white rounded-xl shadow-sm border p-5 relative hover:bg-gray-50 ">
                @canany(['update', 'delete'], $post)
                {{-- Dropdown --}}
                    <div class="w-8 h-4 absolute top-4 right-0">
                        <details class="relative">
                            <summary class="cursor-pointer text-gray-400 hover:text-gray-600">

                            </summary>

                            <div class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow z-10">
                                <a href="{{ route('blog.post.edit', [$post->user, $post]) }}"
                                   class="block px-4 py-2 text-sm hover:bg-gray-100">
                                    ✏ Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('blog.post.destroy', [$post->user, $post]) }}"
                                      onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        🗑 Delete
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                @endcanany

                {{-- Meta --}}
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <span>{{ $post->created_at->format('M d, Y') }}</span>
                </div>

                {{-- Title --}}
                <h2 class="text-xl font-semibold mb-2 leading-snug">
                    {{ $post->title }}
                </h2>

                {{-- Excerpt --}}
                <p class="text-gray-700 leading-relaxed">
                    {{ Str::limit($post->description, 180) }}
                </p>

                {{-- Read more --}}
                <a href="{{ route('blog.post.show', [$user, $post]) }}"
                   class="inline-block mt-3 text-sm font-medium text-green-700 hover:underline">
                    Read more →
                </a>
            </div>
        @empty
            <p class="text-gray-500 text-center">You haven't created any posts yet.</p>
        @endforelse
        {{$posts->links()}}

    </div>

@endsection
