
function time() {
    var currentdate = new Date();
    var datetime = currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/"
                + currentdate.getFullYear() + " "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes();

  $("#search").html(datetime);
}
$(document).ready(function () {
  time();
});
setInterval(time, 30000);
