@extends('layouts.website')

@section('title','Create Post')


@section('content')
    <form id="post-form" method="POST" action="{{ route('blog.post.store') }}">
        @csrf

        <input type="hidden" name="title" id="form-title">
        <input type="hidden" name="description" id="form-description">
        <div id="wrap-editor">
            <div id="container-title" class="">
                <input type="text" id="input-title" name="title" placeholder="Title" class="font-Georgia font-medium text-5xl px-5 w-full border-none focus:ring-0 focus:shadow-none bg-[#fff]">
            </div>
            <div id="editor-root"></div>
        </div>

        <style>
            #wrap-editor{
                max-width:680px;
                margin: 40px auto;

            }


            .editor-container {
                max-width: 680px;
            }

            .editor-container {
                position: relative;
            }

        </style>

    </form>

@endsection
