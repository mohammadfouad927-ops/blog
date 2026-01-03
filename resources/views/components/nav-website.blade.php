<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16 items-center">

            <!-- Left -->
            <a href="{{ route('blog') }}" class="text-xl font-bold">
                TechBlog
            </a>
            <div class="relative w-1/3 ">

                <input type="text" name="query" id="searchInput"
                       class="w-3/4 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Search..."
                       role="combobox"
                       aria-expanded="false"
                       aria-controls="searchMenu"
                       aria-autocomplete="list"
                       autocomplete="off">


                <div id="searchMenu" class="hidden w-full absolute mt-1 left-3 border rounded shadow flex flex-col p-4 bg-white" role="listbox">
                    <p class="text-gray-400 border-b-2"> RECENT SEARCHES </p>
                    <div id="recent-searches-menu">
                    </div>

                </div>

            </div>

            <style>
                #searchMenu:before{
                    content: "";
                    position: absolute;
                    top: -6px;
                    left: 20px;
                    border-width: 0 6px 6px 6px;
                    border-style: solid;
                    border-color: transparent transparent white transparent;
                }
            </style>



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
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const searchMenu  = document.getElementById('searchMenu');
        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');
        let recentSearches = JSON.parse(localStorage.getItem('recent_searches')) || [];
        let activeIndex = -1;
        let openedByClick = false;



        if (btn) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }


        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();

                if (query) {
                    let recent_searches = JSON.parse(localStorage.getItem('recent_searches')) || [];

                    if(recent_searches.indexOf(query) === -1){
                        recent_searches.unshift(query);
                        localStorage.setItem('recent_searches', JSON.stringify(recent_searches));
                    }

                    this.value = '';
                }
                    window.location.href = `{{ route('blog.search') }}?q=` + encodeURIComponent(query);
            }

        });

        if(recentSearches.length){
            recentSearches.forEach(function(keyword){
                const searchRow = document.createElement('div');
                searchRow.className = "px-4 py-2 flex justify-between items-center hover:bg-gray-100 focus:bg-gray-100";
                searchRow.setAttribute('role', 'option');
                searchRow.setAttribute('tabindex', '-1');

                const searchLink = document.createElement('a');
                searchLink.href = `{{ route('blog.search') }}?q=${encodeURIComponent(keyword)}`;
                searchLink.className = "flex-grow";
                searchLink.textContent = keyword;

                const btnDelete = document.createElement('button');
                btnDelete.className = "text-red-500 hover:text-red-700";
                btnDelete.innerHTML = "&#10005;";
                btnDelete.addEventListener('click', function(e){
                    const index = recentSearches.indexOf(keyword);
                    if (index > -1){
                        recentSearches.splice(index, 1);
                        localStorage.setItem('recent_searches', JSON.stringify(recentSearches));
                        searchRow.remove();
                        if(recentSearches.length === 0){
                            document.getElementById('recent-searches-menu').innerHTML = '<p class="text-gray-600">You have no recent searches.</p>';
                        }
                        e.stopPropagation();
                    }
                })

                searchRow.appendChild(searchLink);
                searchRow.appendChild(btnDelete);
                document.getElementById('recent-searches-menu').appendChild(searchRow);
            })
        }else{
            document.getElementById('recent-searches-menu').innerHTML = '<p class="text-gray-600">You have no recent searches.</p>';
        }

        searchInput.addEventListener('mousedown', function(){
            openedByClick = true;
        })

        searchInput.addEventListener('focus',function(){
            if(!openedByClick){
                openMenu();
            }
        })

        searchInput.addEventListener('click', function(e){
            e.preventDefault();
            let searchMenu = document.getElementById('searchMenu')
            if(searchMenu.classList.contains('hidden')){
                openMenu();
            }else{
                closeMenu();
                openedByClick = false;
                this.blur();
            }

            // e.stopPropagation();

        },true);


        searchInput.addEventListener('keydown', (e) => {
            const options = [...document.querySelectorAll('#recent-searches [role="option"]')];

            if (!options.length) return;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    activeIndex = (activeIndex + 1) % options.length;
                    options[activeIndex].focus();
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    activeIndex = (activeIndex - 1 + options.length) % options.length;
                    options[activeIndex].focus();
                    break;

                case 'Escape':
                    closeMenu();
                    searchInput.blur();
                    break;
            }
        });

        document.addEventListener('click',function(event){
            if (!searchMenu.contains(event.target) && event.target !== searchInput) {
                closeMenu();
                openedByClick = false;
            }
        });

        function openMenu() {
            searchMenu.classList.remove('hidden');
            searchInput.setAttribute('aria-expanded', 'true');
        }

        function closeMenu() {
            searchMenu.classList.add('hidden');
            searchInput.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
        }

    });
</script>
