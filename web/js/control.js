

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
       sticky: true,
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
                  $(".jGrowl-notification:last-child").remove();
                  $.jGrowl("The requested action was completed successfully.", {
                    sticky: false,
                    life: 10000,
                    position: 'top-right',
                    theme: 'bg-green'
                  });
                    status();
                } else {
                  $(".jGrowl-notification:last-child").remove();
                  if (data == 0) {
                    data = "";
                  }
                  $.jGrowl("There was an error completing the requested action.<br>"+ data, {
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

    $('#shutdownbutton').bootstrapSwitch('state', false);

    $('#shutdownbutton').on('switchChange.bootstrapSwitch', function(event, state) {
      if (state == true) {
          $('#shutdownval').val('on');
      } else {
          $('#shutdownval').val('off');
      }
    });
});

function graph() {
  $(".graphpanel").hide();
  $("#graphspinner").show();
  if ($("#graphselect").val() != "null") {
    $("#cpugraph").attr("src" , window.location.pathname + "/graph?type=cpu&period="+$("#graphselect").val());
    $("#memgraph").attr("src" , window.location.pathname + "/graph?type=mem&period="+$("#graphselect").val());
    $("#netingraph").attr("src" , window.location.pathname + "/graph?type=netin&period="+$("#graphselect").val());
    $("#netoutgraph").attr("src" , window.location.pathname + "/graph?type=netout&period="+$("#graphselect").val());
    $("#diskgraph").load(function() {
      $("#graphspinner").hide();
      $(".graphpanel").show();
    }).attr("src" , window.location.pathname + "/graph?type=disk&period="+$("#graphselect").val());

  }
}


$(document).on('change','#graphselect',graph);


function status() {
    $("#spinner").show();
			$(function() {$.getJSON(window.location.pathname + "/json?type=status",function(result){
        if (result == 0) {
          $("#controlpanel").hide();
          $("#downerror").show();
        } else {
          $("#controlpanel").show();
          $("#downerror").hide();
          if (result.status == "running") {
            $("#status").html("Online");
            $("#uptime").show();
            $("#uptime").html("Running for " + result.uptime);
            $("#status").attr('class','bs-label label-success');
          } else if (result.status == "suspended") {
            $("#status").html("Suspended");
            $("#status").attr('class','bs-label label-black');
            $("#uptime").hide();
          } else {
            $("#status").html("Offline");
            $("#status").attr('class','bs-label label-danger');
            $("#uptime").hide();
          }
        	$("#node").html(result.node);
        	$(".hostname").html(result.name);
        	$("#ip").html(result.ip);
          $("#os").html(result.os);
          $("#nameserver").val(result.nameserver);
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
          if (result.status == "stopped") {
            $("#disk").html('??');
          } else {
            $("#disk").html(result.disk);
          }
          $("#availabledisk").html(result.availabledisk);
          var width = Math.round($("#diskprogress").width() * (result.disk_percent/100));
          $("#diskprogress_value").width(width);
          $("#diskprogress1").html(result.disk_percent+"%");
      }
      $("#spinner").hide();
	});});
	}

  function ip() {
    $(function() {$.getJSON(window.location.pathname + "/json?type=ip",function(result){
      for (var i = 0; i<result.length; i++) {
        var ip = result[i];
        $('#ipv4').append('<tr><td data-title="IP">' + ip.ip + '</td><td data-title="Gateway">' + ip.gateway + '</td><td data-title="Netmask">' + ip.netmask + '</td><td data-title="Interface">' + ip.interface + '</td><td data-title="Reverse DNS">' + ip.rdns + ' &nbsp;&nbsp;&nbsp;<a class="btn btn-border btn-alt border-blue-alt btn-link font-blue-alt" href="#" title=""><span>Change</span></a></td></tr>');
      }
    });});
  }
  ip();
	status();
	setInterval(status, 60000);
