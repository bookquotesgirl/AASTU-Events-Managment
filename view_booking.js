document.addEventListener('DOMContentLoaded', function(){
    const userEmail=localStorage.getItem('userEmail');
    const userRole=localStorage.getItem('userRole');

    const backHome=document.getElementById('home');
    backHome.addEventListener('click', function(){
        window.location.href='index.html';
    });
    const userEmail=localStorage.getItem('userEmail');
    fetch('php/view_my_bookings?email=' + encodeURIComponent(userEmail) + '&role=' + encodeURIComponent(userRole))
    .then(response=>response.json())
    ,then(data=>{
        const bookingList=document.getElementById('bookingList');
        if(data.length===0){
            bookingList.innerHTML='<p>No bookings fouund.</p>';
            return;
        }
        data.forEach(booking=>{
            const div=document.createElement('div');
            div.className='booking';
            div.innerHTML=`
            <p><strong>Event:</strong>${booking.event_name}</p>
                <p><strong>Ticket Type:</strong>${booking.tickets}</p>
                    <p><strong>User Name:</strong>${booking.user_name}</p>
                        <p><strong>Email:</strong>${booking.user_email}</p>
            `;
            bookingList.appendChild(div);
        });
    })
    .catch(error=>{
        console.error('Error fetching bookings:', error);
    });
    
});