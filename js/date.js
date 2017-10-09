$(function () {
    myDate();   
});

//UPDATES DATE EVERY SECOND
function myDate() {
	var d = new Date();
	var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
	var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
	var mod = (d.getHours() < 12 ? "AM" : "PM");
	var hour = (d.getHours() % 12 === 0 ? 12 : d.getHours() % 12);
	var minutes = ((d.getMinutes() < 10) ? "0" + d.getMinutes() : d.getMinutes());
	var time = days[d.getDay()] + ", " + months[d.getMonth()] + " " + d.getDate() + " " + hour + ":" + minutes + " " + mod;
	if ($("#headerDate").html() != time) {
	    $("#headerDate").html(time);
	}
	setTimeout(myDate, 1000);
}