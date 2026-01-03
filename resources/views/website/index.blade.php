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
    @guest
        {{-- Auto-open login/register prompt modal for guests --}}
        <div id="login-modal" aria-hidden="true" class="fixed inset-0 z-50 hidden items-center justify-center">
            <div id="login-modal-overlay" class="absolute inset-0 bg-black bg-opacity-70"></div>

            <div class="relative max-w-3xl w-full mx-4 md:mx-0 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="h-60 md:h-80 bg-cover bg-center" style="background-image: url('{{ asset('images/login-prompt.jpg') }}');"></div>

                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-2">Login to share your ideas</h2>
                    <p class="text-sm text-gray-600 mb-4">Please login or register to post your thoughts and join the discussion.</p>

                    <div class="flex gap-3 items-center">
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Login</a>
                        <a href="{{ route('register') }}" class="border border-gray-300 px-4 py-2 rounded">Register</a>
                        <button id="login-modal-close" class="ml-auto text-sm text-gray-500">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Modal visibility helpers */
            #login-modal.hidden { display: none; }
            #login-modal.show { display: flex; animation: fadeIn .12s ease; }
            @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }

            /* Prevent body scroll when modal open */
            body.modal-open { overflow: hidden; }
        </style>

        <script>
            (function () {
                const modal = document.getElementById('login-modal');
                    const overlay = document.getElementById('login-modal-overlay');
                const closeBtn = document.getElementById('login-modal-close');

                function openModal() {
                    modal.classList.remove('hidden');
                    modal.classList.add('show');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('modal-open');
                    console.log(1);
                }
                function closeModal() {
                    modal.classList.remove('show');
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                }

                // Auto-open on first load for guests
                document.addEventListener('DOMContentLoaded', function () {
                    // Optionally you can show once per session using sessionStorage:
                    if (!sessionStorage.getItem('loginModalShown')) {
                        openModal();
                        sessionStorage.setItem('loginModalShown', '1');
                    }
                    setTimeout(openModal, 10000);

                });

                // Close handlers
                overlay.addEventListener('click', closeModal);
                closeBtn.addEventListener('click', closeModal);
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') closeModal();
                });
            })();
        </script>
    @endguest



        <h1 class="text-3xl font-bold mb-6">Latest Posts</h1>


    <div class="grid gap-6 mt-4" id="post-list">
        @include('website.partials.posts', ['posts' => $posts])
    </div>

    @if ($posts->hasMorePages())
        <button
            id="load-more"
            data-next-page="{{ $posts->currentPage() + 1 }}"
            class="mt-6 px-6 py-2 bg-black text-white rounded"
        >
            Show more
        </button>
    @endif

    <script>
        document.getElementById('load-more')?.addEventListener('click', function () {
            let button = this;
            button.style = 'display: none;';
            let page = button.getAttribute('data-next-page');

            fetch(`?page=${page}`,
                {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('post-list').insertAdjacentHTML('beforeend', html);

                button.setAttribute('data-next-page', parseInt(page) + 1);

                if (!html.trim()) {
                    button.remove();
                }
                else {
                    button.style = '';
                }
            });
        });
    </script>


@endsection
