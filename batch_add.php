<?php include_once("header.php");
// 支付分类列表
$pay_type_list = $conn->show_type(2, $userid);
$pay_type_option = "";
foreach ($pay_type_list as $myrow) {
    $pay_type_option = $pay_type_option . "<option value='$myrow[classid]'>" . $myrow['classname'] . "</option>";
}
// 收入分类列表
$pay_type_list = $conn->show_type(1, $userid);
$income_type_option = "";
foreach ($pay_type_list as $myrow2) {
    $income_type_option = $income_type_option . "<option value='$myrow2[classid]'>" . $myrow2['classname'] . "</option>";
}
// 账户列表
$bank_list = $conn->db_list("bank", "where userid='$userid'", "order by bankid asc");
$bank_option = "";
foreach ($bank_list as $myrow) {
    $bank_option = $bank_option . "<option value='$myrow[bankid]'>" . $myrow['bankname'] . "</option>";
}

//检查是否记账并执行
if (isset($_POST['submit'])) {
    $path = $_POST['money'];
    $path1 = $_POST['classid'];
    $path2 = $_POST['time'];
    $path3 = $_POST['remark'];
    $path4 = $_POST['zhifu'];
    $path5 = $_POST['bankid'];
    $ok_count = 0;
    $no_ok_count = 0;
    $error_count = 0;

    foreach ($path as $key => $value) {
        $addtime = strtotime($path2[$key]);
        if ($value == "" || $path1 == "" || $value <= 0) {
            $no_ok_count++;
            continue;
        }
        $query = $conn->exec("insert into " . TABLE . "account (acmoney, acclassid, actime, acremark,zhifu,jiid,bankid) values('$value','" . $path1[$key] . "','$addtime','" . $path3[$key] . "','" . $path4[$key] . "','$userid','" . $path5[$key] . "')");
        if ($query) {
            $ok_count++;
        } else {
            $error_count++;
        }
    }
    echo "<script type='text/javascript'>alert('成功写入：" . $ok_count . "条，不符合条件：" . $no_ok_count . "条，失败：" . $error_count . "条');window.location='batch_add.php';</script>";
}
?>
    <table align="left" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3'
           class='table table-striped table-bordered'>
        <tr>
            <td bgcolor="#EBEBEB"><span class="red">支出</span> - <a href="#" class="AddBox" data-info="2">加一行</a></td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF">
                <form id="form_2" name="form_2" method="post">
                    <div id="itlu_wrap_2" class="itlu_wrap">
                        <?php for ($i = 0; $i < 1; $i++) { ?>
                            <div class="list">
                                <input name="zhifu[]" type="hidden" value="2"/>
                                <div class="list_1"><i>金额：</i><input type="number" step="0.01" name="money[]"/></div>
                                <div class="list_2"><i>分类：</i><select
                                            name="classid[]"><?php echo $pay_type_option; ?></select></div>
                                <div class="list_2"><i>账户：</i><select
                                            name="bankid[]"><?php echo $bank_option; ?></select></div>
                                <div class="list_3"><i>备注：</i><input name="remark[]" type="text"/></div>
                                <div class="list_4">时间：<input type="text" name="time[]"
                                                              value="<?php echo date("Y-m-d H:i"); ?>"
                                                              onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today; ?>'})"/>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="sub_btn"><input name="submit" type="submit" id="submit" value="支出记账"
                                                class="btn btn-danger"/></div>
                </form>
            </td>
        </tr>
        <tr>
            <td bgcolor="#EBEBEB"><span class="green">收入</span> - <a href="#" class="AddBox" data-info="1">加一行</a></td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF">
                <form id="form_1" name="form_1" method="post">
                    <div id="itlu_wrap_1" class="itlu_wrap">
                        <?php for ($i = 0; $i < 1; $i++) { ?>
                            <div class="list">
                                <input name="zhifu[]" type="hidden" value="1"/>
                                <div class="list_1"><i>金额：</i><input type="number" step="0.01" name="money[]"/></div>
                                <div class="list_2"><i>分类：</i><select
                                            name="classid[]"><?php echo $income_type_option; ?></select></div>
                                <div class="list_2"><i>账户：</i><select
                                            name="bankid[]"><?php echo $bank_option; ?></select></div>
                                <div class="list_3"><i>备注：</i><input name="remark[]" type="text"/></div>
                                <div class="list_4">时间：<input type="text" name="time[]"
                                                              value="<?php echo date("Y-m-d H:i"); ?>"
                                                              onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today; ?>'})"/>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="sub_btn"><input name="submit" type="submit" id="submit" value="收入记账"
                                                class="btn btn-success"/></div>
                </form>
            </td>
        </tr>
    </table>
    <script>
        $("#form_2").keypress(function (e) {
            if (e.which == 13) {
                return false;
            }
        });
        $("#form_1").keypress(function (e) {
            if (e.which == 13) {
                return false;
            }
        });
        $(document).ready(function () {
            var MaxInputs = 4;
            var x = 1;
            var FieldCount = 1;
            $(".AddBox").click(function (e) {
                //console.log(e.target.dataset.info);
                //var typeid = $(this).attr("date-info");
                var typeid = e.target.dataset.info;
                console.log(typeid);
                var html = "<div class=\"list\"><input name=\"zhifu[]\" type=\"hidden\" value=\"1\" /><div class=\"list_1\"><i>金额：</i><input type=\"text\" name=\"money[]\" /></div><div class=\"list_2\"><i>分类：</i><select name=\"classid[]\"><?php echo $pay_type_option;?></select></div><div class=\"list_2\"><i>账户：</i><select name=\"bankid[]\"><?php echo $bank_option;?></select></div><div class=\"list_3\"><i>备注：</i><input name=\"remark[]\" type=\"text\" /></div><div class=\"list_4\">时间：<input type=\"text\" name=\"time[]\" value=\"<?php echo date("Y-m-d H:i");?>\" onclick=\"WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today;?>'})\" /></div><div class=\"list_5\"><a class=\"removeclass\" href=\"#\">删除</a></div></div>";
                if (typeid == 2) {
                    html = "<div class=\"list\"><input name=\"zhifu[]\" type=\"hidden\" value=\"2\" /><div class=\"list_1\"><i>金额：</i><input type=\"text\" name=\"money[]\" /></div><div class=\"list_2\"><i>分类：</i><select name=\"classid[]\"><?php echo $pay_type_option;?></select></div><div class=\"list_2\"><i>账户：</i><select name=\"bankid[]\"><?php echo $bank_option;?></select></div><div class=\"list_3\"><i>备注：</i><input name=\"remark[]\" type=\"text\" /></div><div class=\"list_4\">时间：<input type=\"text\" name=\"time[]\" value=\"<?php echo date("Y-m-d H:i");?>\" onclick=\"WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today;?>'})\" /></div><div class=\"list_5\"><a class=\"removeclass\" href=\"#\">删除</a></div></div>";
                }
                var addboxid = $("#itlu_wrap_" + typeid);
                if (x <= MaxInputs) {
                    FieldCount++;
                    addboxid.append(html);
                    x++;
                }
                console.log(x);
            });
            $("body").on("click", ".removeclass", function (e) {
                if (x > 1) {
                    $(this).parents('div > .list').remove();
                    x--;
                }
            });

        });
    </script>
<?php include_once("footer.php"); ?>