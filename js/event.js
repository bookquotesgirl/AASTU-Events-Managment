document.addEventListener('DOMContentLoaded', () => {
    const getTicketButton=document.getElementById('tic');
    const logo=document.getElementById('logo');
    const selectTicketButton=document.getElementById('tib');
    const popup=document.getElementById('popup');
    const cancel=document.getElementById('cancel');
    const button=document.getElementById('check');

    getTicketButton.addEventListener('click',()=>{
        popup.style.display='block';
    });
    selectTicketButton.addEventListener('click',()=>{
        popup.style.display='block';
    });
cancel.addEventListener('click', () =>{
    popup.style.display='none';
});
popup.addEventListener('click', (e) =>{
    if(e.target===popup){
    popup.style.display='none';
    }
});
button.addEventListener('click', function(){
    window.location.href='checkout.html';
});
logo.addEventListener('click', function(){
    window.location.href='index.html';
});
document.getElementById("cancel").addEventListener("click", function () {
    document.getElementById("popup").classList.remove("active");
});

})