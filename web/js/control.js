

$(document).ready(function () {
   $('.action').click(function() {
     $('#form_action').val($(this).data('value'));
     if ($(this).data('value') == "mainip") {
       $('#form_value').val($(this).data('setting'));
     } else {
       $('#form_value').val($(".value[data-value="+ $(this).data('value') +"]").val());
     }
     console.log($('#form_value').val());
     $('.value').val('');
     $( "#actionform" ).trigger("submit");
     $.jGrowl("The requested action is in progress.", {
       sticky: false,
       life: 5000,
       position: 'top-right',
       theme: 'bg-blue'
     });
   });

    $('#actionform').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url : $(this).attr('action') || window.location.pathname,
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
                if (data == 1) {
                  $.jGrowl("The requested action was completed successfully.", {
                    sticky: false,
                    life: 10000,
                    position: 'top-right',
                    theme: 'bg-green'
                  });
                    status();
                } else {
                  $.jGrowl("There was an error completing the requested action.", {
                    sticky: false,
                    life: 10000,
                    position: 'top-right',
                    theme: 'bg-red'
                  });
                }
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });

    $('#tuntapbutton').on('switchChange.bootstrapSwitch', function(event, state) {
      if (state == true) {
          $('#tuntapval').val('on');
      } else {
          $('#tuntapval').val('off');
      }
    });
});

function status() {
    $("#spinner").show();
			$(function() {$.getJSON(window.location.pathname + "/json",function(result){
        if (result.status == "running") {
          $("#status").html("Online");
          $("#uptime").html("Running for " + result.uptime);
          $("#status").attr('class','bs-label label-success');
        } else {
          $("#status").html("Offline");
          $("#status").attr('class','bs-label label-danger');
        }
	$("#node").html(result.node);
	$(".hostname").html(result.name);
	$("#ip").html(result.ip);
  $("#os").html(result.os);
  $("#nameserver").val(result.nameserver);
  $("#processes").html(result.nproc);
  $("#mem").html(result.mem);
  $("#availablemem").html(result.availablemem);
  var width = Math.round($("#memprogress").width() * (result.ram_percent/100));
  $("#memprogress_value").width(width);
  $("#memprogress1").html(result.ram_percent+"%");
  $("#swap").html(result.swap);
  $("#availableswap").html(result.availableswap);
  var width = Math.round($("#swapprogress").width() * (result.swap_percent/100));
  $("#swapprogress_value").width(width);
  $("#swapprogress1").html(result.swap_percent+"%");
  $("#cpu").html(result.cpu);
  $("#availablecpu").html(result.cpus);
  var width = Math.round($("#cpuprogress").width() * (result.cpu/100));
  $("#cpuprogress_value").width(width);
  $("#cpuprogress1").html(result.cpu+"%");
  $("#disk").html(result.disk);
  $("#availabledisk").html(result.availabledisk);
  var width = Math.round($("#diskprogress").width() * (result.disk_percent/100));
  $("#diskprogress_value").width(width);
  $("#diskprogress1").html(result.disk_percent+"%");
  $("#spinner").hide();
  $('#tuntapbutton').bootstrapSwitch('state', result.tuntap);
	});});
	}
	status();
	setInterval(status, 60000);
