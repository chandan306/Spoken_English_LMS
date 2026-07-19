

import Alpine from 'alpinejs';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.Alpine = Alpine;

Alpine.start();

// Back To Top Button
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', function () {

    if(window.scrollY > 300){
        backToTop.style.display = 'block';
    }else{
        backToTop.style.display = 'none';
    }

});

backToTop?.addEventListener('click', function(){

    window.scrollTo({
        top:0,
        behavior:'smooth'
    });

});
