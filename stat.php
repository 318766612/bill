<?php
include_once __DIR__ . '/header.php';
$bank = new Bank();
$bank_list = $bank->get_bank($userid);//账户列表
$bank_option = "";
foreach ($bank_list as $myrow)
    $bank_option = $bank_option . "<option value='$myrow[bank_id]'>" . $myrow['bank_name'] . "</option>";
?>
    <div class="table stat">
        <div class="itlu-title">
            <select name="bank_id" id="bank_id" style="width: 88px">
                <?php echo $bank_option; ?>
            </select>
        </div>
    </div>
    <div id="body_bank">

    </div>

    <script language="javascript">
        $("#bank_id").change(function () {
            let bank_id = $(this).val();
            let uid =<?php echo $userid;?>;
            let params = FormatDeleteData(uid, 'bank_id', bank_id);
            Request_Data(GetUrl('Account', 'count_bank'), params, GetCount, GetError); // 调用加载数据的函数
        });

        function GetCount(data) {
            let $root = $('<div class="table"></div>');
            let $head = $('<div class="table-header-group"></div>');
            let $head_ul = $('<ul class="table-row"></ul>');
            $head_ul.append($('<li class="w15p"></li>').text('信息'));
            $head_ul.append($('<li class="w22p"></li>').text('收入'));
            $head_ul.append($('<li class="w22p"></li>').text('支出'));
            $head_ul.append($('<li class="w22p"></li>').text('剩余'));
            $head.append($head_ul);
            $root.append($head)
            let $content = $('<div class="table-row-group"></div>');
            $.each(data, function (index, item) {
                //console.log(item[0] + "   " + item[1] + "   " + item[2] + "   " + item[3])
                let $ul = $('<ul class="table-row"></ul>');
                $ul.append($('<li class="w15p"></li>').text(item[0]));
                $ul.append($('<li class="w22p"></li>').text(item[1]));
                $ul.append($('<li class="w22p"></li>').text(item[2]));
                $ul.append($('<li class="w22p"></li>').text(item[3]));
                $content.append($ul)
            });
            $root.append($content);
            $('#body_bank').html($root);
        }

        function GetError() {
            console.log('加载失败');
        }

    </script>
<?php include_once __DIR__ . '/footer.php'; ?>