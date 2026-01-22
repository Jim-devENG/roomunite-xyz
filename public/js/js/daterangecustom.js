'use strict';

function customDaterangeFormat (format) {
	var sessionDateFinal = format.toUpperCase();
	var sepSign = "-";
	var dateFormat = 'YYYY MM DD';
	var showDateFormat = 'YYYY MM DD';

	if (sessionDateFinal.includes("/")){
		sepSign = '/';
	} else if (sessionDateFinal.includes(".")) {
		sepSign = '.';
	} else {
		sepSign = '-';
	}

	var dateSep = dateFormat.replace(/ /g,sepSign);

	switch(sessionDateFinal) {
		case 'YYYY' + sepSign + 'MM' + sepSign + 'DD':
				showDateFormat = 'YYYY MM DD';
				dateSep = showDateFormat.replace(/ /g,sepSign);
			break;
		case 'DD' + sepSign + 'MM' + sepSign + 'YYYY':
				showDateFormat = 'DD MM YYYY';
				dateSep = showDateFormat.replace(/ /g,sepSign);
			break;

		case 'MM' + sepSign + 'DD' + sepSign+ 'YYYY':
				showDateFormat = 'MM DD YYYY';
				dateSep = showDateFormat.replace(/ /g,sepSign);
			break;
		case 'DD' + sepSign + 'M' + sepSign + 'YYYY':
				showDateFormat = 'DD MMM YYYY';
				dateSep = showDateFormat.replace(/ /g,sepSign);
			break;

		case 'YYYY' + sepSign + 'M' + sepSign + 'DD':
				showDateFormat = 'YYYY MMM DD';
				dateSep = showDateFormat.replace(/ /g,sepSign);
			break;
		default:

	}

	return {
		dateFormat: dateFormat,
		showDateFormat: showDateFormat,
		sepSign:sepSign,
		dateSep:dateSep,
	};
}

function dateRangeBtn (startDate, endDate, dt=null, format, maxDays=0, minDays=0) {
	var df = dt;
	var customFormat =	customDaterangeFormat(format);
	if(startDate == undefined || !startDate){
		var startDate = moment();
		startDate = moment(startDate, customFormat.showDateFormat);
		var endDate   = moment();
		endDate = moment(endDate, customFormat.showDateFormat);
	} else {
		startDate = moment(startDate, customFormat.showDateFormat);
		endDate = moment(endDate, customFormat.showDateFormat);
	} 
	
	var days = 15;

    var maxDate = "08-21-2050";
    if(maxDays > 0) {
        maxDate = moment(startDate, customFormat.showDateFormat).add(maxDays, 'days');
        
    }
    
   // maxDate = moment(startDate, customFormat.showDateFormat).add(5, 'days');
	var init = moment();
	var initdate;
	if(dt == 1) {
		init = moment(0);
		if(minDays > 0) {
            initdate = moment(startDate, customFormat.dateFormat).add(3, 'days');
        }
        else {
            initdate =  moment(init, customFormat.dateFormat);
        }
		
		var today = moment();
		today =  moment(today, customFormat.dateFormat);

		$('#daterange-btn').daterangepicker({
			    ranges: {
							'Anytime'	  : [moment(0),moment()],
							'Today'       : [moment(), moment()],
							'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
							'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
							'Last 30 Days': [moment().subtract(29, 'days'), moment()],
							'This Month'  : [moment().startOf('month'), moment().endOf('month')],
							'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			    },
				"autoApply": true,
				"defaultDate": null,
				"useCurrent": false,
				"startDate": startDate,
				"endDate": endDate,
				"minDate": initdate,
				"maxDate": maxDate,
				"drops": "auto",
				"autoUpdateInput": false,
				/*"isInvalidDate": function(date) {
				    
				    console.log("START DATE1:");
				    console.log($('#daterange-btn').val());
				    console.log("MY DATE1: ");
				    console.log(moment(date).format('YYYY-MM-DD'));
				    
                    var dateRanges = [
                            { 'start': moment('08-14-2024'), 'end': moment('08-23-2024') }
                    ];
                    return dateRanges.reduce(function(bool, range) {
                            return bool || (date >= range.start && date <= range.end);
                    }, false);
                } */
			}, function(start, end) {

				var startDate        = moment(start, customFormat.showDateFormat).format(customFormat.dateSep);
				
				console.log("Pick click fuction of start date!");
				$("#startDate").val(startDate);
				var endDate          = moment(end, customFormat.showDateFormat).format(customFormat.dateSep);
				$("#endDate").val(endDate);
				initdate = moment(initdate, customFormat.showDateFormat).format(customFormat.dateSep);
				today = moment(today, customFormat.showDateFormat).format(customFormat.dateSep);
				if (startDate == 'undefined' || endDate == 'undefined') {
					$('#daterange-btn span').html('Pick a date range');
				} else if (startDate == '' || endDate == '' || (startDate === initdate && endDate === today )) {
					$('#daterange-btn span').html('Anytime');

				} else {
						startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
						endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
						$("#startDate").val(startDate);
						$("#endDate").val(endDate);
						$('#daterange-btn span').text(startDate + '-' + endDate );
				}
			});
	} else {
	    	if(minDays > 0) {
            minDays = moment(startDate, customFormat.dateFormat).add(3, 'days');
        }
        else {
            initdate =  moment(init, customFormat.dateFormat);
        }
		
		$('#daterange-btn').daterangepicker({
				"autoApply": true,
				"useCurrent": false,
				"alwaysShowCalendars": true,
				"startDate": startDate,
				"defaultDate": null,
				"endDate": endDate,
				"minDate": initdate,
				"maxDate": maxDate,
				"drops": "auto",
				"autoUpdateInput": false,
			/*	"isInvalidDate": function(date) {
				    
				    console.log("START DATE2:");
				    console.log($('#daterange-btn').val());
				    console.log("MY DATE2: ");
				    console.log(moment(date).format('YYYY-MM-DD'));
				    
                    var dateRanges = [
                            { 'start': moment('08-14-2024'), 'end': moment('08-23-2024') }
                    ];
                    return dateRanges.reduce(function(bool, range) {
                            return bool || (date >= range.start && date <= range.end);
                    }, false);
                }*/
			}, function(start, end) {

				var startDate = moment(start, customFormat.showDateFormat).format(customFormat.dateSep);
				$("#startDate").val(startDate);
				console.log("Pick click fuction of start date!");
				var endDate          = moment(end, customFormat.showDateFormat).format(customFormat.dateSep);
				$("#endDate").val(endDate);
				
					if(startDate=='' && endDate==''){
						$('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range');
					} else {

							startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
							endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
							$("#startDate").val(startDate);
							$("#endDate").val(endDate);
							// $('#daterange-btn span').text(startDate + '-' + endDate );
							if(df == 'single') {
								price_calculation('', '', '');
							}
					}
			}).on('apply.daterangepicker', function(ev, picker) {
			    picker.element.val(picker.startDate.format(picker.locale.format));
              console.log(picker.startDate.format('YYYY-MM-DD'));
              console.log(picker.endDate.format('YYYY-MM-DD'));
            });
	}
}


function dateStartRangeBtn (startDate, dt=null, format) {
	var df = dt;
	var customFormat =	customDaterangeFormat(format);
	if(startDate == undefined || !startDate){
		var startDate = moment();
		startDate = moment(startDate, customFormat.showDateFormat);
		//var endDate   = moment();
		//endDate = moment(endDate, customFormat.showDateFormat);
	} else {
		startDate = moment(startDate, customFormat.showDateFormat);
		//endDate = moment(endDate, customFormat.showDateFormat);
	} 
	
	
	
	//var days = 15;

   /* var maxDate = "08-21-2050";
    if(maxDays > 0) {
        maxDate = moment(startDate, customFormat.showDateFormat).add(maxDays, 'days');
        
    }
    
    maxDate = moment(startDate, customFormat.showDateFormat).add(5, 'days');
    
    */
	var init = moment();
	var initdate;
	if(dt == 1) {
		init = moment(0);
	/*	if(minDays > 0) {
            initdate = moment(startDate, customFormat.dateFormat).add(3, 'days');
        }
        else {
            initdate =  moment(init, customFormat.dateFormat);
        }
        */
        
        initdate =  moment(init, customFormat.dateFormat);
		
		var today = moment();
		today =  moment(today, customFormat.dateFormat);

		$('#datestart-btn').daterangepicker({
			    ranges: {
							'Anytime'	  : [moment(0),moment()],
							'Today'       : [moment(), moment()],
							'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
							'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
							'Last 30 Days': [moment().subtract(29, 'days'), moment()],
							'This Month'  : [moment().startOf('month'), moment().endOf('month')],
							'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		    },
			"autoApply": true,
			"singleDatePicker": true,
			"startDate": startDate,
			"drops": "down",
			"opens": "left"
		}, function(start, end) {

				var startDate        = moment(start, customFormat.showDateFormat).format(customFormat.dateSep);
				
				$("#startDate").val(startDate);
			//	var endDate          = moment(end, customFormat.showDateFormat).format(customFormat.dateSep);
			//	$("#endDate").val(endDate);
				initdate = moment(initdate, customFormat.showDateFormat).format(customFormat.dateSep);
				today = moment(today, customFormat.showDateFormat).format(customFormat.dateSep);
				if (startDate == 'undefined') {
					$('#datestart-btn span').html('Pick a date range');
				} else if (startDate == '' || startDate === initdate) {
					$('#datestart-btn span').html('Anytime');

				} else {
						startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
						//endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
						$("#startDate").val(startDate);
					//	$("#endDate").val(endDate);
						$('#datestart-btn span').text(startDate);
				}
			});
	} else {
	    	
	    	/*
	    if(minDays > 0) {
            minDays = moment(startDate, customFormat.dateFormat).add(3, 'days');
        }
        else {
            initdate =  moment(init, customFormat.dateFormat);
        }
        */
        initdate =  moment(init, customFormat.dateFormat);
		
		$('#datestart-btn').daterangepicker({
				"autoApply": true,
				"singleDatePicker": true,
				"alwaysShowCalendars": true,
				"startDate": startDate,
				"drops": "down",
				"opens": "left",
			}, function(start, end) {

				var startDate = moment(start, customFormat.showDateFormat).format(customFormat.dateSep);
				$("#startDate").val(startDate);
				
			//	var endDate          = moment(end, customFormat.showDateFormat).format(customFormat.dateSep);
			//	$("#endDate").val(endDate);
				
				if(startDate==''){
					$('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range');
				} else {

						startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
					//	endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
						$("#startDate").val(startDate);
					//	$("#endDate").val(endDate);
						// $('#daterange-btn span').text(startDate + '-' + endDate );
						if(df == 'single') {
							price_calculation('', '', '');
						}
				}
			});
	}
}

function dateEndRangeBtn (startDate, endDate, dt=null, format, minDays=0, maxDays=0) {
	var df = dt;
	var customFormat =	customDaterangeFormat(format);
	if(endDate == undefined || !endDate){
	//	var startDate = moment();
	//	startDate = moment(startDate, customFormat.showDateFormat);
		var endDate   = moment();
		endDate = moment(endDate, customFormat.showDateFormat);
	} else {
	//	startDate = moment(startDate, customFormat.showDateFormat);
		endDate = moment(endDate, customFormat.showDateFormat);
	} 
	

   /* var maxDate = "08-21-2050";
    if(maxDays > 0) {
        maxDate = moment(startDate, customFormat.showDateFormat).add(maxDays, 'days');
        
    }
    */
   
	var init = moment();
	var initdate;
	if(dt == 1) {
		init = moment(0);
	/*	if(minDays > 0) {
            initdate = moment(startDate, customFormat.dateFormat).add(3, 'days');
        }
        else {
            initdate =  moment(init, customFormat.dateFormat);
        }
        */
        
        initdate =  moment(init, customFormat.dateFormat);
		
		var today = moment();
		today =  moment(today, customFormat.dateFormat);

		$('#dateend-btn').daterangepicker({
			    ranges: {
							'Anytime'	  : [moment(0),moment()],
							'Today'       : [moment(), moment()],
							'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
							'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
							'Last 30 Days': [moment().subtract(29, 'days'), moment()],
							'This Month'  : [moment().startOf('month'), moment().endOf('month')],
							'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			    },
				"autoApply": true,
				"singleDatePicker": true,
				"endDate": endDate,
				"drops": "auto",
				"opens": "left",
				"isInvalidDate": function(date) {
				    
				    
				    if(minDays > 0) {
				        var mStart = $('#startDate').val();
				         var startDateVal = moment(mStart, "MM-DD-YYYY");
			            var startDateAdjust = startDateVal.add(1, 'days');
			            //var minAcceptedDate = moment(startDateAdjust, customFormat.showDateFormat);
    				    var minAcceptedDate = moment(startDateAdjust, customFormat.showDateFormat).add(minDays - 2, 'days');
    				   //console.log("start date!");
    				   // console.log(mStart);
    				   
                        var dateRanges = [
                                { 'start': moment(startDateAdjust), 'end': moment(minAcceptedDate) }
                        ];
                        return dateRanges.reduce(function(bool, range) {
                                return bool || (date >= range.start && date <= range.end);
                        }, false);
				    } 
				    
				     if(maxDays > 0) {
				         
				          console.log("MAX DAYS 1");
    				   console.log(maxDays);
    				   
			            var mStartEnd = $('#startDate').val();
			            var mStartEndVal = moment(mStartEnd, "MM-DD-YYYY");
			            var maxDateStart = mStartEndVal.add(maxDays, 'days');
			            //var endDateVal = moment(mEnd, "MM-DD-YYYY");
			            //var endDateAdjust = endDateVal.add(1, 'days');
			            //var maxAcceptedDate = moment(maxDateStart, customFormat.showDateFormat);
			            
			            var maxAcceptedDate = moment([year]).endOf('year').format('MM-DD-YYYY');
			            
			            console.log("END DATES" + maxAcceptedDate);
			            
			             var maxDateRanges = [
                                { 'start': moment(maxDateStart), 'end': moment(maxAcceptedDate) }
                        ];
                        
                        return maxDateRanges.reduce(function(bool, range) {
                                return bool || (date >= range.start && date <= range.end);
                        }, false); 
			            
			        }
				    
                } 
			}, function(start, end) {

				//var startDate        = moment(start, customFormat.showDateFormat).format(customFormat.dateSep);
				
				//console.log("Pick click fuction of start date!");
				//$("#startDate").val(startDate);
				var endDate          = moment(end, customFormat.showDateFormat).format(customFormat.dateSep);
				$("#endDate").val(endDate);
				initdate = moment(initdate, customFormat.showDateFormat).format(customFormat.dateSep);
				today = moment(today, customFormat.showDateFormat).format(customFormat.dateSep);
				if (endDate == 'undefined') {
					$('#error-area span').html('Pick a date range');
				} else if (endDate == '' || endDate === today) {
					$('#error-area span').html('Anytime');

				} else {
						//startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
						endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
					//	$("#startDate").val(startDate);
						$("#endDate").val(endDate);
						$('#error-area span').text(startDate + '-' + endDate );
				}
			});
	} else {
	    /*	if(minDays > 0) {
            minDays = moment(startDate, customFormat.dateFormat).add(3, 'days');
        }
        else {
            initdate =  moment(init, customFormat.dateFormat);
        }*/
        
        initdate =  moment(init, customFormat.dateFormat);
		
		$('#dateend-btn').daterangepicker({
				"autoApply": true,
				"singleDatePicker": true,
				"alwaysShowCalendars": true,
				"endDate": endDate,
				"drops": "auto",
				"opens": "left",
			    "isInvalidDate": function(date) {
			        
			        
			        if(minDays > 0) {
			            
			            var mStart = $('#startDate').val();
			            var startDateVal = moment(mStart, "MM-DD-YYYY");
			            var startDateAdjust = startDateVal.add(1, 'days');
			           // var minAcceptedDate = moment(startDateAdjust, customFormat.showDateFormat);
    				    var minAcceptedDate = moment(startDateAdjust, customFormat.showDateFormat).add(minDays - 2, 'days');
    				    
    				   // console.log("start date!");
    				   // console.log(mStart);
    				    
                        var dateRanges = [
                                { 'start': moment(startDateAdjust), 'end': moment(minAcceptedDate) }
                        ];
                        
                        return dateRanges.reduce(function(bool, range) {
                                return bool || (date >= range.start && date <= range.end);
                        }, false); 
			        } 
			        
			         if(maxDays > 0) {
			             
			             console.log("MAX DAYS");
    				   console.log(maxDays);


			            var mStartEnd = $('#startDate').val();
			            var mStartEndVal = moment(mStartEnd, "MM-DD-YYYY");
			            var maxDateStart = mStartEndVal.add(maxDays, 'days');
			            //var endDateVal = moment(mEnd, "MM-DD-YYYY");
			            //var endDateAdjust = endDateVal.add(1, 'days');
			            //var maxAcceptedDate = moment(maxDateStart, customFormat.showDateFormat);
			            
			            //var maxAcceptedDate = moment([year]).endOf('year').format('MM-DD-YYYY');
			            
			            var maxAcceptedDate = moment("08-21-2025", "MM-DD-YYYY");
			            
			            console.log("END DATES" + maxAcceptedDate);
			            
			             var maxDateRanges = [
                                { 'start': moment(maxDateStart), 'end': moment(maxAcceptedDate) }
                        ];
                        
                        return maxDateRanges.reduce(function(bool, range) {
                                return bool || (date >= range.start && date <= range.end);
                        }, false); 
			            
			        }
			        
                } 
			}, function(start, end) {

				//var startDate = moment(start, customFormat.showDateFormat).format(customFormat.dateSep);
			//	$("#startDate").val(startDate);
				//console.log("Pick click fuction of start date!");
				var endDate          = moment(end, customFormat.showDateFormat).format(customFormat.dateSep);
				$("#endDate").val(endDate);
				
					if(endDate==''){
						$('#error-area span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range');
					} else {

							//startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
							endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
						//	$("#startDate").val(startDate);
							$("#endDate").val(endDate);
							// $('#daterange-btn span').text(startDate + '-' + endDate );
							if(df == 'single') {
								price_calculation('', '', '');
							}
					}
			});
	}
}


function formDate (startDate, endDate) {
	var customFormat =	customDaterangeFormat();
	var init = moment(0);
	var initdate;
	initdate =  moment(init, customFormat.showDateFormat).format(customFormat.dateSep);
	var today = moment();
	today =  moment(today, customFormat.showDateFormat).format(customFormat.dateSep);



	if(startDate == undefined || !startDate){
		var startDate = moment();
		startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
		var endDate   = moment();
		endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
	} else {
		startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
		endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
	}

	if (startDate == 'undefined' || endDate == 'undefined') {
		$('#daterange-btn span').html('Pick a date range');
	} else if (startDate == '' || endDate == '' || (startDate === initdate && endDate === today )) {
		$('#daterange-btn span').html('Anytime');

	} else {
			startDate = moment(startDate, customFormat.showDateFormat).format(customFormat.dateSep);
			endDate = moment(endDate, customFormat.showDateFormat).format(customFormat.dateSep);
			$("#startDate").val(startDate);
			$("#endDate").val(endDate);
			$('#daterange-btn span').text(startDate + '-' + endDate );
	}
}
