<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Post') }}
        </h2>
    </x-slot>



    <form action="{{route('posts.store')}}" method="POST" class="mx-10 mt-10">
        @csrf
        <div class="sm:col-span-2">
            <label for="title" class="block text-sm/6 font-semibold text-gray-900">Title</label>
            <div class="mt-2.5">
                <input id="title" type="text" name="title" autocomplete="organization" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
        </div>
        <div class="sm:col-span-2 mt-3">
            <label for="description" class="block text-sm/6 font-semibold text-gray-900">Description</label>
            <div class="mt-2.5">
                <textarea id="description" name="description" rows="4" class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>
        <div class="mt-10 flex flex-row-reverse  ">
            <button type="submit" class="block w-1/4 rounded-md bg-sky-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-xs hover:bg-sky-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">Save</button>
        </div>
    </form>
</x-app-layout>
