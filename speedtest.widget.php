<?php

require_once("guiconfig.inc");

if ($_REQUEST['ajax']) { 
    $results = shell_exec("speedtest --secure --json");
    if(($results !== null) && (json_decode($results) !== null)) {
        $config['widgets']['speedtest_result'] = $results;
        write_config("Save speedtest results");
        echo $results;
    } else {
        echo json_encode(null);
    }
} else {
    $results = isset($config['widgets']['speedtest_result']) ? $config['widgets']['speedtest_result'] : null;
    if(($results !== null) && (!is_object(json_decode($results)))) {
        $results = null;
    }
?>
<table class="table">
    <tr>
        <td><h4>Ping <i class="fa fa-exchange"></h4></td>
        <td><h4>Download <i class="fa fa-download"></i></h4></td>
        <td><h4>Upload <i class="fa fa-upload"></h4></td>
    </tr>
    <tr>
        <td><h4 id="speedtest-ping">N/A</h4></td>
        <td><h4 id="speedtest-download">N/A</h4></td>
        <td><h4 id="speedtest-upload">N/A</h4></td>
    </tr>
    <tr>
        <td>ISP <i class="fa fa-network-wired"></td>
        <td>Host <i class="fa fa-server"></td>
        <td>IP <i class="fa fa-globe"></td>
    </tr>
    <tr>
				<td id="speedtest-isp">N/A</td>
        <td id="speedtest-host">N/A</td>
        <td><span id="speedtest-ip">N/A</span><span id="speedtest-geoip"></span></td>
    </tr>
    <tr>
        <td colspan="3" id="speedtest-ts" style="font-size: 0.8em;">&nbsp;</td>
    </tr>
</table>
<a id="updspeed" href="#" class="fa fa-refresh" style="display: none;"></a>
<script type="text/javascript">

function geoIP(results){
		console.log('IP API');
		$.ajax({
				url: "http://ip-api.com/json/"+results.client.ip, // URL da API
				method: "GET",
				dataType: 'json',
				success: function(response) {
						// Verifica se o status é 'success'
						if (response.status === "success") {
								// Obtém latitude e longitude
								var latitude = response.lat;
								var longitude = response.lon;
								// Exibe o resultado na página
								$('#speedtest-geoip').html('<a href="https://www.google.com/maps?q='+latitude+','+longitude+'" target="_blank"><i class="fa fa-map-marker-alt"></a>');
						} else {
								$('#speedtest-geoip').html("");
						}
				},
				error: function() {
						// Caso ocorra um erro na requisição
						$("#speedtest-geoip").html("");
				}
		});
}

function update_result(results) {
		console.log('Speed Test');
    if(results != null) {
        var date = new Date(results.timestamp);
        $("#speedtest-ts").html(date);
        $("#speedtest-ping").html(results.ping.toFixed(2) + "<small> ms</small>");
        $("#speedtest-download").html((results.download / 1000000).toFixed(2) + "<small> Mbps</small>");
        $("#speedtest-upload").html((results.upload / 1000000).toFixed(2) + "<small> Mbps</small>");
        $("#speedtest-isp").html(results.client.isp);
        $("#speedtest-host").html(results.server.name + ", " + results.server.country + ' <a href="https://www.google.com/maps?q='+results.server.lat+','+results.server.lon+'" target="_blank"><i class="fa fa-map-marker-alt"></a>');
        $("#speedtest-ip").html(results.client.ip);
				//geoIP(results);
    } else {
        $("#speedtest-ts").html("Speedtest failed");
        $("#speedtest-ping").html("N/A");
        $("#speedtest-download").html("N/A");
        $("#speedtest-upload").html("N/A");
        $("#speedtest-isp").html("N/A");
        $("#speedtest-host").html("N/A");
        $("#speedtest-ip").html("N/A");
				$("#speedtest-geoip").html("");
    }
}

function update_speedtest() {
    $('#updspeed').off("click").blur().addClass("fa-spin").click(function() {
        $('#updspeed').blur();
        return false;
    });
    $.ajax({
        type: 'POST',
        url: "/widgets/widgets/speedtest.widget.php",
        dataType: 'json',
        data: {
            ajax: "ajax"
        },
        success: function(data) {
            update_result(data);
        },
        error: function() {
            update_result(null);
        },
        complete: function() {
            $('#updspeed').off("click").removeClass("fa-spin").click(function() {
                update_speedtest();
                return false;
            });
        }
    });
}
events.push(function() {
    var target = $("#updspeed").closest(".panel").find(".widget-heading-icon");
    $("#updspeed").prependTo(target).show();
    $('#updspeed').click(function() {
        update_speedtest();
        return false;
    });
    update_result(<?php echo ($results === null ? "null" : $results); ?>);
});
</script>
<?php } ?>
