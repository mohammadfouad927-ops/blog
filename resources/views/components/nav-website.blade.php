<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16 items-center">

            <!-- Left -->
            <a href="{{ route('blog') }}" class="text-xl font-bold">
                TechBlog
            </a>

            <!-- Right -->
            @auth
                <div class="relative">
                    <button id="userMenuButton"
                            class="flex items-center gap-2 focus:outline-none">
                        <span class="text-gray-700">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div id="userMenu"
                         class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow">
                        <a href="{{ route('blog.profile.edit') }}"
                           class="block px-4 py-2 hover:bg-gray-100">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-blue-600">
                    Login
                </a>
            @endauth

        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');

        if (btn) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
    });
</script>
