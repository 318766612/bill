<?php
include_once __DIR__ . '/header.php';
//============搜索参数处理================
$s_category_id = get('category_id', 'all');
$s_starttime = get('starttime', $this_month_firstday);
$s_endtime = get('endtime', $today);//默认今天
$s_startmoney = get('startmoney');
$s_endmoney = get('endmoney');
$s_remark = get('remark');
$s_bank_id = get('bank_id');
$s_page = get('page', '1');
$page_num = 20;
$pageurl = "show.php?1=1";
if ($s_category_id != "")
    $pageurl = $pageurl . "&category_id=" . $s_category_id;

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

if ($s_bank_id != "")
    $pageurl = $pageurl . "&bank_id=" . $s_bank_id;

//$banklist = $conn->db_list("bank", "where userid='$userid'", "order by bankid asc");
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
        <td bgcolor="#EBEBEB">查询修改</td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF">
            <div class="search_box">
                <form id="s_form" name="s_form" method="get">
                    <p><label for="category_id">分类：<select class="w180" name="category_id" id="category_id">
                                <option value="all" <?php if ($s_category_id == "all") {
                                    echo "selected";
                                } ?>>全部分类
                                </option>
                                <option value="pay" <?php if ($s_category_id == "pay") {
                                    echo "selected";
                                } ?>>====支出====
                                </option>
                                <?php
                                foreach ($pay_type_list as $myrow) {
                                    if ($myrow['category_id'] == $s_category_id) {
                                        echo "<option value='$myrow[category_id]' selected>支出>>" . $myrow['category_name'] . "</option>";
                                    } else {
                                        echo "<option value='$myrow[category_id]'>支出>>" . $myrow['category_name'] . "</option>";
                                    }
                                }
                                ?>
                                <option value="income" <?php if ($s_category_id == "income") {
                                    echo "selected";
                                } ?>>====收入====
                                </option>
                                <?php
                                foreach ($income_type_list as $myrow) {
                                    if ($myrow['category_id'] == $s_category_id) {
                                        echo "<option value='$myrow[category_id]' selected>收入 -- " . $myrow['category_name'] . "</option>";
                                    } else {
                                        echo "<option value='$myrow[category_id]'>收入 -- " . $myrow['category_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select></label></p>
                    <p><label>时间：<input class="w100" value="<?php echo $s_starttime; ?>" type="text"
                                        name="starttime" id="starttime"
                                        onClick="WdatePicker({maxDate:'#F{$dp.$D(\'endtime\')||\'<?php echo $today; ?>\'}'})"/>-<input
                                    class="w100" type="text" name="endtime" value="<?php if ($s_endtime == "") {
                                echo $today;
                            } else {
                                echo $s_endtime;
                            } ?>" id="endtime"
                                    onClick="WdatePicker({minDate:'#F{$dp.$D(\'starttime\')}',maxDate:'%y-%M-%d'})"/></label>
                    </p>

                    <p><label for="remark">备注：<input class="w180" type="text" name="remark" id="remark" size="30"
                                                     value="<?php echo $s_remark; ?>"></label></p>
                    <p><label for="bank_id">账户：<select class="w180" name="bank_id" id="bank_id">
                                <option value="" <?php if ($s_bank_id == "") {
                                    echo "selected";
                                } ?>>全部账户
                                </option>
                                <?php
                                foreach ($bank_list as $myrow) {
                                    if ($myrow['bank_id'] == $s_bank_id) {
                                        echo "<option value='$myrow[bank_id]' selected>" . $myrow['bank_name'] . "</option>";
                                    } else {
                                        echo "<option value='$myrow[bank_id]'>" . $myrow['bank_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select></label></p>
                    <p class="btn_div"><input type="submit" name="submit" value="查询" class="btn btn-primary"/></p>
                </form>
            </div>
        </td>
    </tr>
</table>

<div class="table stat">
    <div id="stat"></div>
</div>

<?php
//show_tab(1);
echo "<form name='del_all' id='del_all' method='post' onsubmit='return deleterecordAll(this);'>";
show_tab(1);
$account = new Account();
$Prolist = $account->get_account($userid, $s_category_id, $s_bank_id, $s_starttime, $s_endtime, $s_remark, $s_page, $page_num);
$data = $Prolist["data"];
//$thiscount = 0;
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
//    }
    echo "<li>" . $row['remark'] . "</li>";
    //操作列
    $data_json = json_encode($row);
    echo "<li><a href='javascript:' onclick='editorRecord(this,\"myModal\")' data-info=" . $data_json . "><img src='img/edit.png' /></a><a class='ml8' href='javascript:' onclick='deleteRecord(" . $row['acid'] . ");'><img src='img/del.png' /></a></li>";
    echo "</ul>";
    //$thiscount++;
}
echo "</form>";
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
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
                            <?php echo $pay_option; ?>
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" id="submit_edit" name="submit_edit" class="btn btn-primary">保存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script language="javascript">
    //统计
    $("#stat").html("<?php echo $s_starttime;?> 至 <?php echo $s_endtime;?>共收入<strong class='green'><?php echo $account->statistics($s_starttime, $s_endtime, $userid, 1);?></strong>，共支出<strong class='red'><?php echo $account->statistics($s_starttime, $s_endtime, $userid, 2);?></strong>");

    function checkeditpost(form, type) {
        $("#submit_" + type).addClass("disabled");
        let uid =<?php echo $userid;?>;
        let params = FormatForm(uid, "#" + type + "-form");
        console.log(params);
        Request_Data(GetUrl('Account', 'update'), params, ResponseSuccess, ResponseFail);
    }

    function editorRecord(data_form, editor_form_id) {
        let info = $(data_form).data('info');
        $("#" + editor_form_id).modal({backdrop: 'static', keyboard: true});
        let editorEle = document.getElementById(editor_form_id);
        DeserializationFrom(editorEle, info);
        let zhifu_category = info['zhifu'];
        $("#edit-classid").empty();
        if (zhifu_category == 1) {
            $("#edit-classid").append("<?php echo $income_option;?>");
        } else {
            $("#edit-classid").append(" <?php echo $pay_option; ?>");
        }
    }

    function deleteRecord(acid) {
        let uid =<?php echo $userid;?>;
        let params = FormatDeleteData(uid, 'acid', acid);
        Request_Data(GetUrl('Account', 'delete'), params, ResponseSuccess, ResponseFail);
    }

</script>