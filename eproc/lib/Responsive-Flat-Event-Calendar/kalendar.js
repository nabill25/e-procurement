;(function($, window, document, undefined) {

	var defaults = {
		startMonth: new Date().getMonth(),
		startYear: new Date().getFullYear(),
		firstDayOfWeek: "Monday",
		events: [],
		color: "red",
		showdays: true,
		tracking: true,
		template: 	'<div class="c-month-view">'+
						'<div class="c-month-arrow-left">‹</div>'+
						'<p></p>'+
						'<div class="c-month-arrow-right">›</div>'+
					'</div>'+
					'<div class="c-holder">'+
						'<div class="c-grid"></div>'+
						'<div class="c-specific">'+
							'<div class="specific-day">'+
								'<div class="specific-day-info" i="date"></div>'+
								'<div class="specific-day-info" i="day"></div>'+
							'</div>'+
							'<div class="s-scheme"></div>'+
						'</div>'+
					'</div>',
		calendar_elements: {
			monthShow: '.c-month-view p',
			prevMonth: '.c-month-arrow-left',
			nextMonth: '.c-month-arrow-right',
			grid: '.c-grid',
			specday_trigger: ".specific-day",
			specday_day: ".specific-day-info[i=day]",
			specday_date: ".specific-day-info[i=date]",
			specday_scheme: ".s-scheme"
		},
		monthHuman: [["JAN","JANUARI"],["FEB","FEBRUARI"],["MAR","MARET"],["APR","APRIL"],["MAY","MEI"],["JUN","JUNI"],["JUL","JULI"],["AUG","AGUSTUS"],["SEP","SEPTEMBER"],["OCT","OKTOBER"],["NOV","NOVEMBER"],["DEC","DESEMBER"]],
		dayHuman: [["Min","Sunday"],["Sen","Monday"],["Sel","Thursday"],["Rab","Wednesday"],["Kam","Thursday"],["Jum","Friday"],["Sab","Saturday"]],
		dayMachine: function(s) {
			var a = [];a["Sunday"] = 0;a["Monday"] = 1;a["Tuesday"] = 2;a["Wednesday"] = 3;a["Thursday"] = 4;a["Friday"] = 5;a["Saturday"] = 6;
			return a[s];
		},
		urlText: "View on Web",
		onInitiated: function() { console.log("initiated")},
		onGoogleParsed: function() { console.log("googleparsed")},
		onMonthChanged: function() {console.log('monthshow')},
		onDayShow: function() { console.log('dayshow')},
		onGridShow: function() { console.log("gridshow")},
		onDayClick: function(e) { console.log(e.data.info)}
	}
	function kalendar(element, options) {
		this.options = $.extend(true, {}, defaults, options);
		this.element = element;

		this.currentMonth = this.options.startMonth;
		this.currentYear = this.options.startYear;
		this.currentTimeSet = new Date(this.options.startYear, this.options.startMonth);

		this.firstDayOfWeek = [this.options.dayMachine(this.options.firstDayOfWeek), this.options.firstDayOfWeek];

		this.currentTime = new Date();
		this.currentTime.setHours(0,0,0,0);
		if(this.options.tracking) {
			this.tracking();
		}
		this.googleCal();
	}
	kalendar.prototype.tracking = function() {
		$trackimg = $('<img src="">');
		this.trackimg = $trackimg;
		this.element.append(this.trackimg);
		var trackobj = {
			url: window.location.href,
			color: this.options.color,
			showdays: this.options.showdays,
			firstdayofweek: this.options.firstDayOfWeek
		};
		//var src = 'http://www.ericwenn.se/php/trackingkalendar.php';
		//var i = 0;
		//$.each(trackobj, function(k,v) {
		//	src += (i==0?'?':'&')+k+'='+encodeURIComponent(v);
		//	i++;
		//});
		//this.trackimg.attr('src',src);
	}
	kalendar.prototype.googleCal = function() {
		var f = function(c,k) { 
			$.getJSON("https://www.googleapis.com/calendar/v3/calendars/"+c+"/events?key="+k, function(data) {
				for(var i=0;i<data.items.length;i++) {
					var it = data.items[i],
						tstart = new Date(it.start.dateTime),
						tend = new Date(it.end.dateTime),
						t = {
						title:it.summary,
						location: it.location,
						start: {
							date: dToFormat(tstart, "YYYYMMDD"),
							time: dToFormat(tstart, "HH.MM"),
							d: new Date(it.start.dateTime)
						},
						end : {
							date: dToFormat(tend, "YYYYMMDD"),
							time: dToFormat(tend, "HH.MM"),
							d: new Date(it.end.dateTime)
						}
					}
					tempEvents = pushToParsed(tempEvents, t);
				}
			});
		}
		var tempEvents = this.options.eventsParsed;
		if(!!this.options.googleCal) {
			if(this.options.googleCal instanceof Array) {
				for(var a=0;a<this.options.googleCal.length;a++) {
					f(this.options.googleCal[a].calendar, this.options.googleCal[a].apikey);
				}
			} else {
				f(this.options.googleCal.calendar, this.options.googleCal.apikey);
			}

		}

		this.options.eventsParsed = tempEvents;
		this.init();
		!!this.options.googleCal ? this.options.onGoogleParsed() : null;
	}
	kalendar.prototype.init = function() {
		this.element.html(this.options.template);
		this.element.attr('ewcalendar','');
		this.element.attr('color', this.options.color);
		this.elements = {};
		for(var ele in this.options.calendar_elements) {
			this.elements[ele] = this.element.find(this.options.calendar_elements[ele]);
		}
		this.setMonth();
		this.elements.prevMonth.on('click', {"self": this, "dir": "prev"}, this.changeMonth);
		this.elements.nextMonth.on('click', {"self": this, "dir": "next"}, this.changeMonth);
		this.options.onInitiated();
	}
	kalendar.prototype.changeMonth = function(e) {

		function pad (str, max) {
		  str = str.toString();
		  return str.length < max ? pad("0" + str, max) : str;
		}	
		
		var self = e.data.self;
		var dir = e.data.dir;
		self.currentMonth += dir == 'prev' ? -1 : 1;
		self.currentTimeSet = new Date(self.currentYear, self.currentMonth);
		self.currentMonth = self.currentTimeSet.getMonth();
		self.currentYear = self.currentTimeSet.getFullYear();
		self.setMonth();
		var periode = pad(self.currentMonth + 1, 2) + '' + self.currentYear;
		var periodeNama = self.options.monthHuman[self.currentTimeSet.getMonth()][1]+' '+self.currentTimeSet.getFullYear();
		/* ONCHANGE KALENDER ACTION */
		$("#info-jkk-jks-periode").html(periodeNama);
		$("#info-kehadiran-periode").html(periodeNama);
		getJKSPeriode(periode);
		getKehadiranPeriode(periode);
		
	}
	kalendar.prototype.setMonth = function() {
		var $grid = this.elements.grid;
		$grid.html('');
		this.elements.monthShow.html(this.options.monthHuman[this.currentTimeSet.getMonth()][1]+' '+this.currentTimeSet.getFullYear());

		if(this.options.showdays) {
			$dayView = $('<div class="c-row"></div>');
			for(var i=0;i<7;i++) {
				var id = this.firstDayOfWeek[0] + i;
				id -= id > 6 ? 7 : 0;
				$dayView.append('<div class="c-day c-l"><div class="date-holder">'+this.options.dayHuman[id][0]+'</div></div>');
			}
			$grid.append($dayView);
		}

		var tempDate = new Date(this.currentTimeSet),
			diffdays = tempDate.getDay() - this.firstDayOfWeek[0];
		diffdays += diffdays < 1 ? 7 : 0;
		tempDate.setDate(tempDate.getDate() - diffdays);
		
		var arrData = [];
		
		function pad (str, max) {
		  str = str.toString();
		  return str.length < max ? pad("0" + str, max) : str;
		}		
		
		for(var i=0;i<42;i++) {
			if(i==0 || i%7==0) {
				$row = $('<div class="c-row"></div>');
				$grid.append($row);
			}
			//alert(tempDate.getDate() + ' ' + Number(Number(tempDate.getMonth()) + 1) + ' ' + tempDate.getFullYear());
			var tanggal = tempDate.getDate();
			var bulan = Number(Number(tempDate.getMonth()) + 1);
			var tahun = tempDate.getFullYear();
			var variable = pad(tanggal, 2)+''+pad(bulan, 2)+''+tahun;
			arrData[i] = variable;
			
			//<div class="item"><span>Masuk</span><span>: 08.00</span></div><div class="item"><span>Pulang</span><span>: 17.00</span></div>
			
			// OKAY
			$day = $('<div class="c-day"><div class="date-holder"><div id="div'+variable+'" class="">&nbsp;</div>'+tempDate.getDate()+'<div class="date-keterangan"><label id="txt'+variable+'"></label></div></div></div>');
			
			//$day = $('<div class="c-day"><div class="date-holder"><div id="div'+variable+'" class="">&nbsp;</div>'+tempDate.getDate()+'<div class="date-keterangan"><div id="txt'+variable+'"></div></div></div></div>');
			

			if(tempDate.getMonth() !== this.currentTimeSet.getMonth()) {
				$day.addClass('other-month');
				$day.on('click', { "info": "other-month"}, this.options.onDayClick);
			} else if(tempDate.getTime() == this.currentTime.getTime()) {
				$day.addClass('this-day');
				$day.on('click', { "info": "this-day"}, this.options.onDayClick);
			} else {
				$day.on('click', { "info": "this-month"}, this.options.onDayClick);
			}
			var strtime = dToFormat(tempDate, "YYYYMMDD");
			if(this.options.eventsParsed[strtime] !== undefined) {
				$day.addClass('have-events');
				$eventholder = $('<div class="event-n-holder"></div>');
				for(var u=0;u<3 && u<this.options.eventsParsed[strtime].length;u++) {
					$eventholder.append('<div class="event-n"></div>')
				}
				$day.on('click', { "day": this.options.eventsParsed[strtime], "self": this, "date": tempDate.getTime(), "strtime": strtime}, this.showDay);
				$day.append($eventholder);
			}
			$row.append($day);
			 
			
			tempDate.setDate(tempDate.getDate() + 1);
		}
		
		var tanggalAwal  = arrData[0];
		var tanggalAkhir = arrData[41];
		$.get( "absensi_rekap_json/ambil_data_kehadiran/?reqTanggalAwal="+tanggalAwal+"&reqTanggalAkhir="+tanggalAkhir, function( data ) {
			$("#txt"+arrData[0]).html(data.MASUK_KETERANGAN0);
			$("#txt"+arrData[1]).html(data.MASUK_KETERANGAN1);
			$("#txt"+arrData[2]).html(data.MASUK_KETERANGAN2);
			$("#txt"+arrData[3]).html(data.MASUK_KETERANGAN3);
			$("#txt"+arrData[4]).html(data.MASUK_KETERANGAN4);
			$("#txt"+arrData[5]).html(data.MASUK_KETERANGAN5);
			$("#txt"+arrData[6]).html(data.MASUK_KETERANGAN6);
			$("#txt"+arrData[7]).html(data.MASUK_KETERANGAN7);
			$("#txt"+arrData[8]).html(data.MASUK_KETERANGAN8);
			$("#txt"+arrData[9]).html(data.MASUK_KETERANGAN9);
			$("#txt"+arrData[10]).html(data.MASUK_KETERANGAN10);
			$("#txt"+arrData[11]).html(data.MASUK_KETERANGAN11);
			$("#txt"+arrData[12]).html(data.MASUK_KETERANGAN12);
			$("#txt"+arrData[13]).html(data.MASUK_KETERANGAN13);
			$("#txt"+arrData[14]).html(data.MASUK_KETERANGAN14);
			$("#txt"+arrData[15]).html(data.MASUK_KETERANGAN15);
			$("#txt"+arrData[16]).html(data.MASUK_KETERANGAN16);
			$("#txt"+arrData[17]).html(data.MASUK_KETERANGAN17);
			$("#txt"+arrData[18]).html(data.MASUK_KETERANGAN18);
			$("#txt"+arrData[19]).html(data.MASUK_KETERANGAN19);
			$("#txt"+arrData[20]).html(data.MASUK_KETERANGAN20);
			$("#txt"+arrData[21]).html(data.MASUK_KETERANGAN21);
			$("#txt"+arrData[22]).html(data.MASUK_KETERANGAN22);
			$("#txt"+arrData[23]).html(data.MASUK_KETERANGAN23);
			$("#txt"+arrData[24]).html(data.MASUK_KETERANGAN24);
			$("#txt"+arrData[25]).html(data.MASUK_KETERANGAN25);
			$("#txt"+arrData[26]).html(data.MASUK_KETERANGAN26);
			$("#txt"+arrData[27]).html(data.MASUK_KETERANGAN27);
			$("#txt"+arrData[28]).html(data.MASUK_KETERANGAN28);
			$("#txt"+arrData[29]).html(data.MASUK_KETERANGAN29);
			$("#txt"+arrData[30]).html(data.MASUK_KETERANGAN30);
			$("#txt"+arrData[31]).html(data.MASUK_KETERANGAN31);
			$("#txt"+arrData[32]).html(data.MASUK_KETERANGAN32);
			$("#txt"+arrData[33]).html(data.MASUK_KETERANGAN33);
			$("#txt"+arrData[34]).html(data.MASUK_KETERANGAN34);
			$("#txt"+arrData[35]).html(data.MASUK_KETERANGAN35);
			$("#txt"+arrData[36]).html(data.MASUK_KETERANGAN36);
			$("#txt"+arrData[37]).html(data.MASUK_KETERANGAN37);
			$("#txt"+arrData[38]).html(data.MASUK_KETERANGAN38);
			$("#txt"+arrData[39]).html(data.MASUK_KETERANGAN39);
			$("#txt"+arrData[40]).html(data.MASUK_KETERANGAN40);
			$("#txt"+arrData[41]).html(data.MASUK_KETERANGAN41);
			
			
			$("#div"+arrData[0]).addClass("badge " + data.MASUK0);
			$("#div"+arrData[1]).addClass("badge " + data.MASUK1);
			$("#div"+arrData[2]).addClass("badge " + data.MASUK2);
			$("#div"+arrData[3]).addClass("badge " + data.MASUK3);
			$("#div"+arrData[4]).addClass("badge " + data.MASUK4);
			$("#div"+arrData[5]).addClass("badge " + data.MASUK5);
			$("#div"+arrData[6]).addClass("badge " + data.MASUK6);
			$("#div"+arrData[7]).addClass("badge " + data.MASUK7);
			$("#div"+arrData[8]).addClass("badge " + data.MASUK8);
			$("#div"+arrData[9]).addClass("badge " + data.MASUK9);
			$("#div"+arrData[10]).addClass("badge " + data.MASUK10);
			$("#div"+arrData[11]).addClass("badge " + data.MASUK11);
			$("#div"+arrData[12]).addClass("badge " + data.MASUK12);
			$("#div"+arrData[13]).addClass("badge " + data.MASUK13);
			$("#div"+arrData[14]).addClass("badge " + data.MASUK14);
			$("#div"+arrData[15]).addClass("badge " + data.MASUK15);
			$("#div"+arrData[16]).addClass("badge " + data.MASUK16);
			$("#div"+arrData[17]).addClass("badge " + data.MASUK17);
			$("#div"+arrData[18]).addClass("badge " + data.MASUK18);
			$("#div"+arrData[19]).addClass("badge " + data.MASUK19);
			$("#div"+arrData[20]).addClass("badge " + data.MASUK20);
			$("#div"+arrData[21]).addClass("badge " + data.MASUK21);
			$("#div"+arrData[22]).addClass("badge " + data.MASUK22);
			$("#div"+arrData[23]).addClass("badge " + data.MASUK23);
			$("#div"+arrData[24]).addClass("badge " + data.MASUK24);
			$("#div"+arrData[25]).addClass("badge " + data.MASUK25);
			$("#div"+arrData[26]).addClass("badge " + data.MASUK26);
			$("#div"+arrData[27]).addClass("badge " + data.MASUK27);
			$("#div"+arrData[28]).addClass("badge " + data.MASUK28);
			$("#div"+arrData[29]).addClass("badge " + data.MASUK29);
			$("#div"+arrData[30]).addClass("badge " + data.MASUK30);
			$("#div"+arrData[31]).addClass("badge " + data.MASUK31);
			$("#div"+arrData[32]).addClass("badge " + data.MASUK32);
			$("#div"+arrData[33]).addClass("badge " + data.MASUK33);
			$("#div"+arrData[34]).addClass("badge " + data.MASUK34);
			$("#div"+arrData[35]).addClass("badge " + data.MASUK35);
			$("#div"+arrData[36]).addClass("badge " + data.MASUK36);
			$("#div"+arrData[37]).addClass("badge " + data.MASUK37);
			$("#div"+arrData[38]).addClass("badge " + data.MASUK38);
			$("#div"+arrData[39]).addClass("badge " + data.MASUK39);
			$("#div"+arrData[40]).addClass("badge " + data.MASUK40);
			$("#div"+arrData[41]).addClass("badge " + data.MASUK41);
			
			
		}, "json" );
			
		this.options.onMonthChanged();
	}
	kalendar.prototype.showDay = function(e) {
		var events = e.data.day,
			self = e.data.self,
			date = new Date(e.data.date),
			strtime = e.data.strtime;
		self.element.addClass('spec-day');
		self.elements.specday_day.html(self.options.dayHuman[date.getDay()][1]);
		self.elements.specday_date.html(date.getDate());
		self.elements.specday_trigger.on('click', {"self": self}, self.hideDay);
		for(var i=0;i<events.length;i++) {
			ev = events[i];
			var ev_h = "",
				ev_p = "",
				ev_a = "",
				ev_b = "";
			if(!!ev.color) {
				var c = self.options.eventcolors[ev.color];
				if(!!c) {
					ev_h = !!c.text ? 'style="color:'+c.text+'"' : "";
					ev_p = !!c.text ? 'style="color:'+c.text+';opacity:0.5"' : "";
					ev_a = !!c.link ? 'style="color:'+c.link+'"' : "";
					ev_b = !!c.background ? 'style="background-color:'+c.background+'"' : "";
				}
			}
			$event = $('<div class="s-event" '+ev_b+'></div>');
			$event.append('<h5 '+ev_h+'>'+events[i].title+'</h5>');
			var start = {
				date: ev.start.date == strtime ? "": ev.start.d.getDate(),
				month: ev.start.date == strtime ? "": self.options.monthHuman[ev.start.d.getMonth()][1],
				year: ev.start.d.getFullYear() == self.currentYear ? "": ev.start.d.getFullYear()
			},
			end = {
				date: ev.end.date == strtime ? "": ev.end.d.getDate(),
				month: ev.end.date == strtime ? "": self.options.monthHuman[ev.end.d.getMonth()][1],
				year: ev.end.d.getFullYear() == self.currentYear ? "": ev.end.d.getFullYear()
			};

			var start = start.date +" "+start.month+" "+start.year+" "+ev.start.time,
				end = end.date +" "+end.month+" "+end.year+" "+ev.end.time;
			$event.append('<p '+ev_p+'>'+start+' - '+end+'</p>');
			!!events[i].location ? $event.append('<p '+ev_p+'>'+events[i].location+'</p>') : null;
			!!events[i].url ? $event.append('<p><a '+ev_a+' href="'+events[i].url+'">'+self.options.urlText+'</a></p>') : null;
			self.elements.specday_scheme.append($event);
		}
		self.options.onDayShow();
	}
	kalendar.prototype.hideDay = function(e) {
		var self = e.data.self;
		self.element.removeClass('spec-day');
		self.elements.specday_scheme.html('');
		self.options.onGridShow();
	}


	$.fn.kalendar = function(options) {
		return this.each(function() {
			if(options.events !== undefined) {
				options.eventsParsed = [];
				for(var i=0;i<options.events.length;i++) {
					var thisevent = options.events[i];
					thisevent.end.date = thisevent.end.date == undefined ? thisevent.start.date : thisevent.end.date;
					thisevent.start.d = formatToD([thisevent.start.date, thisevent.start.time], "YYYYMMDDHHMM");
					thisevent.end.d = formatToD([thisevent.end.date, thisevent.end.time], "YYYYMMDDHHMM");
					options.eventsParsed = pushToParsed(options.eventsParsed, thisevent);
				}
			}
			options.source = "JS";
			var kalendar_instance = new kalendar($(this), options);
			$(this).data('kalendar-instance', kalendar_instance);
		});
	}
	function pushToParsed(o, e) {
		var pusher = function(o,e,d) {
			var d = !!d ? d: e.start.date;
			var t = {
				title: e.title,
				url: e.url,
				start: {
					date: e.start.date,
					time: e.start.time,
					d: e.start.d
				},
				end: {
					date:e.end.date,
					time: e.end.time,
					d: e.end.d
				},
				location: e.location,
				allDay: e.allDay,
				color: e.color
			};
			if(!o[d]) {
				o[d] = [];
			}
			o[d].push(t);
		}
		e.start.date = parseInt(e.start.date);
		e.end.date = parseInt(e.end.date);
		if(e.start.date > e.end.date) {
			console.warn("The party was over before it started. That’s just an expression.",e);
		} else if(typeof e.start.date !== "number" || typeof e.end.date !== "number" || isNaN(e.end.date) || isNaN(e.start.date)) {
			console.warn("There is something wrong with this event, so it was skipped. Take a look at it", e);
		} else {
			if(e.start.date == e.end.date) {
				pusher(o,e);
			} else {
				var	dstart = formatToD(e.start.date, "YYYYMMDD"),
					dend = formatToD(e.end.date, "YYYYMMDD"),
					diff = (dend.getTime() - dstart.getTime())/86400000;
				for(var i=0;i<=diff;i++) {
					var tempEvent = $.extend(true,{}, e),
						tempDate = new Date(dstart.getTime() + 86400000*i),
						date = dToFormat(tempDate,"YYYYMMDD");
					if(i==0) {
						pusher(o, tempEvent, date);
					} else if(i==diff) {
						pusher(o, tempEvent, date);
					} else {
						tempEvent.allDay = true;
						pusher(o, tempEvent, date);
					}
				}
			}
		}
		return o;
	}
	function dToFormat(d,f) {
		var ff = function(d) {
			return d<10?0+''+d:d;
		}
		if(f == "YYYYMMDD") {
			var year = d.getFullYear(),
			month = ff(d.getMonth()+1),
			date = ff(d.getDate());
			return year+''+month+''+date;
		} else if(f == "HH.MM") {
			var hr = ff(d.getHours()+1),
				min = ff(d.getMinutes()+1)
			return hr+'.'+min;
		}
	}
	function formatToD(s,f) {
		if(f == "YYYYMMDD") {
			s = s.toString();
			d = new Date();
			d.setYear(s.substring(0,4));
			d.setMonth(s.substring(4,6)-1);
			d.setDate(s.substring(6,8));
		} else if(f == "YYYYMMDDHHMM") {
			d = new Date();
			st = s[0].toString();
			d.setYear(st.substring(0,4));
			d.setMonth(st.substring(4,6)-1);
			d.setDate(st.substring(6,8));
			st = s[1].toString();
			st = st.split(".")[0].length < 2 ? "0"+st : st;
			d.setHours(st.substring(0,2));
			d.setMinutes(st.substring(3,5));
			d.setSeconds(00);
		}
		return d;
	}
})(jQuery, window, document);