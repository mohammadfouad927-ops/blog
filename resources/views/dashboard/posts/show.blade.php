<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Post Detail ' . $post->id) }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6 mt-6">

        <h1 class="text-3xl font-bold mb-4">{{ $post->title }}</h1>

        <p class="text-gray-700 mb-6">
            {{ $post->description }}
        </p>

        <div class="border-t pt-4 mt-4 text-sm text-gray-600">
            <p><strong>Author:</strong> {{ $post->user->name }}</p>
            <p><strong>Created at:</strong> {{ $post->created_at->format('Y-m-d H:i') }}</p>
            <p><strong>Last updated:</strong> {{ $post->updated_at?->format('Y-m-d H:i') }}</p>
        </div>

        <div class="mt-6 flex gap-3">
            @can('update',$post)
                <a href="#"
                   onclick="editPost({{$post->id}})"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">
                    Edit
                </a>
            @endcan

            <a href="{{ route('posts.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">
                Back to Posts
            </a>
        </div>

    </div>

    <x-modal name="editPostModal" maxWidth="xl">
        <h2 class="text-xl font-bold mb-4 mt-2 text-center">Edit Post</h2>

        <form id="edit-form" method="POST" class="container flex flex-col w-full px-10 py-10">
            @csrf
            @method('PUT')
            <div class="mt-2 w-full">

                <input id="edit-title"
                       name="title"
                       class="block w-38 mb-3 rounded-md w-80"
                       placeholder="Title">
            </div>


            <div class="w-full">
                <textarea id="edit-content"
                          name="description"
                          class="block rounded-md border w-full h-24"
                          placeholder="Content" row="5">
                </textarea>
            </div>


            <div class="mt-5 mb-2 mx-auto">
                <button type="submit" class="bg-sky-500 text-white px-32 py-2 rounded-md hover:bg-sky-700">
                    Save
                </button>

            </div>

        </form>
    </x-modal>

    <script>
        function editPost(id){
            fetch(`${id}/edit`)
                .then(res=> res.json())
                .then(post =>{
                    document.getElementById('edit-form').action = `${id}`;
                    document.getElementById('edit-title').value = post.title;
                    document.getElementById('edit-content').value = post.description;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'editPostModal' }));
                })
        }
    </script>

</x-app-layout>
