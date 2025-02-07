<?php

require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("functions.inc");
require_once("/usr/local/www/widgets/include/interfaces.inc");

// Obtém as interfaces configuradas e seus detalhes
$ifdescrs = get_configured_interface_with_descr();
$interfaces = [];

foreach ($ifdescrs as $ifdescr => $ifname) {
    $ifinfo = get_interface_info($ifdescr);
    $status = ($ifinfo['status'] == "up") ? "UP" : "DOWN";
    $speed = isset($ifinfo['media']) ? $ifinfo['media'] : "Desconhecido";
    $ip_address = isset($ifinfo['ipaddr']) ? $ifinfo['ipaddr'] : "N/A";

    $interfaces[$ifdescr] = [
        'name' => $ifname,
        'status' => $status,
        'speed' => $speed,
        'ip' => $ip_address
    ];
}

// Modificação para considerar a interface selecionada
if ($_REQUEST['ajax']) {
    $selected_interface = isset($_REQUEST['interface']) ? escapeshellarg($_REQUEST['interface']) : '';

    if (!empty($selected_interface)) {
        $results = shell_exec("speedtest --secure --json --source {$selected_interface}");
    } else {
        $results = shell_exec("speedtest --secure --json");
    }

    if (($results !== null) && (json_decode($results) !== null)) {
        $config['widgets']['speedtest_result'] = $results;
        write_config("Save speedtest results");
        echo $results;
    } else {
        echo json_encode(null);
    }
} else {
    $results = isset($config['widgets']['speedtest_result']) ? $config['widgets']['speedtest_result'] : null;
    if (($results !== null) && (json_decode($results, true) === null)) {
        $results = null;
    }
?>
    <select id="interface-select" name="interface-select" class="form-control">
        <option value="">Escolha a interface</option>
        <?php foreach ($interfaces as $iface_name => $iface_data) {
            if ($iface_data['status'] === "UP") {  // Exibir apenas interfaces ativas
                echo "<option value=\"" . htmlspecialchars($iface_name, ENT_QUOTES, 'UTF-8') . "\">";
                echo "{$iface_data['name']} ({$iface_data['ip']})";
                echo "</option>";
            }
        } ?>
    </select>
    <br> 
    <table class="table">
        <tr>
            <td>
                <h4>Ping <i class="fa fa-exchange"></i></h4>
            </td>
            <td>
                <h4>Download <i class="fa fa-download"></i></h4>
            </td>
            <td>
                <h4>Upload <i class="fa fa-upload"></i></h4>
            </td>
        </tr>
        <tr>
            <td>
                <h4 id="speedtest-ping">N/A</h4>
            </td>
            <td>
                <h4 id="speedtest-download">N/A</h4>
            </td>
            <td>
                <h4 id="speedtest-upload">N/A</h4>
            </td>
        </tr>
        <tr>
            <td>ISP <i class="fa fa-network-wired"></i></td>
            <td>Host <i class="fa fa-server"></i></td>
            <td>IP <i class="fa fa-globe"></i></td>
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
        function update_result(results) {
            console.log('Speed Test');
            if (results != null) {
                var date = new Date(results.timestamp);
                $("#speedtest-ts").html(date);
                $("#speedtest-ping").html(results.ping.toFixed(2) + "<small> ms</small>");
                $("#speedtest-download").html((results.download / 1000000).toFixed(2) + "<small> Mbps</small>");
                $("#speedtest-upload").html((results.upload / 1000000).toFixed(2) + "<small> Mbps</small>");
                $("#speedtest-isp").html(results.client.isp);
                $("#speedtest-host").html(results.server.name + ", " + results.server.country + ' <a href="https://www.google.com/maps?q=' + results.server.lat + ',' + results.server.lon + '" target="_blank"><i class="fa fa-map-marker-alt"></i></a>');
                $("#speedtest-ip").html(results.client.ip);
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
            $('#interface-select').on('change', function() {
                console.log('Interface: ' + this.value);
                let int_select = $('#interface-select').val();
                console.log('Interface: ' + int_select);
            });
            let int_select = $('#interface-select').val();
            $.ajax({
                type: 'POST',
                url: "/widgets/widgets/speedtest.widget.php",
                dataType: 'json',
                data: {
                    ajax: "ajax",
                    interface: int_select
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
