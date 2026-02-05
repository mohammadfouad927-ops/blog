<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4">
        <div class="h-16 flex items-center justify-between">

            <!-- LEFT SIDE -->
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('blog') }}" class="text-xl font-bold whitespace-nowrap">
                    TechBlog
                </a>

                <!-- Search -->
                <div class="relative w-72">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-400"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                    </span>

                    <input
                        type="text"
                        name="query"
                        id="searchInput"
                        class="w-full pl-10 pr-4 py-2 border rounded-full
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Search"
                        autocomplete="off"
                    >

                    <div id="searchMenu" class="hidden w-full absolute mt-1 left-3 border rounded shadow flex flex-col p-4 bg-white z-50" role="listbox">
                        <div id="container-recent-search" class="hidden">
                            <p class="text-gray-400 border-b-2"> RECENT SEARCHES </p>
                            <div id="recent-searches-menu" role="listbox">
                            </div>
                        </div>

                        <div id="auto-complete-search" class="hidden">
                            <p class="text-gray-400 border-b-2"> USERS </p>
                            <div id="auto-complete-search-menu">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auth -->
            @auth
                <!-- RIGHT SIDE -->
                <div class="flex items-center gap-4">

                    {{-- Write / Publish --}}
                    @if(request()->routeIs("blog.post.create"))
                        <button
                            id="publish-post"
                            class="bg-green-600 text-white px-4 py-2 rounded-full
                                   hover:bg-green-700 transition"
                        >
                            Publish
                        </button>
                    @else
                        <a
                            href="{{ route('blog.post.create') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-full
                                   border border-gray-300 text-gray-700
                                   hover:bg-gray-100 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V7.828
                                         a2 2 0 00-.586-1.414l-3.828-3.828
                                         A2 2 0 0012.172 2H6a2 2 0 00-2 2v14
                                         a2 2 0 002 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14.5 3.5l6 6"/>
                            </svg>

                            <span class="text-sm font-medium hidden sm:inline">
                                Write
                            </span>
                        </a>
                    @endif

                    <!-- Divider -->
                    <div class="h-6 w-px bg-gray-300"></div>


                    <div class="relative">
                        <button id="userMenuButton"
                                class="flex items-center gap-2 focus:outline-none">
                            <span class="text-gray-700">
                                {{ auth()->user()->name }}
                            </span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="userMenu"
                             class="hidden absolute right-0 mt-2 w-40
                                    bg-white border rounded shadow z-30">
                            <a href="{{ route('blog.settings.edit') }}"
                               class="block px-4 py-2 hover:bg-gray-100">
                                Settings
                            </a>

                            <a href="{{ route('blog.profile.show',[Auth::user()]) }}"
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
    </div>
</nav>
