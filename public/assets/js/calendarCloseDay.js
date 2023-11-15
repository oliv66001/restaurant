function validateDate() {
	console.log("Fonction validateDate appelée");
  const selectedDayOfMonth = $('#calendar_start_date_day').val();
  const selectedMonth = $('#calendar_start_date_month').val() - 1;
  const selectedYear = $('#calendar_start_date_year').val();
  const selectedTime = $('#calendar_start_time_hour').val();
  const selectedDate = new Date(Date.UTC(selectedYear, selectedMonth, selectedDayOfMonth, selectedTime));
  const selectedDayOfWeek = selectedDate.getUTCDay();

  
  console.log("Jour de la semaine calculé:", selectedDayOfWeek);
  console.log('Jours fermés:', closedDays);
  console.log('Jour sélectionné:', selectedDayOfWeek);
  console.log('Heure sélectionnée:', selectedTime);

  if (closedDays.includes(selectedDayOfWeek)) {
    alert('Le restaurant est fermé ce jour-là. Veuillez choisir un autre jour.');
    $('#calendar_start_date_day').val('');  // Réinitialise le champ du jour
  }
}

$(document).ready(function () {
  $('#calendar_start_date_day').on('change', validateDate);
  $('#calendar_start_date_month').on('change', validateDate);
  $('#calendar_start_date_year').on('change', validateDate);
  $('#calendar_start_time_hour').on('change', validateDate);
});
