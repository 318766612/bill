<?php
include_once __DIR__ . '/header.php';
$bank = new Bank();
$bank_list = $bank->get_bank($userid);//账户列表
$bank_option = "";
foreach ($bank_list as $myrow) {
    $bank_option = $bank_option . "<option value='$myrow[bank_id]'>" . $myrow['bank_name'] . "</option>";
}

$category = new Category();
$pay_type_list = $category->type_category($userid, 2);//支出列表
$pay_option = "";
foreach ($pay_type_list as $myrow) {
    $pay_option = $pay_option . "<option value='$myrow[category_id]'>" . $myrow['category_name'] . "</option>";
}

$income_type_list = $category->type_category($userid, 1);//收入列表
$income_option = "";
foreach ($income_type_list as $myrow) {
    $income_option = $income_option . "<option value='$myrow[category_id]'>" . $myrow['category_name'] . "</option>";
}

?>
<table align="left" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3'
       class='table table-striped table-bordered'>
    <tr>
        <td bgcolor="#EBEBEB" class="add_th">
            <div class="tab-title"><span class="red on" data-id="pay">支出</span>
                <span class="green" data-id="income">收入</span>
            </div>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" id="contentbox">
            <div class="record_form" id="pay">
                <form id="pay_form" name="pay_form" method="post" onsubmit="return checkpost(this,'pay')">
                    <input name="type" type="hidden" id="type" value="2"/>
                    <div class="input-group">
                        <span class="input-group-label">金额</span>
                        <input class="form-field" type="number" step="0.01" name="money" id="money" size="20"
                               maxlength="8"/>
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">分类</span>
                        <select class="form-field" name="category_id" id="category_id">
                            <?php echo $pay_option; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">账户</span>
                        <select class="form-field" name="bank_id" id="bank_id">
                            <?php echo $bank_option; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">备注</span>
                        <input class="form-field" type="text" name="remark" id="remark" size="30" maxlength="20">
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">时间</span>
                        <input class="form-field" type="text" name="time" id="time" size="30"
                               value="<?php echo date("Y-m-d H:i"); ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today; ?>'})"/>
                    </div>
                    <div class="input-group">
                        <button name="submit" type="submit" id="submit_pay" class="btn btn-danger">支出记一笔</button>
                    </div>
                    <span id="pay_error" class="red" style="display:none;"></span>
                </form>
            </div>

            <div class="record_form" id="income" style="display:none;">
                <form id="income_form" name="income_form" method="post" onsubmit="return checkpost(this,'income');">
                    <input name="type" type="hidden" id="type" value="1"/>
                    <div class="input-group">
                        <span class="input-group-label">金额</span>
                        <input class="form-field" type="number" step="0.01" name="money" id="money" size="20"
                               maxlength="8"/>
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">分类</span>
                        <select class="form-field" name="category_id" id="category_id">
                            <?php echo $income_option; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">账户</span>
                        <select class="form-field" name="bank_id" id="bank_id">
                            <?php echo $bank_option; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">备注</span>
                        <input class="form-field" type="text" name="remark" id="remark" size="30" maxlength="20">
                    </div>
                    <div class="input-group">
                        <span class="input-group-label">时间</span>
                        <input class="form-field" type="text" name="time" id="time" size="30"
                               value="<?php echo date("Y-m-d H:i"); ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today; ?>'})"/>
                    </div>
                    <div class="input-group">
                        <button name="submit" type="submit" id="submit_income" class="btn btn-success">收入记一笔</button>
                    </div>
                    <span id="income_error" class="red" style="display:none;"></span>
                </form>
            </div>
        </td>
    </tr>
</table>

<div class="table stat">
    <div id="stat"></div>
</div>

<?php
$s_classid = 'all';
$s_starttime = $today;
$s_endtime = $today;
$s_startmoney = '';
$s_endmoney = '';
$s_remark = '';
$s_bankid = '';
//$s_page = '1';
$page_num = 20;
$s_page = get('page', '1');
$pageurl = "show.php?1=1";
if ($s_classid != "")
    $pageurl = $pageurl . "&classid=" . $s_classid;

if ($s_starttime != "")
    $pageurl = $pageurl . "&starttime=" . $s_starttime;

if ($s_endtime != "")
    $pageurl = $pageurl . "&endtime=" . $s_endtime;

if ($s_startmoney != "")
    $pageurl = $pageurl . "&startmoney=" . $s_startmoney;

if ($s_endmoney != "")
    $pageurl = $pageurl . "&endmoney=" . $s_endmoney;

if ($s_remark != "")
    $pageurl = $pageurl . "&remark=" . $s_remark;

if ($s_bankid != "")
    $pageurl = $pageurl . "&bankid=" . $s_bankid;


show_tab(1);
$account = new Account();
$Prolist = $account->get_account($userid, $s_classid, $s_bankid, $s_starttime, $s_endtime, $s_remark, $s_page, $page_num);
$thiscount = 0;
$data = $Prolist["data"];
foreach ($data as $row) {
    if ($row['type'] == 1) {
        $fontcolor = "green";
        $word = "收入";
    } else {
        $fontcolor = "red";
        $word = "支出";
    }
    echo "<ul class=\"table-row " . $fontcolor . "\">";
    echo "<li><i class='noshow'>" . $word . ">></i>" . $row['category_name'] . "</li>";
    echo "<li>" . $row['bank_name'] . "</li>";
    echo "<li class='t_a_r'>" . price_format($row['money']) . "</li>";
    echo "<li>" . $row['time'] . "</li>";
    echo "<li>" . $row['remark'] . "</li>";
    $data_json = json_encode($row);
    echo "<li><a href='javascript:' onclick='editorRecord(this,\"myModal\")' data-info=" . $data_json . "><img src='img/edit.png' /></a><a class='ml8' href='javascript:' onclick='deleteRecord(" . $row['acid'] . ");'><img src='img/del.png' /></a></li>";
    //echo "<li><a href='javascript:' onclick='editRecord(this,\"myModal\")' data-info='{\"id\":\"" . $row["acid"] . "\",\"money\":\"" . price_format($row["acmoney"]) . "\",\"zhifu\":\"" . $row["zhifu"] . "\",\"bankid\":\"" . $row["bankid"] . "\",\"addtime\":\"" . $row['actime'] . "\",\"remark\":" . json_encode($row["acremark"]) . ",\"classname\":" . json_encode($word . " -- " . $row["classname"]) . "}'><img src='img/edit.png' /></a><a class='ml8' href='javascript:' onclick='deleteRecord(\"record\"," . $row['acid'] . ");'><img src='img/del.png' /></a></li>";
    echo "</ul>";
    $thiscount++;
}
show_tab(3);
?>

<?php
//显示页码
//$allcount = record_num_query($userid, $s_classid, $s_starttime, $s_endtime, $s_startmoney, $s_endmoney, $s_remark, $s_bankid);
//$allcount = count($Prolist);
//$pages = ceil($allcount / 20);
$allcount = $Prolist["page"]["all"];
$page_len = $Prolist["page"]["page_len"];
$pages = $Prolist["page"]["pages"];

if ($pages > 1) {
    ?>
    <div class="page"><?php getPageHtml($s_page, $pages, $pageurl . "&", $allcount, $page_len); ?></div>
<?php } ?>

<?php include_once __DIR__ . '/footer.php'; ?>
<!--// 编辑-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="edit-form" name="edit-form" method="post" onsubmit="return checkeditpost(this,'edit');">
            <input name="edit-acid" type="hidden" id="edit-acid"/>
            <input name="edit-type" type="hidden" id="edit-type"/>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">数据修改</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-money">金额</label>
                        <input type="number" step="0.01" name="edit-money" class="form-control" id="edit-money"
                               placeholder="收支金额" required="请输入收支金额"/>
                    </div>
                    <div class="form-group">
                        <label for="edit-category_id">分类</label>
                        <select name="edit-category_id" id="edit-category_id" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-bank_id">账户</label>
                        <select name="edit-bank_id" id="edit-bank_id" class="form-control">
                            <?php echo $bank_option; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit-remark">备注</label>
                        <input type="text" name="edit-remark" class="form-control" id="edit-remark" maxlength="20"/>
                    </div>

                    <div class="form-group">
                        <label for="edit-time">时间</label>
                        <input type="text" name="edit-time" class="form-control" id="edit-time"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'<?php echo $today; ?>'})"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="error_show" class="footer-tips"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button id="submit_edit" name="submit_edit" type="submit" class="btn btn-primary">保存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    //统计今天的收支总数
    $("#stat").html("今天支出<strong class='red'><?php echo $account->statistics($today, $today, $userid, 2);?></strong>，收入<strong class='green'><?php echo $account->statistics($today, $today, $userid, 1);?></strong>");


    $(".tab-title span").off("click").on("click", function () {
        var index = $(this).index();
        $(this).addClass("on").siblings().removeClass("on");
        var tab = $(this).attr("data-id");
        $("#contentbox .record_form").eq(index).show().siblings().hide();
    });
    var UrlParam = getUrlParam('action');
    if (UrlParam == "income") {
        $("#income").show();
        $("#pay").hide();
        $(".tab-title span.green").addClass("on");
        $(".tab-title span.red").removeClass("on");
    }
    $("#btn_submit_save_edit").click(function () {
        $(this).addClass("disabled");
        //saveEditRecord();
    });

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null)
            return unescape(r[2]);
        return null; //返回参数值
    }

    function checkpost(form, type) {
        if ((form.money.value == "") || (form.money.value <= 0)) {
            alert("请输入金额且金额必须大于0");
            form.money.focus();
            return false;
        }
        $("#submit_" + type).addClass("disabled");
        let uid =<?php echo $userid;?>;
        let params = FormatForm(uid, "#" + type + "_form");
        Request_Data(GetUrl('Account', 'add'), params, ResponseSuccess, ResponseFail);
        //saverecord(type);
        //return true;
    }

    function checkeditpost(form, type) {
        if ((form.edit - money.value == "") || (form.edit - money.value <= 0)) {
            alert("请输入金额且金额必须大于0");
            form.edit - money.focus();
            return false;
        }
        $("#submit_" + type).addClass("disabled");
        let uid =<?php echo $userid;?>;
        let params = FormatForm(uid, "#" + type + "-form");
        Request_Data(GetUrl('Account', 'update'), params, ResponseSuccess, ResponseFail);
        //saverecord(type);
        //return true;
    }

    function editorRecord(data_form, editor_form_id) {
        let info = $(data_form).data('info');
        $("#" + editor_form_id).modal({backdrop: 'static', keyboard: true});
        let editorEle = document.getElementById(editor_form_id);
        DeserializationFrom(editorEle, info);
        let type = info['type'];
        //console.log(zhifu_category);
        $("#edit-category_id").empty();

        if (type == 1) {
            $("#edit-category_id").append("<?php echo $income_option;?>");
        } else {
            $("#edit-category_id").append(" <?php echo $pay_option; ?>");
        }
    }

    function deleteRecord(acid) {
        let uid =<?php echo $userid;?>;
        let params = FormatDeleteData(uid, 'acid', acid);
        Request_Data(GetUrl('Account', 'delete'), params, ResponseSuccess, ResponseFail);
    }

</script>