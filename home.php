<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['user'])) $_SESSION['user'] = new Account();
if (!$_SESSION['user']->authenticated) {
    header('Location: login.php');
    exit;
}

$_SESSION['user']->update();
$title = 'Home';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>
<div class="content">
    <canvas id="incomeChart">Your browser does not support the canvas element.</canvas>
</div>
<script>
    async function fetchJson(url) {
        let data = await fetch(url)
        let json = await data.json()
        return json;
    }

    function generateDatasetObject(id, label, color, data) {
        data = data[id];

        let dataset = {
            label: label,
            data: [],
            backgroundColor: color,
            borderColor: color,
        }

        for (let [key, value] of Object.entries(data)) {
            let data = {};
            data.x = key;
            data.y = value;
            dataset.data.push(data);
        }

        dataset.data.sort((a, b) => new Date(a.x) - new Date(b.x));
        return dataset;
    }

    const config = {
        type: 'line',
        data: {
            datasets: []
        },
        options: {
            scales: {
                x: {
                    type: "time",
                    time: {
                        unit: "day",
                    },
                },
            },

            // Container for pan options
            pan: {
                enabled: true,
                mode: 'xy'
            },

            // Container for zoom options
            zoom: {
                drag: true,
                enabled: true,
                mode: 'xy',
            }
        }
    };

    const incomeChart = new Chart(
        document.getElementById('incomeChart'),
        config
    );

    (async () => {
        let data = await fetchJson('<?=API_PATH?>/get_transactions_totals.php');

        incomeChart.data.datasets.push(generateDatasetObject('OmzetVoorWitwas', 'Omzet voor Witwassen', 'rgb(130,57,0)', data.data));
        incomeChart.data.datasets.push(generateDatasetObject('OmzetNaWitwas', 'Omzet na Witwassen', 'rgb(51,114,0)', data.data));
        incomeChart.data.datasets.push(generateDatasetObject('MoneyForSlave', 'Geld naar slaven', 'rgb(255,51,90)', data.data));
        incomeChart.data.datasets.push(generateDatasetObject('Profit', 'Winst', 'rgb(38,162,43)', data.data));

        incomeChart.update();
    })();


</script>
<?php include 'includes/footer.php'; ?>