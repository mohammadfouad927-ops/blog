
function createDeleteBtn(keyword, row, onDelete){
    const btnDelete = document.createElement('button');
    btnDelete.className = "text-red-500 hover:text-red-700";
    btnDelete.innerHTML = "&#10005;";

    btnDelete.addEventListener('click', function(e){
        e.stopPropagation();
        onDelete(keyword);
        row.remove();
    })

    return btnDelete;
}

function createSearchLink(baseUrl, keyword){
    const searchLink = document.createElement('a');
    searchLink.href = `${baseUrl}?q=${encodeURIComponent(keyword)}`;
    searchLink.className = "flex-grow";
    searchLink.textContent = keyword;
    return searchLink;
}

function createSearchRow(){
    const searchRow = document.createElement('div');
    searchRow.className = "px-4 py-2 flex justify-between items-center hover:bg-gray-100 focus:bg-gray-100";
    searchRow.setAttribute('role', 'option');
    searchRow.setAttribute('tabindex', '-1');
    return searchRow;
}

export function createSearchRecentCard(baseUrl, keyword, onDelete){
    const searchRow = createSearchRow();
    const link = createSearchLink(baseUrl, keyword);
    const deleteBtn = createDeleteBtn(keyword, searchRow, onDelete);

    searchRow.appendChild(link);
    searchRow.appendChild(deleteBtn);
    return searchRow;
}


export function renderEmptyRecentSearchState(container) {
    // show "You have no recent searches"
    container.innerHTML = '<p class="text-gray-600">You have no recent searches.</p>';
}
