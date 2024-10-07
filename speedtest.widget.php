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
        <td>ISP</td>
        <td colspan="2" id="speedtest-isp">N/A</td>
    </tr>
    <tr>
        <td>Host</td>
        <td colspan="2" id="speedtest-host">N/A</td>
    </tr>
    <tr>
        <td colspan="3" id="speedtest-ts" style="font-size: 0.8em;">&nbsp;</td>
    </tr>
</table>
<a id="updspeed" href="#" class="fa fa-refresh" style="display: none;"></a>
<script type="text/javascript">
function update_result(results) {
    if(results != null) {
        var date = new Date(results.timestamp);
        $("#speedtest-ts").html(date);
        $("#speedtest-ping").html(results.ping.toFixed(2) + "<small> ms</small>");
        $("#speedtest-download").html((results.download / 1000000).toFixed(2) + "<small> Mbps</small>");
        $("#speedtest-upload").html((results.upload / 1000000).toFixed(2) + "<small> Mbps</small>");
        $("#speedtest-isp").html(results.client.isp);
        $("#speedtest-host").html(results.server.name + ", " + results.server.country);
    } else {
        $("#speedtest-ts").html("Speedtest failed");
        $("#speedtest-ping").html("N/A");
        $("#speedtest-download").html("N/A");
        $("#speedtest-upload").html("N/A");
        $("#speedtest-isp").html("N/A");
        $("#speedtest-host").html("N/A");
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
