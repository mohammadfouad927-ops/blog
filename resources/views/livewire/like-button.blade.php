<button
    wire:click="toggleLike"
    wire:loading.attr="disabled"
    class="inline-flex items-center gap-2"
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="currentColor"
        viewBox="0 0 24 24"
        width="24"
        class="transition-colors duration-200
            {{ $likedByMe ? 'text-red-500' : 'text-gray-400' }}"
    >
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                 2 5.42 4.42 3 7.5 3
                 9.24 3 10.91 3.81 12 5.08
                 13.09 3.81 14.76 3 16.5 3
                 19.58 3 22 5.42 22 8.5
                 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
    </svg>

    <span>{{ $likesCount }}</span>
</button>
