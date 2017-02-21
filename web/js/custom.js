$(document).ready(function () {

$('.user').hide();

   $('#adminlink').click(function() {
     $('.admin').toggle();
  });

  $('#userlink').click(function() {
    $('.user').toggle();
 });

 $('.delete').click(function() {
   $("#clientusername").html($(this).data('value'));
   $('#confirmdelete').data("value", $(this).data('value'));
});

$('#confirmdelete').click(function() {
  $('#form_id').val($(this).data('value'));
  $( "#deleteform" ).trigger("submit");
});



});
