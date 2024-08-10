// 提交数据./inc/Api.php?act=操作类&mod=方法
function Request_Data(operate, data, successCallback, errorCallback) {
    let url = "./inc/Api.php?" + operate;
    console.log(url);
    $.ajax({
        type: "POST",
        dataType: "text",
        url: url,
        data: data,
        success: function (res) {
            console.log(res);
            let data = '';
            if (res != '') {
                data = eval("(" + res + ")");    //将返回的json数据进行解析，并赋给data
            }
            if (data.code == 1) {
                successCallback(data.data);
            } else {
                $("#error_show").show();
                $('#error_show').html(data.msg);
                errorCallback(data.msg);
            }
        },
        error: function (xhr, status, error) {
            console.log(error);
            if (errorCallback != null)
                errorCallback(error);
        }
    });
}

function GetUrl(act, mod) {
    return 'act=' + act + "&mod=" + mod;
}

function ResponseSuccess(data) {
    console.log("操作成功");
    if (isEmptyObject(data)) {
        window.location.reload();
    } else {

    }
}

function ResponseFail(data) {
    console.log('操作失败:' + data);
}

function isEmptyObject(obj) {
    return $.isEmptyObject(obj);
}

function FormatForm(uid, form) {
    let dataObj = {};
    dataObj['uid'] = uid;
    let data_array = $(form).serializeArray();
    $.each(data_array, function () {
        if (!isEmptyObject(this.value)) {
            let key = (this.name).replace("edit-", "")
            dataObj[key] = this.value;
        }
    });
    let data_json = JSON.stringify(dataObj);
    //console.log('请求参数:' + data_json);
    let params = {data: data_json};
    return params;
}

function DeserializationFrom(form, json_data) {
    $.each(json_data, function (key, value) {
        let ele_name = "#edit-" + key;
        $(ele_name).val(value);
    });
}

function DeserializationBank(json_data) {
    $.each(json_data, function (key, value) {
        let ele_name = "#" + key;
        $(ele_name).val(value);
    });
}


function FormatDeleteData(uid, id_name, data) {
    let dataObj = {};
    dataObj['uid'] = uid;
    dataObj[id_name] = data;
    let data_json = JSON.stringify(dataObj);
    //console.log('删除请求参数：' + data_json);
    let params = {data: data_json};
    return params;
}


function chushihua() {
    // 初始化
    $("#classname").val("");
    $("#classname").removeAttr('readonly');
    $("#classtype_div").show();
    $("#newclassname_div").hide();
    $("#classid").val("");
    $('#btn_submit').attr('date-info', 'add');
    $("#classtype").find("option").attr("selected", false);
    $("#error_show").html("");
}

function chushihua_bank() {
    // 初始化银行卡
    $("#bankid").val("");
    $("#bankname").val("");
    $("#bankaccount").val("");
    $("#balancemoney").val("");
    $('#btn_submit').attr('date-info', 'add');
    $("#error_show").html("");
}