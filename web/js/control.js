

$(document).ready(function () {
   $('.action').click(function() {
     $.gritter.add({
       title:	'Action in progress',
       text:	'Your action is in progress.',
       image: 	'../img/spinner.gif',
       sticky: true
       });
     $('#form_action').val($(this).data('value'));
     if ($(this).data('value') == "mainip") {
       $('#form_value').val($(this).data('setting'));
     } else {
       $('#form_value').val($(".value[data-value="+ $(this).data('value') +"]").val());
     }
     console.log($('#form_value').val());
     $('.value').val('');
     $( "#actionform" ).trigger("submit");
   });

    $('#actionform').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url : $(this).attr('action') || window.location.pathname,
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
               $('.gritter-item-wrapper').remove();
                if (data == 1) {
                  $.gritter.add({
                    title:	'Success',
                    text:	'Your action was completed successfully.',
                    image: 	'../img/tick.png',
                    time: 8000,
                    sticky: false
                  });
                    status();
                } else {
                  $.gritter.add({
                    title:	'Error',
                    text:	'There was an error with your action.',
                    image: 	'../img/cross.png',
                    time: 8000,
                    sticky: false
                  });
                }
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});

function status() {
			$(function() {$.getJSON(window.location.pathname + "/json",function(result){
        if (result.data.status == "running") {
          $("#power").html("Online");
          $("#power").attr('class','label label-success');
        } else if (result.data.status == "suspended") {
          $("#power").html("Suspended");
          $("#power").attr('class','label label-inverse');
        } else {
          $("#power").html("Offline");
          $("#power").attr('class','label label-important');
        }
        if (result.data.tuntap == 1) {
          $("#tunon").attr('disabled', true);
          $("#tunoff").attr('disabled', false);
        } else {
          $("#tunoff").attr('disabled', true);
          $("#tunon").attr('disabled', false);
        }
        if (result.data.fuse == 1) {
          $("#fuseon").attr('disabled', true);
          $("#fuseoff").attr('disabled', false);
        } else {
          $("#fuseoff").attr('disabled', true);
          $("#fuseon").attr('disabled', false);
        }
	$("#loadavg").html(result.data.loadavg);
	$("#ram").html(result.data.ram);
	$("#vswap").html(result.data.swap);
	$("#disk").html(result.data.disk);
	$("#node").html(result.data.node);
	$(".hostname").html(result.data.hostname);
	$("#ip").html(result.data.ip);
  $("#os").html(result.data.os);
	$("#ram_bar").html(result.data.ram_percent + "%");
	$("#ram_bar").width(result.data.ram_percent + "%");
  $("#disk_bar").html(result.data.disk_percent + "%");
	$("#disk_bar").width(result.data.disk_percent + "%");
  $("#vswap_bar").html(result.data.swap_percent + "%");
  $("#vswap_bar").width(result.data.swap_percent + "%");
  $(".action[data-value='mainip']").show();
  $(".action[data-setting='"+ result.data.ip + "']").hide();
	});});
	}
	status();
	setInterval(status, 60000);
