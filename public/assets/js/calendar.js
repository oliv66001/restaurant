document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function() {
        const startInput = document.getElementById("calendar_start");
        const numberOfGuestsInput = document.getElementById("calendar_numberOfGuests");

        function updateAvailablePlaces() {
    let year = document.querySelector("#calendar_start [name='calendar[start][date][year]']").value;
    let month = document.querySelector("#calendar_start [name='calendar[start][date][month]']").value;
    let day = document.querySelector("#calendar_start [name='calendar[start][date][day]']").value;
    let hour = document.querySelector("#calendar_start [name='calendar[start][time][hour]']").value;
    let minute = document.querySelector("#calendar_start [name='calendar[start][time][minute]']").value;
          
    let start = `${year}-${month}-${day} ${hour}:${minute}`;

    let numberOfGuests = numberOfGuestsInput.value;
   

            if (start && numberOfGuests) {
                console.log(numberOfGuests);
                fetch(
                    `/calendar/remaining-places-api?start=${encodeURIComponent(
                      start
                    )}&numberOfGuests=${encodeURIComponent(numberOfGuests)}`
                    
                  )
                  
                .then(response => {
                return response.json();
                
                
        })
        
                .then(data => {
                document.getElementById("calendar_availablePlaces").value = data.remainingPlaces;
               
               
    })
   
            } else {
                document.getElementById("calendar_availablePlaces").value = '';
            }
        }

        
        startInput.addEventListener("change", updateAvailablePlaces);
        numberOfGuestsInput.addEventListener("change", updateAvailablePlaces);
        startInput.addEventListener("input", updateAvailablePlaces);
        numberOfGuestsInput.addEventListener("input", updateAvailablePlaces);
    }, 500);
});
