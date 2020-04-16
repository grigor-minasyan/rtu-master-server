// var cur_temp = 15;//{{ cur_temp }};
let update_delay = 1000;
let RTU_id = 2;
let RTU2_id = 3;

function to_f(c) {
  return (1.8*c+32);
}

var RTU_id_list = [];
var is_celcius = 0;
var max_hist = 200;


function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

function draw_threshold(id, t1, t2, t3, t4, cur, unit) {
  t1 = parseInt(t1);
  t2 = parseInt(t2);
  t3 = parseInt(t3);
  t4 = parseInt(t4);
  cur = parseInt(cur);
  if (!is_celcius && unit == "c") {
    t1 = to_f(t1);
    t2 = to_f(t2);
    t3 = to_f(t3);
    t4 = to_f(t4);
    cur = to_f(cur);
  }
  var canvas = document.getElementById(id);
	var width = canvas.offsetWidth;
	var height = canvas.offsetHeight;
  var ctx = canvas.getContext("2d");
  ctx.clearRect(0, 0, width, height);
  var range = t4-t1;
  var start = t1 - 0.2*range;
  var end = t4 + 0.2*range;
  ctx.font = "18px Arial";
  var start_y = 27, text_offset = 18;

  ctx.fillStyle = "#6a0dad";
  ctx.fillRect(0,start_y,width*(t1-start)/(end-start),height/2);

  ctx.fillStyle = "#2731e6";
  ctx.fillRect(width*(t1-start)/(end-start),start_y,width*(t2-t1)/(end-start),height/2);
  ctx.fillText(t1.toFixed(1).toString(), width*(t1-start)/(end-start)-text_offset, start_y-10);

  ctx.fillStyle = "#2cbf40";
  ctx.fillRect(width*(t2-start)/(end-start),start_y,width*(t3-t2)/(end-start),height/2);
  ctx.fillText(t2.toFixed(1).toString(), width*(t2-start)/(end-start)-text_offset, start_y-10);

  ctx.fillStyle = "#a19600";
  ctx.fillRect(width*(t3-start)/(end-start),start_y,width*(t4-t3)/(end-start),height/2);
  ctx.fillText(t3.toFixed(1).toString(), width*(t3-start)/(end-start)-text_offset, start_y-10);

  ctx.fillStyle = "#cf2115";
  ctx.fillRect(width*(t4-start)/(end-start),start_y,width*(end-t4)/(end-start),height/2);
  ctx.fillText(t4.toFixed(1).toString(), width*(t4-start)/(end-start)-text_offset, start_y-10);

  var cur_pos = cur;
  if (cur_pos < start+6*range/100) {
    cur_pos = start+6*range/100;
  } else if (cur_pos > end-6*range/100) {
    cur_pos = end-6*range/100;
  }
  ctx.fillStyle = "#222222";
  ctx.fillRect(width*(cur_pos-start)/(end-start),start_y,2,height/2+10);
  ctx.fillText(cur.toFixed(1).toString() + (unit == "c" ? (is_celcius ? "C" : "F") : unit), width*(cur_pos-start)/(end-start)-text_offset, height/2+start_y+30);
}

function validate_form(ipaddress, port, id){
  if (!(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipaddress))){
    alert("You have entered an invalid IP address!");
    return (false);
  }
  if (isNaN(port) || port < 1 || port % 1 != 0) {
    alert("Port number is invalid");
    return false;
  }
  if (isNaN(id) || id < 1 || id % 1 != 0) {
    alert("ID number is invalid");
    return false;
  }
  return true;
}

function temp_toggle() {
  if (is_celcius == 0) {
    is_celcius = 1;
    $("#temp_toggle").html("C");
  } else {
    is_celcius = 0;
    $("#temp_toggle").html("F");
  }
}
/*
function submit_rtu_data() {
  let ip_address = document.getElementById("ip_address").value;
  var port = document.getElementById("port").value;
  var device_id = document.getElementById("device_id").value;
  if (validate_form(ip_address, port, device_id)) {
    $('input[type="text"], textarea').val('');
    // alert("\nIP: " + ip_address + "\nPort: " + port + "\nDevice ID: " + device_id + "\nForm Submitted Successfully......");
    $.getJSON($SCRIPT_ROOT + '/_monitor_new_rtu/' + ip_address.toString() + '/' + port.toString() + '/' + device_id.toString(), function(data) {
      if(data.success) {
        alert(data.message);
        RTU_id_list.push(Number(device_id));
        location.reload();
        return true;
      } else {
        alert(data.message);
        return false;
      }
    });
  }
}

function get_id_list() {
  $.getJSON($SCRIPT_ROOT + '/_get_id_list/', function(data) {
    RTU_id_list = data;
    str = "";
    str2 = "";
    for (var i = 0; i < RTU_id_list.length; i++) {
      str += "<a class=\"nav-link" + (i ? "" : " active") + "\" id=\"devices_tab_";
      str += RTU_id_list[i].toString();
      str += "\" data-toggle=\"pill\" href=\"#devices_tab_inside_";
      str += RTU_id_list[i].toString();
      str += "\" role=\"tab\" aria-controls=\"devices_tab_inside_";
      str += RTU_id_list[i].toString();
      str += "\" aria-selected=\"" + (i ? "false" : "true") + "\">Device #";
      str += RTU_id_list[i].toString();
      str += "</a>";

      str2 += "<div class=\"tab-pane fade" + (i ? "" : " show active") + "\" id=\"devices_tab_inside_";
      str2 += RTU_id_list[i].toString();
      str2 += "\" role=\"tabpanel\" aria-labelledby=\"devices_tab_";
      str2 += RTU_id_list[i].toString();
      str2 += "\"><div class=\"col\"><h4>Current temperature is <span class = \"temp_to_update\" id=\"temp";
      str2 += RTU_id_list[i].toString();
      str2 += "\"></span>" + "</h4><h4>Current humidity is <span id=\"hum";
      str2 += RTU_id_list[i].toString();
      str2 += "\"></span>%</h4>";
      str2 += "<canvas id=\"threshold_canvas_";
      str2 += RTU_id_list[i].toString();
      str2 += "\" width=\"500\" height=\"100\">Your browser does not support the canvas element.</canvas>";
      str2 += "<h4><span id=\"alarm";
      str2 += RTU_id_list[i].toString();
      str2 += "\"></span></h4><p>History</p><table class=\"table table-striped\">";
      str2 += "<thead><tr><th scope=\"col\">#</th><th scope=\"col\">Date</th><th scope=\"col\">Time</th><th scope=\"col\">Temperature</th><th scope=\"col\">Humidity</th></tr></thead>";
      str2 += "<tbody id = \"history";
      str2 += RTU_id_list[i].toString();
      str2 += "\"></tbody></table></div></div>";


    }
    $("#v-pills-tab").html(str);
    $("#v-pills-tabContent").html(str2);
  });
}

function id_to_remove() {
  var device_id = document.getElementById("device_id_to_remove").value;
  if (isNaN(device_id) || device_id < 1 || device_id % 1 != 0) {
    alert("ID number is invalid");
    return false;
  } else {
    $('input[type="text"], textarea').val('');
    $.getJSON($SCRIPT_ROOT + '/_remove_device/' + device_id.toString(), function(data) {
      if(data.success) {
        alert(data.message);
        for(var i = 0; i < RTU_id_list.length; i++){
          if (RTU_id_list[i] == Number(device_id)) {
            RTU_id_list.splice(i, 1);
          }
        }
        location.reload();
        return true;
      } else {
        alert(data.message);
        return false;
      }
    });
  }
}

function change_hisory_count() {
  var new_h = document.getElementById("new_history_count").value;
  if (isNaN(new_h) || new_h < 1 || new_h % 1 != 0) {
    alert("Number is invalid");
    return false;
  } else {
    max_hist = new_h;
    return false;
  }
}

var RTU_obj;
function update_temp(id) {
  $.getJSON($SCRIPT_ROOT + '/_update_cur_temp/' + id.toString(), function(data) {
    RTU_obj = data;
    RTU_obj[4].sort(function(item2, item1){
      if (item1[0] != item2[0]) return item1[0] - item2[0]
      else if (item1[1] != item2[1]) return item1[1] - item2[1];
      else if (item1[2] != item2[2]) return item1[2] - item2[2];
      else if (item1[3] != item2[3]) return item1[3] - item2[3];
      else if (item1[4] != item2[4]) return item1[4] - item2[4];
      else if (item1[5] != item2[5]) return item1[5] - item2[5];
      else return 0;
    });
    str = "";
    for (let i = 0; i < Object.keys(RTU_obj[4]).length && i < max_hist; i++) {
      str = str + "<tr><td>" + (i+1).toString() + "</td><td>" +
      RTU_obj[4][i][1].toString() + "/" +
      RTU_obj[4][i][2].toString() + "/" +
      RTU_obj[4][i][0].toString() + "</td><td>" +
      RTU_obj[4][i][3].toString() + ":" +
      RTU_obj[4][i][4].toString() + ":" +
      RTU_obj[4][i][5].toString() + "</td><td class = \"temp_to_update\">" +
      (is_celcius ? RTU_obj[4][i][6] : to_f(RTU_obj[4][i][6])).toFixed(1).toString() + (is_celcius ? "C" : "F") + " </td><td>" +
      RTU_obj[4][i][7].toString() + "%" +"</td></tr>";
    }
    $("#history"+id.toString()).html(str);
    $("#temp"+id.toString()).html((is_celcius ? RTU_obj[3][6] : to_f(RTU_obj[3][6]).toFixed(1)).toString() + (is_celcius ? "C" : "F"));
    $("#hum"+id.toString()).html(RTU_obj[3][7]);


    draw_threshold(id, RTU_obj[1][0], RTU_obj[1][1], RTU_obj[1][2], RTU_obj[1][3], RTU_obj[3][6]);
    if(RTU_obj[2] == 3) {
      //major over
      $("#alarm"+id.toString()).html("Alarm MJ OVER!");
    } else if (RTU_obj[2] == 2) {
      //minor over
      $("#alarm"+id.toString()).html("Alarm MN OVER!");
    } else if (RTU_obj[2] == 4) {
      // minor under
      $("#alarm"+id.toString()).html("Alarm MN UNDER!");
    } else if (RTU_obj[2] == 12) {
      //major under
      $("#alarm"+id.toString()).html("Alarm MJ UNDER!");
    } else {
      $("#alarm"+id.toString()).html("No alarm, everything clear");
    }
  });
  return false;
}
*/
function update_temp_new() {
  $.ajax({
    url:"/post_responder.php",
    type:"POST",
    contentType:"application/json",
    success: function(data){
      //updating the device information device_info_table_
      data_obj = JSON.parse(data);
      for (let i = 0; i < data_obj.length; i++) {
        let table_info_str = "";
        table_info_str += "<td>" + data_obj[i].rtu_id + "</td>";
        table_info_str += "<td>" + data_obj[i].rtu_ip + "</td>";
        table_info_str += "<td>" + data_obj[i].rtu_port + "</td>";
        table_info_str += "<td>" + data_obj[i].type + "</td>";
        table_info_str += "<td>" + (data_obj[i].link ? "<span class = \"text-success\">Online</span>" : "<span class = \"text-danger\">Offline</span>") + "</td>";
        table_info_str += "<td>" + data_obj[i].display_count + "</td>";
        $("#device_info_table_"+data_obj[i].rtu_id).html(table_info_str);

        //updating the standing alarms table
        let standing_str = "";
        let alarm_count = 0;
        for (let j = 0; j < data_obj[i].standing.length; j++) {
          standing_str +="<tr>";

          standing_str += "<td>" + data_obj[i].standing[j].display + "</td>";
          standing_str += "<td>" + data_obj[i].standing[j].long_desc + "</td>";
          standing_str += "<td>" + data_obj[i].standing[j].point + "</td>";
          standing_str += "<td>" + data_obj[i].standing[j].description + "</td>";
          standing_str += "<td>" + (data_obj[i].standing[j].is_set == "1" ? ("<span class = \"text-danger\">Alarm</span>" + (alarm_count++ ? "" : "")) : "<span class = \"text-success\">Clear</span>") + "</td>";
          standing_str += "</tr>";

          //updating the canvases
          // $("#v-pills-display-"+data_obj[i].standing[j].rtu_id_display).html("<canvas id=\"threshold_canvas_" + data_obj[i].standing[j].rtu_id_display + "\" width=\"" + $("#v-pills-display-"+data_obj[i].standing[j].rtu_id_display).width() + "\" height=\"200\">Unsupported</canvas>");
          //
          // draw_threshold("threshold_canvas_" + data_obj[i].standing[j].rtu_id_display,
          //   data_obj[i].standing[j].mj_und_val, data_obj[i].standing[j].mn_und_val,
          //   data_obj[i].standing[j].mn_ovr_val, data_obj[i].standing[j].mj_ovr_val,
          //   data_obj[i].standing[j].value, data_obj[i].standing[j].unit);

        }
        $("#standing_table_"+data_obj[i].rtu_id).html(standing_str);
        //change the standing alarm text to red if there are any alarms
        if (alarm_count) $("#v-pills-standing-" + data_obj[i].rtu_id + "-tab").addClass("text-danger");
        else $("#v-pills-standing-" + data_obj[i].rtu_id + "-tab").removeClass("text-danger");


      }
    }
  })
}

window.setInterval(function(){
  update_temp_new();
}, update_delay);
