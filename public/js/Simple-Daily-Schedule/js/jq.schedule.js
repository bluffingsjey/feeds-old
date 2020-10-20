
(function($) {
    $.fn.timeSchedule = function(options){
        var defaults = {
            rows : {},
            startTime: "00:00",
            endTime: "24:00",
            widthTimeX:25,		// 1cell The width of the neighborhood(px)
            widthTime:600,		// Delimiter time ( in seconds )
            timeLineY:50,		// timeline height(px)
            timeLineBorder:1,	// timeline height border
            timeBorder:1,		// border width
            timeLinePaddingTop:0,
            timeLinePaddingBottom:0,
            headTimeBorder:1,	// time border width
            dataWidth:160,		// data width
            verticalScrollbar:0,	// vertical scrollbar width
            // event
            init_data: null,
            change: null,
            click: null,
            append: null,
            time_click: null,
            debug:"",			// debug selecter
			counter:0,
        };
        this.calcStringTime = function(string) {
            var slice = string.split(':');
            var h = Number(slice[0]) * 60 * 60;
            var i = Number(slice[1]) * 60;
            var min = h + i;
            return min;
        };
        this.formatTime = function(min) {
            var h = "" + (min/36000|0) + (min/3600%10|0);
            var i = "" + (min%3600/600|0) + (min%3600/60%10|0);
            var string = h + ":" + i;

			switch(string){
        case "00:00":
					string = "12 am";
					break;
				case "01:00":
					string = "1 am";
					break;
				case "02:00":
					string = "2 am";
					break;
				case "03:00":
					string = "3 am";
					break;
				case "04:00":
					string = "4 am";
					break;
        case "05:00":
					string = "5 am";
					break;
				case "06:00":
					string = "6 am";
					break;
				case "07:00":
					string = "7 am";
					break;
				case "08:00":
					string = "8 am";
					break;
				case "09:00":
					string = "9 am";
					break;
				case "10:00":
					string = "10 am";
					break;
				case "11:00":
					string = "11 am";
					break;
				case "12:00":
					string = "12 pm";
					break;
				case "13:00":
					string = "1 pm";
					break;
				case "14:00":
					string = "2 pm";
					break;
				case "15:00":
					string = "3 pm";
					break;
				case "16:00":
					string = "4 pm";
					break;
				case "17:00":
					string = "5 pm";
					break;
        case "18:00":
					string = "6 pm";
					break;
        case "19:00":
					string = "7 pm";
					break;
        case "20:00":
					string = "8 pm";
					break;
				case "21:00":
					string = "9 pm";
					break;
				case "22:00":
					string = "10 pm";
					break;
				case "23:00":
					string = "11 pm";
					break;

				default:
					string = h + ":" + i;
			}

            return string;
        };

        var setting = $.extend(defaults,options);
        this.setting = setting;
        var scheduleData = new Array();
        var timelineData = new Array();
        var $element = $(this);
        var element = (this);
        var tableStartTime = element.calcStringTime(setting.startTime);
        var tableEndTime = element.calcStringTime(setting.endTime);
        var currentNode = null;
        tableStartTime -= (tableStartTime % setting.widthTime);
        tableEndTime -= (tableEndTime % setting.widthTime);

        this.getScheduleData = function(){
            return scheduleData;
        }
        this.getTimelineData = function(){
            return timelineData;
        }
        // Get the current time line number
        this.getTimeLineNumber = function(top){
            var num = 0;
            var n = 0;
            var tn = Math.ceil(top / (setting.timeLineY + setting.timeLinePaddingTop + setting.timeLinePaddingBottom));
            for(var i in setting.rows){
                var r = setting.rows[i];
                var tr = 0;
                if(typeof r["schedule"] == Object){
                    tr = r["schedule"].length;
                }
                if(currentNode && currentNode["timeline"]){
                    tr ++;
                }
                n += Math.max(tr,1);
                if(n >= tn){
                    break;
                }
                num ++;
            }
            return num;
        }
        // Additional background data
        this.addScheduleBgData = function(data){
            var st = Math.ceil((data["start"] - tableStartTime) / setting.widthTime);
            var et = Math.floor((data["end"] - tableStartTime) / setting.widthTime);
            var $bar = jQuery('<div class="sc_bgBar"><span class="text"></span></div>');
            var stext = element.formatTime(data["start"]);
            var etext = element.formatTime(data["end"]);
            var snum = element.getScheduleCount(data["timeline"]);
            $bar.css({
                left : (st * setting.widthTimeX),
                top : 0,
                width : ((et - st) * setting.widthTimeX),
                height : $element.find('.sc_main .timeline').eq(data["timeline"]).height()
            });
            if(data["text"]){
                $bar.find(".text").text(data["text"]);
            }
            if(data["class"]){
                $bar.addClass(data["class"]);
            }
            //$element.find('.sc_main').append($bar);
            $element.find('.sc_main .timeline').eq(data["timeline"]).append($bar);
        }
        // Adding a Schedule
        this.addScheduleData = function(data){
            var st = Math.ceil((data["start"] - tableStartTime) / setting.widthTime);
            var et = Math.floor((data["end"] - tableStartTime) / setting.widthTime);
            var snum = element.getScheduleCount(data["timeline"]);
		    var $bar = jQuery('<div class="sc_Bar timeline-holder-'+data["timeline"]+'" id="timeline-'+data["timeline"]+'-'+snum+'"><span class="head"><span class="time"></span></span><span class="text"></span></div>');
            var stext = element.formatTime(data["start"]);
            var etext = element.formatTime(data["end"]);

            $bar.css({
                left : (st * setting.widthTimeX),
                top : ((snum * setting.timeLineY) + setting.timeLinePaddingTop),
                width : ((et - st) * setting.widthTimeX),
                height : (setting.timeLineY)
            });
            $bar.find(".time").text(stext+"-"+etext);
            if(data["text"]){
                $bar.find(".text").text(data["text"]);
            }
            if(data["class"]){
                $bar.addClass(data["class"]);
            }
            //$element.find('.sc_main').append($bar);
            $element.find('.sc_main .timeline').eq(data["timeline"]).append($bar);
            // Additional data
            scheduleData.push(data);
            // key
            var key = scheduleData.length - 1;
            $bar.data("sc_key",key);

            $bar.bind("mouseup",function(){
                // Call When the callback is set
                if(setting.click){
                    if(jQuery(this).data("dragCheck") !== true && jQuery(this).data("resizeCheck") !== true){
                        var node = jQuery(this);
                        var sc_key = node.data("sc_key");
                        setting.click(node, scheduleData[sc_key]);
                    }
                }
            });

            var $node = $element.find(".sc_Bar");
            // move node.
            $node.draggable({
                grid: [ setting.widthTimeX, 1 ],
                containment: ".sc_main",
				axis:	"x",
                helper : 'original',
                start: function(event, ui) {
					setting.counter = 1;
                    var node = {};
                    node["node"] = this;
                    node["offsetTop"] = ui.position.top;
                    node["offsetLeft"] = ui.position.left;
                    node["currentTop"] = ui.position.top;
                    node["currentLeft"] = ui.position.left;
                    node["timeline"] = element.getTimeLineNumber(ui.position.top);
                    node["nowTimeline"] = node["timeline"];
                    currentNode = node;
                },
                drag: function(event, ui) {
                    jQuery(this).data("dragCheck",true);
                    if(!currentNode){
                        return false;
                    }
                    var $moveNode = jQuery(this);
                    var sc_key = $moveNode.data("sc_key");
                    var originalTop = ui.originalPosition.top;
                    var originalLeft = ui.originalPosition.left;
                    var positionTop = ui.position.top;
                    var positionLeft = ui.position.left;
                    var timelineNum = element.getTimeLineNumber(ui.position.top);
                    // Correction of the position
                    //ui.position.top = Math.floor(ui.position.top / setting.timeLineY) * setting.timeLineY;
                    //ui.position.top = element.getScheduleCount(timelineNum) * setting.timeLineY;
                    ui.position.left = Math.floor(ui.position.left / setting.widthTimeX) * setting.widthTimeX;


                    //$moveNode.find(".text").text(timelineNum+" "+(element.getScheduleCount(timelineNum) + 1));
                    if(currentNode["nowTimeline"] != timelineNum){
                        // Adjustment of height
                        //element.resizeRow(currentNode["nowTimeline"],element.getScheduleCount(currentNode["nowTimeline"]));
                        //element.resizeRow(timelineNum,element.getScheduleCount(timelineNum) + 1);
                        // Current timeline
                        currentNode["nowTimeline"] = timelineNum;
                    }else{
                        //ui.position.top = currentNode["currentTop"];
                    }
                    currentNode["currentTop"] = ui.position.top;
                    currentNode["currentLeft"] = ui.position.left;
                    // Text change
                    element.rewriteBarText($moveNode,scheduleData[sc_key]);

					console.log(currentNode["currentTop"]);
                    return true;
                },
                // Processing after the movement of the element is finished
                stop: function(event, ui) {
                    jQuery(this).data("dragCheck",false);
                    currentNode = null;


                    var node = jQuery(this);
                    var sc_key = node.data("sc_key");
                    var x = node.position().left;
                    var w = node.width();
                    var start = tableStartTime + (Math.floor(x / setting.widthTimeX) * setting.widthTime);
                    var end = tableStartTime + (Math.floor((x + w) / setting.widthTimeX) * setting.widthTime);
                    //var end = start + ((scheduleData[sc_key]["end"] - scheduleData[sc_key]["start"]));

                    scheduleData[sc_key]["start"] = start;
                    scheduleData[sc_key]["end"] = end;
                    // Call When the callback is set
                    if(setting.change){
                        setting.change(node, scheduleData[sc_key]);
                    }
                }
            });
            $node.resizable({
                handles:'e',
                grid: [ setting.widthTimeX, setting.timeLineY ],
                minWidth:setting.widthTimeX,
                start: function(event, ui){
                    var node = jQuery(this);
                    node.data("resizeCheck",true);
					setting.counter = 1;
                },
                // Processing after the movement of the element is finished
                stop: function(event, ui){

                    var node = jQuery(this);
                    var sc_key = node.data("sc_key");
                    var x = node.position().left;
                    var w = node.width();
                    var start = tableStartTime + (Math.floor(x / setting.widthTimeX) * setting.widthTime);
                    var end = tableStartTime + (Math.floor((x + w) / setting.widthTimeX) * setting.widthTime);
                    var timelineNum = scheduleData[sc_key]["timeline"];

                    scheduleData[sc_key]["start"] = start;
                    scheduleData[sc_key]["end"] = end;

                    // Height adjustment
                    element.resetBarPosition(timelineNum);
                    // Text change
                    element.rewriteBarText(node,scheduleData[sc_key]);

                    node.data("resizeCheck",false);
                    // Call When the callback is set
                    if(setting.change){
                        setting.change(node, scheduleData[sc_key]);

                    }
                }
            });
            return key;
        };
        // Get the number of schedule
        this.getScheduleCount = function(n){
            var num = 0;
            for(var i in scheduleData){
                if(scheduleData[i]["timeline"] == n){
                    num ++;
                }
            }
            return num;
        };
        // add
        this.addRow = function(timeline,row){
            var title = row["title"];
            var id = $element.find('.sc_main .timeline').length;
            console.log(row['schedule'][0]['data']);
            var driver_id = 0;
            if(row['schedule'][0]['data'] != null){
              driver_id = row['schedule'][0]['data']['driver_id'];
            }else{
              driver_id = driver_id;
            }

            var html;

            html = '';
            //html += '<div class="timeline drivers-'+id+'"><span>'+title+'</span></div>';
			      html += '<div class="timeline drivers-'+id+' drivers-lists" driver-id='+driver_id+' data-toggle="tooltip" data-placement="top" title="Click here to view ETA">'+title+'</div>';
            var $data = jQuery(html);
            // event call
            if(setting.init_data){
                setting.init_data($data,row);
            }
            $element.find('.sc_data_scroll').append($data);

            html = '';
            html += '<div class="timeline"></div>';
            var $timeline = jQuery(html);
            for(var t=tableStartTime;t<tableEndTime;t+=setting.widthTime){
                var tl_html = '<div class="tl"';

                    switch(t){
                      case 3000:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 6600:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 10200:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 13800:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 17400:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 21000:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 24600:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 28200:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 31800:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 35400:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 39000:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 42600:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 46200:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 49800:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 53400:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 57000:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 60600:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 64200:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 67800:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 71400:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 75000:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 78600:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 82200:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      case 85800:
                        tl_html += "style='border-right: solid 1px #000000;'";
                      default:
                        tl_html += "";
                    }

                    tl_html += '></div>';

                    /*if(i != 72000){
                      tl_html += "id='per_hour'";
                    }else{
                      tl_html += "";
                    }
                    tl_html += '></div>';*/


                var $tl = jQuery(tl_html);
                $tl.width(setting.widthTimeX - setting.timeBorder);

                $tl.data("time",element.formatTime(t));
                $tl.data("timeline",timeline);
                $timeline.append($tl);
            }
            // Click event
            if(setting.time_click){
                $timeline.find(".tl").click(function(){
                    setting.time_click(this,jQuery(this).data("time"),jQuery(this).data("timeline"),timelineData[jQuery(this).data("timeline")]);
                });
            }
            $element.find('.sc_main').append($timeline);

            timelineData[timeline] = row;

            if(row["class"] && (row["class"] != "")){
                $element.find('.sc_data .timeline').eq(id).addClass(row["class"]);
                $element.find('.sc_main .timeline').eq(id).addClass(row["class"]);
            }
            // Schedule Timeline
            if(row["schedule"]){
                for(var i in row["schedule"]){
                    var bdata = row["schedule"][i];
                    var s = element.calcStringTime(bdata["start"]);
                    var e = element.calcStringTime(bdata["end"]);

                    var data = {};
                    data["timeline"] = id;
                    data["start"] = s;
                    data["end"] = e;
                    if(bdata["text"]){
                        data["text"] = bdata["text"];
                    }
                    data["data"] = {};
                    if(bdata["data"]){
                        data["data"] = bdata["data"];
                    }
                    element.addScheduleData(data);
                }
            }
            // Adjustment of height
            element.resetBarPosition(id);
            $element.find('.sc_main .timeline').eq(id).droppable({
                accept: ".sc_Bar",
                drop: function(ev, ui) {
                    var node = ui.draggable;
                    var sc_key = node.data("sc_key");
                    var nowTimelineNum = scheduleData[sc_key]["timeline"];
                    var timelineNum = $element.find('.sc_main .timeline').index(this);
                    // Changing the time line
                    scheduleData[sc_key]["timeline"] = timelineNum;
                    node.appendTo(this);
                    // Height adjustment
                    element.resetBarPosition(nowTimelineNum);
                    element.resetBarPosition(timelineNum);
                }
            });
            // 	Call When the callback is set
            if(setting.append){
                $element.find('.sc_main .timeline').eq(id).find(".sc_Bar").each(function(){
                    var node = jQuery(this);
                    var sc_key = node.data("sc_key");
                    setting.append(node, scheduleData[sc_key]);
                });
            }
        };
        this.getScheduleData = function(){
            var data = new Array();

            for(var i in timelineData){
                if(typeof timelineData[i] == "undefined") continue;
                var timeline = jQuery.extend(true, {}, timelineData[i]);
                timeline.schedule = new Array();
                data.push(timeline);
            }

            for(var i in scheduleData){
                if(typeof scheduleData[i] == "undefined") continue;
                var schedule = jQuery.extend(true, {}, scheduleData[i]);
                schedule.start = this.formatTime(schedule.start);
                schedule.end = this.formatTime(schedule.end);
                var timelineIndex = schedule.timeline;
                delete schedule.timeline;
                data[timelineIndex].schedule.push(schedule);
            }

            return data;
        };
        // Change text
        this.rewriteBarText = function(node,data){
            var x = node.position().left;
            var w = node.width();
            var start = tableStartTime + (Math.floor(x / setting.widthTimeX) * setting.widthTime);
            //var end = tableStartTime + (Math.floor((x + w) / setting.widthTimeX) * setting.widthTime);
            var end = start + (data["end"] - data["start"]);
            var html = element.formatTime(start)+"-"+element.formatTime(end);
            jQuery(node).find(".time").html(html);
        }
        this.resetBarPosition = function(n){
            // Sequence of elements sort
            var $bar_list = $element.find('.sc_main .timeline').eq(n).find(".sc_Bar");
            var codes = [];
            for(var i=0;i<$bar_list.length;i++){
                codes[i] = {code:i,x:jQuery($bar_list[i]).position().left};
            };
            // sort
            codes.sort(function(a,b){
                if(a["x"] < b["x"]){
                    return -1;
                }else if(a["x"] > b["x"]){
                    return 1;
                }
                return 0;
            });
            var check = [];
            var h = 0;
            var $e1,$e2;
            var c1,c2;
            var s1,e1,s2,e2;
            for(var i=0;i<codes.length;i++){
                c1 = codes[i]["code"];
                $e1 = jQuery($bar_list[c1]);
                for(h=0;h<check.length;h++){
                    var next = false;
                    L: for(var j=0;j<check[h].length;j++){
                        c2 = check[h][j];
                        $e2 = jQuery($bar_list[c2]);

                        s1 = $e1.position().left;
                        e1 = $e1.position().left + $e1.width();
                        s2 = $e2.position().left;
                        e2 = $e2.position().left + $e2.width();
                        if(s1 < e2 && e1 > s2){
                            next = true;
                            continue L;
                        }
                    }
                    if(!next){
                        break;
                    }
                }
                if(!check[h]){
                    check[h] = [];
                }
                $e1.css({top:((h * setting.timeLineY) + setting.timeLinePaddingTop)});
                check[h][check[h].length] = c1;
            }
            // Adjustment of height
            this.resizeRow(n,check.length);
        };
        this.resizeRow = function(n,height){
            //var h = Math.max(element.getScheduleCount(n),1);
            var h = Math.max(height,1);
            $element.find('.sc_data .timeline').eq(n).height((h * setting.timeLineY) - setting.timeLineBorder + setting.timeLinePaddingTop + setting.timeLinePaddingBottom);
            $element.find('.sc_main .timeline').eq(n).height((h * setting.timeLineY) - setting.timeLineBorder + setting.timeLinePaddingTop + setting.timeLinePaddingBottom);

            $element.find('.sc_main .timeline').eq(n).find(".sc_bgBar").each(function(){
                jQuery(this).height(jQuery(this).closest(".timeline").height());
            });

			if(setting.counter == 0){
				$element.find(".sc_data").height($element.find(".sc_main_box").height() - 17);
			} else {
				$element.find(".sc_data").height($element.find(".sc_main_box").height());
			}
			$element.find(".sc_data").height($element.find(".sc_main_box").height());

        }
        // resizeWindow
        this.resizeWindow = function(){
            var sc_width = $element.width();
            var sc_main_width = sc_width - setting.dataWidth - (setting.verticalScrollbar);
            var cell_num = Math.floor((tableEndTime - tableStartTime) / setting.widthTime);
            $element.find(".sc_header_cell").width(setting.dataWidth);
            $element.find(".sc_data,.sc_data_scroll").width(setting.dataWidth);
            $element.find(".sc_header").width(sc_main_width);
            $element.find(".sc_main_box").width(sc_main_width);
            $element.find(".sc_header_scroll").width(setting.widthTimeX*cell_num);
            $element.find(".sc_main_scroll").width(setting.widthTimeX*cell_num);

        };
        // init
        this.init = function(){
            var html = '';
            html += '<div class="sc_menu">'+"\n";
            html += '<div class="sc_header_cell">Drivers</div>'+"\n";
            html += '<div class="sc_header" style="overflow: hidden">'+"\n";
            html += '<div class="sc_header_scroll">'+"\n";
            html += '</div>'+"\n";
            html += '</div>'+"\n";
            html += '<br class="clear" />'+"\n";
            html += '</div>'+"\n";
            html += '<div class="sc_wrapper">'+"\n";
            html += '<div class="sc_data">'+"\n";
            html += '<div class="sc_data_scroll">'+"\n";
            html += '</div>'+"\n";
            html += '</div>'+"\n";
            html += '<div class="sc_main_box" id="sc_main_box" style="overflow:auto;">'+"\n";
            html += '<div class="sc_main_scroll">'+"\n";
            html += '<div class="sc_main">'+"\n";
            html += '</div>'+"\n";
            html += '</div>'+"\n";
            html += '</div>'+"\n";
            html += '<br class="clear" />'+"\n";
            html += '</div>'+"\n";

            $element.append(html);

            $element.find(".sc_main_box").scroll(function(){
                $element.find(".sc_data_scroll").css("top", $(this).scrollTop() * -1);
                $element.find(".sc_header_scroll").css("left", $(this).scrollLeft() * -1);

            });
            // add time cell
            var cell_num = Math.floor((tableEndTime - tableStartTime) / setting.widthTime);
            var before_time = -1;
            for(var t=tableStartTime;t<tableEndTime;t+=setting.widthTime){

                if(
                    (before_time < 0) ||
                        (Math.floor(before_time / 3600) != Math.floor(t / 3600))){
                    var html = '';
                    html += '<div class="sc_time">'+element.formatTime(t)+'</div>';
                    var $time = jQuery(html);
                    var cell_num = Math.floor(Number(Math.min((Math.ceil((t + setting.widthTime) / 3600) * 3600),tableEndTime) - t) / setting.widthTime);
                    $time.width((cell_num * setting.widthTimeX) - setting.headTimeBorder);
                    $element.find(".sc_header_scroll").append($time);

                    before_time = t;
                }
            }

            jQuery(window).resize(function(){
                element.resizeWindow();
            }).trigger("resize");

            // addrow
            for(var i in setting.rows){
                this.addRow(i,setting.rows[i]);
            }

        };
        // Initialization
        this.init();

        this.debug = function(){
            var html = '';
            for(var i in scheduleData){
                html += '<div>';

                html += i+" : ";
                var d = scheduleData[i];
                for(var n in d){
                    var dd = d[n];
                    html += n+" "+dd;
                }

                html += '</div>';
            }
            jQuery(setting.debug).html(html);
        };
        if(setting.debug && setting.debug != ""){
            setInterval(function(){
                element.debug();
            },10);
        }

        return( this );
    };
})(jQuery);
