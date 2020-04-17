let update_delay = 1000;

function to_f(c) {
  return Number((1.8*c+32).toFixed(1));
}

var is_celcius = 0;
var max_hist = 200;

const DELETE_ICON = "<svg class=\"bi bi-trash-fill\" width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\"><path fill-rule=\"evenodd\" d=\"M2.5 1a1 1 0 00-1 1v1a1 1 0 001 1H3v9a2 2 0 002 2h6a2 2 0 002-2V4h.5a1 1 0 001-1V2a1 1 0 00-1-1H10a1 1 0 00-1-1H7a1 1 0 00-1 1H2.5zm3 4a.5.5 0 01.5.5v7a.5.5 0 01-1 0v-7a.5.5 0 01.5-.5zM8 5a.5.5 0 01.5.5v7a.5.5 0 01-1 0v-7A.5.5 0 018 5zm3 .5a.5.5 0 00-1 0v7a.5.5 0 001 0v-7z\" clip-rule=\"evenodd\"/></svg>";

const ALERT_ICON = "<svg class=\"bi bi-exclamation-circle-fill\" width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\"><path fill-rule=\"evenodd\" d=\"M16 8A8 8 0 110 8a8 8 0 0116 0zM8 4a.905.905 0 00-.9.995l.35 3.507a.552.552 0 001.1 0l.35-3.507A.905.905 0 008 4zm.002 6a1 1 0 100 2 1 1 0 000-2z\" clip-rule=\"evenodd\"/></svg>";
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

function add_alm_icon(x, id) {
  if (x) $(".alarm-icon-" + id).html(ALERT_ICON);
  else $(".alarm-icon-" + id).html("");
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

function delete_event(id) {
  $.ajax({
    url:"/_delete_event.php",
    type:"POST",
    contentType:"application/json",
    data: JSON.stringify({event_id_to_delete : id}),
    success: function(data){
      alert(data);
    }
  })
}

function id_to_remove() {
  var device_id = document.getElementById("device_id_to_remove").value;
  if (isNaN(device_id) || device_id < 1 || device_id % 1 != 0) {
    alert("ID number is invalid");
    return false;
  } else {
    $('input[type="text"], textarea').val('');
    $.ajax({
      url:"/_delete_device.php",
      type:"POST",
      contentType:"application/json",
      data: JSON.stringify({device_id_to_delete : device_id.toString()}),
      success: function(data){
        alert(data);
      }
    })
  }
}


function submit_rtu_data() {
  let ip_address = document.getElementById("ip_address").value;
  var port = document.getElementById("port").value;
  var device_id = document.getElementById("device_id").value;
  if (validate_form(ip_address, port, device_id)) {
    $('input[type="text"], textarea').val('');
    // alert("\nIP: " + ip_address + "\nPort: " + port + "\nDevice ID: " + device_id + "\nForm Submitted Successfully......");
    $.ajax({
      url:"/_add_device.php",
      type:"POST",
      contentType:"application/json",
      data: JSON.stringify({rtu_id : device_id.toString(), rtu_ip: ip_address.toString(), port: port.toString()}),
      success: function(data){
        alert(data);
      }
    })
  }
}


function update_temp_new() {
  $.ajax({
    url:"/post_responder.php",
    type:"POST",
    contentType:"application/json",
    data: JSON.stringify({max_hist : max_hist}),
    success: function(data){
      //updating the device information device_info_table_
      data_obj = JSON.parse(data);
      for (let i = 0; i < data_obj.length; i++) {
        let table_info_str = "";
        table_info_str += "<td>" + data_obj[i].rtu_id + "</td>";
        table_info_str += "<td>" + data_obj[i].rtu_ip + "</td>";
        table_info_str += "<td>" + data_obj[i].rtu_port + "</td>";
        table_info_str += "<td>" + data_obj[i].type + "</td>";
        add_alm_icon(data_obj[i].link != "1", data_obj[i].rtu_id);
        table_info_str += "<td>" + (data_obj[i].link == "1" ? "<span class = \"text-success\">Online</span>" : "<span class = \"text-danger\">Offline</span>") + "</td>";
        table_info_str += "<td>" + data_obj[i].display_count + "</td>";
        $("#device_info_table_"+data_obj[i].rtu_id).html(table_info_str);

        //updating the events table
        let events_str = "";
        for (let j = 0; j < data_obj[i].events.length; j++) {
          events_str +="<tr>";
          let event_id = data_obj[i].events[j].event_id;
          events_str += "<td><button type=\"button\" class=\"btn btn-danger\" onclick=\"delete_event(" + event_id + ")\">" + DELETE_ICON + "</button></td>";
          events_str += "<td>" + data_obj[i].events[j].time + "</td>";
          events_str += "<td>" + data_obj[i].events[j].description + "</td>";
          events_str += "<td>" + data_obj[i].events[j].type + "</td>";
          events_str += "<td>" + data_obj[i].events[j].display + "</td>";
          events_str += "<td>" + data_obj[i].events[j].point + "</td>";
          let val = Number(data_obj[i].events[j].value);
          let unit_v = data_obj[i].events[j].unit;
          events_str += "<td>" + (unit_v == "c" ? (is_celcius ? val : to_f(val)) : val) + "</td>";
          events_str += "<td>" + (unit_v == "c" ? (is_celcius ? unit_v : "f") : unit_v) + "</td>";
          events_str += "</tr>";
        }
        $("#events_table_"+data_obj[i].rtu_id).html(events_str);


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
        }
        $("#standing_table_"+data_obj[i].rtu_id).html(standing_str);
        //change the standing alarm text to red if there are any alarms
        add_alm_icon(alarm_count, data_obj[i].rtu_id);
        // if (alarm_count) $(".alarm-icon-" + data_obj[i].rtu_id).html(ALERT_ICON);
        // else $(".alarm-icon-" + data_obj[i].rtu_id).html("");

        //updating the thresholds
        for (let k = 0; k < Number(data_obj[i].display_count); k++) {
          //updating the canvases
          let id_text = data_obj[i].standing[4*k].rtu_id + "_" + data_obj[i].standing[4*k].display;
          // console.log(id_text);
          $("#v-pills-display-"+id_text).html("<canvas id=\"threshold_canvas_" + id_text + "\" width=\"" + $("#v-pills-display-"+id_text).width() + "\" height=\"200\">Unsupported</canvas>");
          draw_threshold("threshold_canvas_" + id_text,
            data_obj[i].standing[4*k+0].threshold_value, data_obj[i].standing[4*k+1].threshold_value,
            data_obj[i].standing[4*k+2].threshold_value, data_obj[i].standing[4*k+3].threshold_value,
            data_obj[i].standing[4*k].analog_value, data_obj[i].standing[4*k].unit);
        }
      }
    }
  })
}

window.setInterval(function(){
  update_temp_new();
}, update_delay);
