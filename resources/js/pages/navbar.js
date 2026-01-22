import {debounce} from "../utils/debounce.js";
import {searchUsers} from "../services/searchServices.js";
import {getRecentSearches, addRecentSearch, removeRecentSearch} from "../services/recentSearchServices.js";
import {createUserCard} from "../ui/searchMenu.js";
import {createSearchRecentCard, renderEmptyRecentSearchState} from "../ui/recentSearch.js";

const userMenu = document.getElementById('userMenu');
const searchInput =  document.getElementById('searchInput');
const searchMenu = document.getElementById('searchMenu');
const recentSearchMenu = document.getElementById('recent-searches-menu');
const containerRecentSearch = document.getElementById('container-recent-search');
const autoCompleteSearchMenu = document.getElementById('auto-complete-search-menu');
const autoCompleteSearch = document.getElementById('auto-complete-search');
let activeIndex = -1;
let openedByClick = false;
let recentSearches = getRecentSearches();
const btn = document.getElementById('userMenuButton')
const publishBtn = document.getElementById('publish-post');



if(btn){
    btn.addEventListener('click', () => {
        userMenu.classList.toggle('hidden');
    })
}

if(publishBtn){
    publishBtn.addEventListener('click', () => {
        const titleInput = document.getElementById('input-title');
        const formTitle = document.getElementById('form-title');
        const formDescription = document.getElementById('form-description');

        if (!titleInput.value.trim()) {
            alert('Title is required');
            titleInput.focus();
            return;
        }

        if (!window.__EDITOR_HTML__ || window.__EDITOR_HTML__.trim() === '') {
            alert('Post content is empty');
            return;
        }

        formTitle.value = titleInput.value;
        formDescription.value = window.__EDITOR_HTML__;

        document.getElementById('post-form').submit();
    });
}

function handleSearchInputKeypress(e){
    if (e.key === 'Enter') {
        e.preventDefault();
        const query = searchInput.value.trim();

        if (query) {
            if(getRecentSearches().indexOf(query) === -1){
                addRecentSearch(query);
            }

            searchInput.value = '';
        }
        window.location.href = `/blog/search?q=` + encodeURIComponent(query);
    }

}

function openMenu() {
    autoCompleteSearchMenu.innerHTML = '';
    autoCompleteSearch.classList.add('hidden');
    searchMenu.classList.remove('hidden');
    containerRecentSearch.classList.remove('hidden');
    searchInput.setAttribute('aria-expanded', 'true');
}

function closeMenu() {
    searchMenu.classList.add('hidden');
    containerRecentSearch.classList.remove('hidden');
    searchInput.setAttribute('aria-expanded', 'false');
    activeIndex = -1;
}

function hideContainerRecentSearch() {
    containerRecentSearch.classList.add('hidden');
}

function showContainerRecentSearch() {
    if(searchMenu.classList.contains('hidden')){
        openMenu();
    }
    autoCompleteSearchMenu.innerHTML = '';
    autoCompleteSearch.classList.add('hidden');
    containerRecentSearch.classList.remove('hidden')
}

const handleSearch = debounce(async function(){
    const value = this.value.trim();
    if(value.length !== 0){
        hideContainerRecentSearch();
        const users = await searchUsers(value)

        if (this.value.trim() !== value) return;

        if(users.length === 0){
            closeMenu();
        }

        renderAutoComplete(users);

    }else if (this.value.length === 0){
        showContainerRecentSearch()
    }

}, 900)

if(recentSearches.length !== 0){
    recentSearches.forEach(function(keyword){
        const searchRecentCard = createSearchRecentCard('/blog/search', keyword,(k) => {
            let recentSearchLength = removeRecentSearch(k);
            if( recentSearchLength === 0 ){
                renderEmptyRecentSearchState(recentSearchMenu);
            }
        });

        recentSearchMenu.appendChild(searchRecentCard);
    })
}else{
    renderEmptyRecentSearchState(recentSearchMenu)
}

function handleInputClick(e) {
        e.preventDefault();
        if(searchMenu.classList.contains('hidden')){
            openMenu();
        }else{
            closeMenu();
            openedByClick = false;
            this.blur();
        }

}

function handleSearchInputKeydown(e){
    const options = [...document.querySelectorAll('#container-recent-search [role="option"]')];

    if (!options.length) return;

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            activeIndex = (activeIndex + 1) % options.length;
            console.log(options[activeIndex].children[0]);
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
}

function handleSearchMenuKeydown(e){
    const options = [...document.querySelectorAll('#container-recent-search [role="option"]')];

    if (!options.length) return;

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            activeIndex = (activeIndex + 1) % options.length;
            console.log(options[activeIndex].children[0]);
            options[activeIndex].focus();
            break;

        case 'ArrowUp':
            e.preventDefault();
            activeIndex = (activeIndex - 1 + options.length) % options.length;
            options[activeIndex].focus();
            break;

        case 'Escape':
            searchInput.focus();
            closeMenu();
            break;
    }
}

function renderAutoComplete(users){
    autoCompleteSearch.classList.remove('hidden');
    autoCompleteSearchMenu.innerHTML = '';

    for(const user of users) {
        const userCard = createUserCard(user);
        autoCompleteSearchMenu.appendChild(userCard);
    }
}

searchInput.addEventListener('keypress',  (e) => handleSearchInputKeypress(e));
searchInput.addEventListener('input', handleSearch)
searchInput.addEventListener('keydown', (e) => handleSearchInputKeydown(e) );
searchInput.addEventListener('mousedown', () => openedByClick = true)
searchInput.addEventListener('focus',function(){
    if(!openedByClick){
        openMenu();
    }
})
searchInput.addEventListener('click', (e) => handleInputClick(e) ,true);
searchMenu.addEventListener('keydown', (e) => handleSearchMenuKeydown(e));
document.addEventListener('click',function(event){
    if (!searchMenu.contains(event.target) && event.target !== searchInput) {
        closeMenu();
        openedByClick = false;
    }
});


