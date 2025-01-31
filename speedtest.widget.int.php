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

if ($_REQUEST['ajax']) {
    $interface = escapeshellarg($_REQUEST['interface'] ?? '');
    if (!empty($interface)) {
        $results = shell_exec("speedtest --secure --interface={$interface} --json");
    } else {
        $results = shell_exec("speedtest --secure --json");
    }

    if (!empty($results) && json_decode($results, true) !== null) {
        $config['widgets']['speedtest_result'] = $results;
        write_config("Save speedtest results");
        echo $results;
    } else {
        echo json_encode(null);
    }
} else {
    $results = isset($config['widgets']['speedtest_result']) ? $config['widgets']['speedtest_result'] : null;
    if (!empty($results) && json_decode(trim($results), true) === null) {
        $results = null;
    }
?>

    <label for="interface-select"><strong>Escolha a interface:</strong></label>
    <select id="interface-select" class="form-control">
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
            <td><h4>Ping <i class="fa fa-exchange"></i></h4></td>
            <td><h4>Download <i class="fa fa-download"></i></h4></td>
            <td><h4>Upload <i class="fa fa-upload"></i></h4></td>
        </tr>
        <tr>
            <td><h4 id="speedtest-ping">N/A</h4></td>
            <td><h4 id="speedtest-download">N/A</h4></td>
            <td><h4 id="speedtest-upload">N/A</h4></td>
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
        function update_speedtest() {
            let selectedInterface = $("#interface-select").val();
            if (!selectedInterface) {
                alert("Por favor, selecione uma interface.");
                return;
            }

            $('#updspeed').off("click").blur().addClass("fa-spin").click(() => false);
            $.ajax({
                type: 'POST',
                url: "/widgets/widgets/speedtest.widget.php",
                dataType: 'json',
                data: {
                    ajax: "ajax",
                    interface: selectedInterface
                },
                success: update_result,
                error: () => update_result(null),
                complete: function() {
                    $('#updspeed').off("click").removeClass("fa-spin").click(() => {
                        update_speedtest();
                        return false;
                    });
                }
            });
        }

        function update_result(results) {
            console.log('Speed Test');
            if (results && results.client && results.server) {
                var date = results.timestamp ? new Date(results.timestamp).toLocaleString() : "Data indisponível";
                $("#speedtest-ts").html(date);
                $("#speedtest-ping").html(results.ping.toFixed(2) + "<small> ms</small>");
                $("#speedtest-download").html((results.download / 1000000).toFixed(2) + "<small> Mbps</small>");
                $("#speedtest-upload").html((results.upload / 1000000).toFixed(2) + "<small> Mbps</small>");
                $("#speedtest-isp").html(results.client.isp);
                $("#speedtest-host").html(results.server.name + ", " + results.server.country + ' <a href="https://www.google.com/maps?q=' + results.server.lat + ',' + results.server.lon + '" target="_blank"><i class="fa fa-map-marker-alt"></i></a>');
                $("#speedtest-ip").html(results.client.ip);
                geoIP(results);
            } else {
                $("#speedtest-ts").html("Speedtest failed");
                $(".table h4, .table td").html("N/A");
                $("#speedtest-geoip").html("");
            }
        }

        function geoIP(results) {
            if (!results.client || !results.client.ip) return;
            $.ajax({
                url: "https://ipwho.is/" + results.client.ip,
                method: "GET",
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#speedtest-geoip').html('<a href="https://www.google.com/maps?q=' + response.latitude + ',' + response.longitude + '" target="_blank"><i class="fa fa-map-marker-alt"></i></a>');
                    } else {
                        $('#speedtest-geoip').html("");
                    }
                },
                error: function() {
                    $("#speedtest-geoip").html("");
                }
            });
        }

        if (typeof events !== "undefined" && events.push) {
            events.push(function() {
                $("#updspeed").prependTo($("#updspeed").closest(".panel").find(".widget-heading-icon")).show();
                $('#updspeed').click(() => {
                    update_speedtest();
                    return false;
                });
                update_result(<?php echo htmlspecialchars(($results === null ? "null" : $results), ENT_QUOTES, 'UTF-8'); ?>);
            });
        }
    </script>
<?php } ?>
