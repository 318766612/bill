<?php
include("header.php");
include_once("inc/function.php");
$first_year = get("year", 2019);
$get_year = get("year", $this_year);
?>
    <div class="table stat">
        <div class="itlu-title">
            <select name="year" id="year" style="width: 88px">
                <?php
                if (isset($_COOKIE['selectYear'])) {
                    $get_year = $_COOKIE['selectYear'];
                }
                for ($y = $first_year; $y <= $this_year; $y++) {
                    if ($get_year == $y) {
                        echo "<option value='$y' selected>$y</option>";
                    } else {
                        echo "<option value='$y'>$y</option>";
                    }
                }
                ?>
            </select>
            <div style="font-size: large;font-weight: bold;color: red;margin-left: 20px"><?php echo $get_year . " 全年统计图"; ?></div>
        </div>
    </div>

    <table width="90%" border="0" cellpadding="5" cellspacing="1" class='table table-striped table-bordered'>
        <tr>
            <td style="background:#fff">
                <div id="itlu_main_show" style="width:100%;height:300px"></div>
            </td>
        </tr>
    </table>
    <table width="90%" border="0" cellpadding="5" cellspacing="1" class='table table-striped table-bordered'>
        <tr>
            <td style="background:#fff">
                <div id="itlu_type_pay" style="width:100%;height:300px"></div>
            </td>
        </tr>
    </table>
    <table width="90%" border="0" cellpadding="5" cellspacing="1" class='table table-striped table-bordered'>
        <tr>
            <td style="background:#fff">
                <div id="itlu_type_income" style="width:100%;height:300px"></div>
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        $("#year").change(function () {
            var select_year = $(this).val();
            document.cookie = "selectYear=" + select_year;
            location.reload();
        });

        $(document).ready(function () {
            showMeth();
            payMeth();
            incomeMeth();
        });

        function showMeth() {
            var incomeObj =<?php
                $result = $conn->getYearShow($get_year);
                echo $result;
                ?>;
            var incomeLegend = {data: [], right: 10};
            var incomeSeries = [];
            for (var key in incomeObj) {
                var keyTemp = "支出";
                if (key === '1')
                    keyTemp = "收入";
                var temp = {name: keyTemp, type: 'line', data: incomeObj[key]};
                incomeSeries.push(temp);
                incomeLegend.data.push(keyTemp);
            }
            var myChart = echarts.init(document.getElementById('itlu_main_show'));
            var option = {
                title: {text: '收支统计'},
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis:
                    [{
                        type: 'category',
                        data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
                    }],
                yAxis: [{type: 'value'}],
                legend: incomeLegend,
                series: incomeSeries
            };
            myChart.setOption(option);
        };

        function payMeth() {
            var incomeObj =<?php
                $result = $conn->getYearAccount($get_year, 2);
                echo $result;
                ?>;
            var incomeLegend = {data: [], right: 10};
            var incomeSeries = [];
            for (var key in incomeObj) {
                var temp = {name: key, type: 'line', data: incomeObj[key]};
                incomeSeries.push(temp);
                incomeLegend.data.push(key);
            }
            var myChart = echarts.init(document.getElementById('itlu_type_pay'));
            var option = {
                title: {text: '支出统计'},
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis:
                    [{
                        type: 'category',
                        data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
                    }],
                yAxis: [{type: 'value'}],
                legend: incomeLegend,
                series: incomeSeries
            };
            myChart.setOption(option);
        };

        function incomeMeth() {
            var incomeObj =<?php
                $result = $conn->getYearAccount($get_year, 1);
                echo $result;
                ?>;
            var incomeLegend = {data: [], right: 10};
            var incomeSeries = [];
            for (var key in incomeObj) {
                var temp = {name: key, type: 'line', data: incomeObj[key]};
                incomeSeries.push(temp);
                incomeLegend.data.push(key);
            }
            var myChart = echarts.init(document.getElementById('itlu_type_income'));
            var option = {
                title: {text: '收入统计'},
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis:
                    [{
                        type: 'category',
                        data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
                    }],
                yAxis: [{type: 'value'}],
                legend: incomeLegend,
                series: incomeSeries
                //series: [{name: '默认分类', type: 'bar', data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]}]
            };
            myChart.setOption(option);
        };
    </script>
<?php include("footer.php"); ?>