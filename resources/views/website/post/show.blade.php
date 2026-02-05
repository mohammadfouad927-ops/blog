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
            <div class="post-content text-gray-800 leading-relaxed text-base">
                {!! $post->description !!}
            </div>

            {{-- Divider --}}
            <hr class="my-4">

            {{-- Actions --}}
            <div class="mt-2 flex gap-2 justify-around text-gray-500 text-base">
                <livewire:like-button :post="$post" />
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

    <style>
        /* ── Paragraphs ──────────────────────────────── */
        .post-content p {
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        /* ── Headings ────────────────────────────────── */
        .post-content h3 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .post-content h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }

        /* ── Links ───────────────────────────────────── */
        .post-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .post-content a:hover {
            color: #1d4ed8;
        }

        /* ── Quote style 1 – gray ────────────────────── */
        .post-content blockquote,
        .post-content .quote-style-1 {
            border-left: 4px solid #9CA3AF;
            background-color: #F9FAFB;
            color: #4B5563;
            font-style: italic;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        /* ── Quote style 2 – blue ────────────────────── */
        .post-content .quote-style-2 {
            border-left: 4px solid #3B82F6;
            background: linear-gradient(to right, #DBEAFE, transparent);
            color: #1E40AF;
            font-style: italic;
            font-weight: 500;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        /* ── Bold / Italic (safety net) ──────────────── */
        .post-content strong,
        .post-content b {
            font-weight: 700;
        }

        .post-content em,
        .post-content i {
            font-style: italic;
        }
    </style>
@endsection
