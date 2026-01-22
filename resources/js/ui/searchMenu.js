export function createUserCard(user){
    const userCard = document.createElement('div');
    userCard.className = "px-4 py-2 flex items-center gap-3 hover:bg-gray-100 focus:bg-gray-100";
    userCard.setAttribute('role', 'option');
    userCard.setAttribute('tabindex', '-1');

    const avater = document.createElement('div');
    avater.className = "w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white font-bold";
    avater.textContent = user.name.charAt(0).toUpperCase();

    const userName = document.createElement('span');
    userName.textContent = user.first_name + " " + user.last_name;

    const userLink = document.createElement('a');
    userLink.href = `http://localhost:8000/blog/@${user.name}`;
    userLink.className = "flex-grow";

    userLink.appendChild(userName);
    userCard.appendChild(avater);
    userCard.appendChild(userLink);
    return userCard;
}


