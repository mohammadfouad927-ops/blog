const KEY = 'recent_searches';

export function getRecentSearches(){
    return JSON.parse(localStorage.getItem(KEY)) || [];
}

export function addRecentSearch(query){
    const searches = getRecentSearches();

    if(!searches.includes(query)){
        searches.unshift(query);
        localStorage.setItem(KEY, JSON.stringify(searches));
    }
}

export function removeRecentSearch(query){
    const searches = getRecentSearches();
    const index = searches.indexOf(query);
    if(index > -1){
        searches.split(index,1);
        localStorage.setItem(KEY, JSON.stringify(searches));
        return searches.length;
    }
    return false;
}
