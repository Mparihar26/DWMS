<?php if (!defined('THINK_PATH')) exit();?><div class="panel panel-primary">
            <div class="panel panel-heading" style="margin-bottom:1px">
                <h3 class="panel-title">路线管理</h3>
            </div>
            <div class="panel panel-body" style="margin-bottom:1px">

   	<div style="width:auto" id="container"></div>  

   
<div style="position:absolute;left:0px;top:0px;width:100%">
      <div class="panel panel-info" style="float:right;margin-top:50px;margin-right:30px"><div class="panel-heading">路线选择</div> <div class="panel-body">
	<div id="noSelectionAlert"></div>	
	<div class="btn-group">
		<button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown">
			<span class="glyphicon glyphicon-road"></span> 选择 GPS 设备
		
		</button>
		<ul class="dropdown-menu" role="menu" id="dropdown-button">
		    <!--<li><a href="#">Action</a></li>-->
		 </ul>
	</div>
<select id="mySelect" style="visibility: hidden">
	正在获取 GPS 设备信息... 请稍候...
</select>

<p>
	<input class="btn btn-default navbar-btn" id="startBtn" type="button" onclick="startTool();" value="开始取点" />
	<input class="btn btn-default navbar-btn" type="button" onclick="map.clearOverlays();document.getElementById('info').innerHTML = '';points=[];mypoints=[];" value="清除" />
	<input class="btn btn-default navbar-btn" type="button" onclick="submit();" value="提交" />
</p>
<div id="info"></div></div></div>
    </div>
</div>
</div>

<script type="text/javascript">

 var windowHeight = $(window).height();
 $("#container").css("height", "" + windowHeight - 200 + "px");
map = new BMap.Map("container");                        // 创建Map实例
map.centerAndZoom("安庆市", 13);     // 初始化地图,设置中心点坐标和地图级别

var key = 1;    //开关
var newpoint;   //一个经纬度
var points = [];    //数组，放经纬度信息
var mypoints = [];
var polyline = new BMap.Polyline(); //折线覆盖物

function startTool(){   //开关函数
if(key==1){
        //document.getElementById("startBtn").style.background = "green";
        //document.getElementById("startBtn").style.color = "white";
        document.getElementById("startBtn").value = "正在取点";
        key=0;
    }
    else{
        //document.getElementById("startBtn").style.background = "red";
        document.getElementById("startBtn").value = "开始取点";
        key=1;
    }
}
map.addEventListener("click",function(e){   //单击地图，形成折线覆盖物
    newpoint = new BMap.Point(e.point.lng,e.point.lat);
	mynewpoint = {"latitude": e.point.lat, "longitude": e.point.lng};
    if(key==0){
    //    if(points[points.length].lng==points[points.length-1].lng){alert(111);}
        points.push(newpoint);  //将新增的点放到数组中
		mypoints.push(mynewpoint);
        polyline.setPath(points);   //设置折线的点数组
        map.addOverlay(polyline);   //将折线添加到地图上
        //document.getElementById("info").innerHTML += "new BMap.Point(" + e.point.lng + "," + e.point.lat + "),</br>";    //输出数组里的经纬度
    }
});
map.addEventListener("dblclick",function(e){   //双击地图，形成多边形覆盖物
if(key==0){
        map.disableDoubleClickZoom();   //关闭双击放大
var polygon = new BMap.Polygon(points);
        map.addOverlay(polygon);   //将折线添加到地图上
    }
});

function myAlert(msg)
{
	$("#noSelectionAlert").html('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>错误: </strong> ' + msg + ' </div>');
}

function myAlertSucc(msg)
{
	$("#noSelectionAlert").html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>错误: </strong> ' + msg + ' </div>');
}


function submit()
{
	if(window.mySelection == "") {
		myAlert('请先选择 GPS 设备。');
		return;
	}
	$.ajax({
		type: "POST",
		url: "city?id=GIS_map_receiver",
		timeout: 2000,
		data: { "gps_id":$("#mySelect").val(), "route_detail": JSON.stringify(mypoints)},
		success: function(data) {
			if(data=="succ") {
				myAlertSucc("提交成功");
			} else {
				myAlert("服务器返回失败");
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			myAlert("提交失败");
		}
	});
}

window.mySelection = "";

function setData(data)
{
	$("#mySelect").html("");
	$("#dropdown-button").html("");
	// myjson = JSON.parse(data);
	myarr = data; // myjson["GPS"];
	for(var i = 0; i < myarr.length; i++) {
		//$("#mySelect").append('<option value="' + myarr[i]["value"] + '">' + myarr[i]["name"] + "</option>");
		$("#dropdown-button").append('<li><a onclick="window.mySelection=\'' + myarr[i]["device_serial_num"] + '\';">' + myarr[i]["transport_unit_name"] + myarr[i]["vehicle_type"] + "</a></li>");
		
	}
}

$(document).ready(function() {
	/*$.ajax({
		type: "GET",
		url: "city?id=GIS_gps_getter",
		timeout: 2000,
		success: function(data) {
			setData(data);
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			myAlert("获取 GPS 失败");
		}
	});*/
	setData(page_json);
});

</script>