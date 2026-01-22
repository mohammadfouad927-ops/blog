 @foreach($posts as $post)
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold">
                <a href="{{route('blog.post.show', [$post->user->name, $post->slug])}}" class="hover:text-blue-600">
                    {{ $post->title }}
                </a>
            </h2>
            <p class="text-gray-600 mt-2">
                {{ Str::limit(strip_tags($post->description), 150) }}
            </p>
            <p class="text-lg mt-2">
                <a href="{{route('blog.profile.show', $post->user)}}" class="hover:text-blue-600">
                    {{ $post->user->name }}
                </a>
            </p>
        </div>
@endforeach
