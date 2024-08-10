<?php
include_once __DIR__ . '/header.php';
?>
<div class="table stat">
    <div class="itlu-title"><span class="pull-right"><button type="button" class="btn btn-primary btn-xs" id="btn_add">添加账户</button></span>
    </div>
</div>

<?php
show_tab(4);
$bank = new Bank();
$bank_list = $bank->get_bank($userid);//账户列表
foreach ($bank_list as $row) {
    echo "<ul class=\"table-row\">";
    echo "<li>" . $row["bank_name"] . "</li>";
    echo "<li>" . $row["account"] . "</li>";
    echo "<li>" . $row["balance_money"] . "</li>";
    $data = json_encode($row);
    echo "<li><a class='btn btn-primary btn-xs' href='javascript:' onclick='edit(this)' data-info='$data'>修改</a> <a class='btn btn-danger btn-xs' href='javascript:' onclick='deleteRecord(" . $row["bank_id"] . ")'>删除</a></li>";
    echo "</ul>";
}
show_tab(3);
?>
<?php include_once __DIR__ . '/footer.php'; ?>
<!--// 添加编辑分类-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="addform" name="addform" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加账户</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bank_name">账户名称</label>
                        <input name="bank_id" id="bank_id" type="hidden"/>
                        <input type="text" name="bank_name" class="form-control" id="bank_name" placeholder="账户名称"
                               required="请输入账户名称">
                    </div>
                    <div class="form-group">
                        <label for="account">卡号/帐号</label>
                        <input type="text" name="account" class="form-control" id="account" placeholder="卡号/帐号"
                               required="请输入卡号/帐号">
                    </div>
                    <div class="form-group">
                        <label for="balance_money">账户余额</label>
                        <input type="number" step="0.01" name="balance_money" class="form-control" id="balance_money"
                               placeholder="账户余额" required="请输入账户余额"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="error_show" class="footer-tips"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" id="btn_submit" date-info="add" class="btn btn-primary">保存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    chushihua_bank();
    $("#btn_add").click(function () {
        chushihua_bank();
        $('#myModal').modal({backdrop: 'static', keyboard: false});
    });
    $("#btn_submit").click(function () {
        // $(this).addClass("disabled");
        var action = $(this).attr("date-info");

        let uid =<?php echo $userid;?>;
        let params = FormatForm(uid, "#addform");
        Request_Data(GetUrl('Bank', action), params, ResponseSuccess, ResponseFail);
    });

    // 编辑分类
    function edit(t) {
        //初始化
        chushihua_bank();
        $("#myModal").modal({backdrop: 'static', keyboard: true});
        $("#myModalLabel").text("编辑账户");
        $('#btn_submit').attr('date-info', 'update');
        let json_data = $(t).data('info');
        $.each(json_data, function (key, value) {
            let ele_name = "#" + key;
            $(ele_name).val(value);
        });
    }

    function deleteRecord(bank_id) {
        let uid =<?php echo $userid;?>;
        let params = FormatDeleteData(uid, 'bank_id', bank_id);
        Request_Data(GetUrl('Bank', 'delete'), params, ResponseSuccess, ResponseFail);
    }
</script>