<<<<<<< Updated upstream
=======
const burger = document.querySelector('.burger')
const navLink = document.querySelector('.nav')
const burger2 = document.querySelector('.burger2')
const navLink2 = document.querySelector('.nav2')

// burger.addEventListener('click', burgerAction)


// function burgerAction(e){
//     e.preventDefault();
//     if(!navLink.classList.contains('active')) {
//         navLink.classList.add('active')
//         navLink.style.display = "block";
//         console.log("Ouvert");
        
//     } else {
//         navLink.classList.remove('active')
//         navLink.style.display = "none";
//         console.log("Fermé");
//     }
// }


function toggle(a, b) {
    if (!a.classList.contains(b)) {
        a.classList.add(b)
        a.style.display = "block";
    } else {
        a.classList.remove(b)
        a.style.display = "none";
    }
}

burger.addEventListener('click', ()=> {
    toggle(navLink, 'active')
})


burger2.addEventListener('click', ()=> {
    toggle(navLink2, 'show')
})
>>>>>>> Stashed changes
