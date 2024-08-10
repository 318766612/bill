<?php
include_once __DIR__ . '/header.php';
?>
<div class="table stat">
    <div class="itlu-title"><span class="pull-right"><button type="button" class="btn btn-primary btn-xs" id="btn_add">添加分类</button></span>
    </div>
</div>
<?php
show_tab(5);
$category = new Category();
for ($i = 2; $i >= 1; $i--) {
    if ($i == 2) {
        $fontcolor = "red";
        $word = "支出";
    } else {
        $fontcolor = "green";
        $word = "收入";
    }
    $pay_type_list = $category->type_category($userid, $i);//支出列表
    //$pay_type_list = $conn->show_type($i, $userid);
    foreach ($pay_type_list as $row) {
        echo "<ul class=\"table-row\">";
        echo "<li class='" . $fontcolor . "'>" . $row["category_name"] . "</li>";
        echo "<li class='" . $fontcolor . "'>" . $word . "</li>";
        $data = json_encode($row);
        echo "<li><a class='btn btn-primary btn-xs' href='javascript:' onclick='edit(this)' data-info='$data'>修改</a> <a class='btn btn-success btn-xs' href='javascript:' onclick='change(this)' data-info='$data'>转移</a> <a class='btn btn-danger btn-xs' href='javascript:' onclick='deleteRecord(" . $row["category_id"] . ")'>删除</a></li>";
        echo "</ul>";
    }
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
                    <h4 class="modal-title" id="myModalLabel">分类管理</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_name">分类名称</label>
                        <input type="text" name="category_name" class="form-control" id="category_name"
                               placeholder="分类名称"
                               required="请输入分类名称">
                        <input name="category_id" id="category_id" type="hidden" value=""/>
                        <div id="error_show" style="color:#f00"></div>
                    </div>
                    <div class="form-group" id="classtype_div">
                        <label for="classtype">所属类型</label>
                        <select name="type" id="type" class="form-control">
                            <option value="2">支出</option>
                            <option value="1">收入</option>
                        </select>
                    </div>
                    <div class="form-group" id="newclassname_div" style="display:none;">
                        <label for="new_category_id">目标分类</label>
                        <select name="new_category_id" id="new_category_id" class="form-control">
                            <option value='0'>请选择目标分类</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" id="btn_submit" date-info="add" class="btn btn-primary">保存</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    chushihua();
    $("#btn_add").click(function () {
        chushihua();
        $("#myModalLabel").text("添加分类");
        $('#myModal').modal({backdrop: 'static', keyboard: false});
    });
    $("#btn_submit").click(function () {
        let action = $(this).attr("date-info");
        let uid =<?php echo $userid;?>;
        let params = FormatForm(uid, "#addform");
        Request_Data(GetUrl('Category', action), params, ResponseSuccess, ResponseFail);
    });

    function deleteRecord(category_id) {
        let uid =<?php echo $userid;?>;
        let params = FormatDeleteData(uid, 'category_id', category_id);
        Request_Data(GetUrl('Category', 'delete'), params, ResponseSuccess, ResponseFail);
    }

    // 编辑分类
    function edit(t) {
        chushihua();
        $("#myModal").modal({backdrop: 'static', keyboard: true});
        $("#myModalLabel").text("编辑分类");
        $('#btn_submit').attr('date-info', 'update');
        let json_data = $(t).data('info');
        $.each(json_data, function (key, value) {
            let ele_name = "#" + key;
            $(ele_name).val(value);
        });
    }

    // 转移分类
    function change(t) {
        //初始化
        chushihua();
        $("#newclassid").find("option").not(":first").remove();//清除所有选项
        //$("#newclassid").find("option").remove();//清除所有选项
        var info = $(t).data('info');
        var classname = info.classname;
        var classid = info.classid;
        var classtype = info.classtype;
        $.ajax({
            type: "get",
            url: "date.php?action=getclassify&classtype=" + classtype + "&classid=" + classid + "", //需要获取的页面内容
            async: true,
            success: function (data) {
                console.log(data)
                $("#newclassid").append(data);
            }
        });
        $("#myModalLabel").text("转移分类");
        $("#myModal").modal({backdrop: 'static', keyboard: true});
        $("#classname").val(classname);
        $("#classname").attr('readonly', 'true');//屏蔽编辑
        $("#classid").val(classid);
        $("#classtype_div").hide();
        $("#newclassname_div").show();
        $('#btn_submit').attr('date-info', 'change');
    }
</script>