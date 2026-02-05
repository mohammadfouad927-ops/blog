@extends('layouts.website')

@section('title', 'Search Results')

@section('content')


<div class="max-w-3xl mx-auto mt-8">
    @if($query !== null)
            <h2 class="text-2xl font-semibold mb-4">Search Results for "{{ request('q') }}"</h2>
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

            @if($posts->isEmpty())
                <p class="text-gray-600">No posts found matching your search criteria.</p>
            @else
                @foreach($posts as $post)
                    <div class="bg-white rounded-xl shadow p-4 mb-6">
                        <h3 class="text-xl font-bold mb-2">{{ $post->title }}</h3>
                        <p class="text-gray-700 mb-2">{{ Str::limit(strip_tags($post->description), 150) }}</p>
                        <p class="mb-2">
                        <a class="text-gray-500 hover:text-black" href="{{route('blog.profile.show',[$post->user->name])}}">{{$post->user->name}}</a>
                        </p>
                        <a href="{{ route('blog.post.show', [$post->user->name, $post->slug]) }}" class="text-blue-600 hover:underline">Read More</a>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $posts->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="container mt-6 bg-white rounded-xl shadow p-4 mb-6">
                <h2 class="text-2xl font-semibold mb-4">Recent Searches </h2>
                <div class="mt-6 grid grid-flow-row gap-2 divide-y-2 divide-solid divide-gray-100" id="recent-searches">

            </div>

            </div>

        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let params = new URLSearchParams(window.location.search);
            if( params.get('q')){
                return;
            }

            const recentSearchesContainer = document.getElementById('recent-searches');
            console.log(recentSearchesContainer);
            const recentSearches = JSON.parse(localStorage.getItem('recent_searches')) || [];
            if (recentSearches.length === 0) {
                console.log('No recent searches found.');
                recentSearchesContainer.innerHTML = "<p class=\"text-gray-600\">You have no recent searches.</p>";
            } else {
                recentSearches.forEach(function(keyword) {
                    const searchRow = createRecentSearchRow();
                    const deleteBtn = createDeleteButton();
                    deleteBtn.addEventListener('click', function(){
                        deleteRecentSearch(keyword, recentSearches, searchRow)
                    });
                    const searchLink = createSearchLink(keyword)
                    searchRow.appendChild(searchLink)
                    searchRow.appendChild(deleteBtn)
                    recentSearchesContainer.appendChild(searchRow);
                });
            }
        });

        function createDeleteButton(){
            const deleteBtn = document.createElement('button');
            deleteBtn.innerHTML = '&#10005;';
            deleteBtn.className = 'text-red-500 text-xl ml-2 hover:text-red-700';
            return deleteBtn;
        }

        function createRecentSearchRow(){
            const searchRow = document.createElement('div');
            searchRow.className = 'flex justify-between items-center';
            return searchRow;
        }

        function createSearchLink(keyword){
            const searchLink = document.createElement('a');
            searchLink.href = `{{ route('blog.search') }}?q=${encodeURIComponent(keyword)}`;
            searchLink.className = 'block text-black hover: mb-2';
            searchLink.textContent = keyword;
            return searchLink;
        }

        function deleteRecentSearch(keyword, recentSearches, searchRow){
                const index = recentSearches.indexOf(keyword);
                if (index > -1) {
                    recentSearches.splice(index, 1);
                    localStorage.setItem('recent_searches', JSON.stringify(recentSearches));
                    searchRow.remove()
                    if(recentSearches.length === 0){
                        const recentSearchesContainer = document.getElementById('recent-searches');
                        recentSearchesContainer.innerHTML = '<p class="text-gray-600">You have no recent searches.</p>';
                    }
                }
        }

    </script>

@endsection
