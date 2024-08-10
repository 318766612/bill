<?php
include_once __DIR__ . '/header.php';
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
    <script type="text/javascript" defer>
        $("#year").change(function () {
            let select_year = $(this).val();
            document.cookie = "selectYear=" + select_year;
            location.reload();
        });

        $(document).ready(function () {
            // 当整个文档加载完成后执行这个函数
            let uid =<?php echo $userid;?>;
            let params = FormatDeleteData(uid, 'year', $("#year").val());
            Request_Data(GetUrl('Account','count_year'), params, GetCount, GetError); // 调用加载数据的函数
        });

        function GetCount(data) {
            let allChart = echarts.init(document.getElementById('itlu_main_show'));
            let payChart = echarts.init(document.getElementById('itlu_type_pay'));
            let incomeChart = echarts.init(document.getElementById('itlu_type_income'));
            SetChart(allChart, data.year, '收支统计');
            SetChart(payChart, data.pay, '支出统计');
            SetChart(incomeChart, data.income, '收入统计');
        }

        function GetError() {
            console.log('加载失败');
        }

        function SetChart(chart, data, title) {
            let incomeLegend = {data: [], right: 10};
            let incomeSeries = [];
            for (let key in data) {
                let temp = {name: key, type: 'line', data: data[key]};
                incomeSeries.push(temp);
                incomeLegend.data.push(key);
            }
            let option = {
                title: {text: title},
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
            chart.setOption(option);
        };


    </script>
<?php include_once __DIR__ . '/footer.php'; ?>