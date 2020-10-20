<script type="text/javascript">
$(document).ready(function(e) {
    
	loadingDrivers();
	
	var date_reduced = new Date();
	date_reduced.setDate(date_reduced.getDate()-14);
	$("#date_from").datepicker({dateFormat:"mm/dd/y"}).datepicker("setDate",date_reduced);
	$("#date_to").datepicker({dateFormat:"mm/dd/y"}).datepicker("setDate",new Date());
	$( function() {
		var dateFormat = "mm/dd/y",
		  from = $( "#date_from" )
			.datepicker({
			  defaultDate: "+1w",
			  changeMonth: true,
			  numberOfMonths: 1
			})
			.on( "change", function() {
			  to.datepicker( "option", "minDate", getDate( this ) );
			}),
		  to = $( "#date_to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		  })
		  .on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
		  });
	 
		function getDate( element ) {
		  var date;
		  try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		  } catch( error ) {
			date = null;
		  }
	 
		  return date;
		}
	});
	
	/*
	*	Date from
	*/
	/*var date_reduced = new Date();
	date_reduced.setDate(date_reduced.getDate()-14);
	$("#date_from").datepicker({dateFormat:"dd/mm/y"}).datepicker("setDate",date_reduced);
	$("#date_from").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'M d',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});*/
	
	/*
	*	Date to
	*/
	/*$("#date_to").datepicker({dateFormat:"dd/mm/y"}).datepicker("setDate",new Date());
	$("#date_to").datepicker({
		controlType: 'select',
		oneLine: true,
		dateFormat: 'M d',
		//comment the beforeShow handler if you want to see the ugly overlay
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});*/
	
	
	/*
	*	loading 
	*/
	function loadingDrivers(){
		
		setTimeout(function(){
			$(".loading-stick-circle").fadeOut(function(){
				$(".table_holder").fadeIn();	
				ajaxSearch();
			})	
		},500);
				
	}
	
	/*
	*	search
	*/
	$(".btn-search").click(function(){
		
		$(".table_holder").fadeOut(function(){
			$(".loading-stick-circle").fadeIn();
		});
		
		date_from = $("#date_from").val();
		date_to = $("#date_to").val();
		
		data = {
				'date_from'	:	date_from,
				'date_to'	:	date_to
			};
		
		console.log(date_from +" "+ date_to);
		
		$.ajax({
			url 	:	app_url+'/driversearch',
			type	:	"POST",
			data	: 	data,
			success	: function(r){
				$(".loading-stick-circle").fadeOut(function(){
					$(".table_holder").fadeIn(function(){
						$(this).html(r);
					});
				})		
			}
		});
	})
	
	// ajax search
	function ajaxSearch(){
		
		date_from = $("#date_from").val();
		date_to = $("#date_to").val();
		
		data = {
				'date_from'	:	date_from,
				'date_to'	:	date_to
			};
		
		console.log(date_from +" "+ date_to);
		
		$.ajax({
			url 	:	app_url+'/driversearch',
			type	:	"POST",
			data	: 	data,
			success	: function(r){
				$(".loading-stick-circle").fadeOut(function(){
					$(".table_holder").html(r);	
				})		
			}
		});
			
	}
	
	/*
	*	Sorting
	*/
	/*$(".container").delegate('.sorting','click', function(){
		
		var data = {
				'type'		:	$(this).attr("type"),
				'sorting'	:	$(this).attr("sort"),
				'date_from'	:	$("#date_from").val(),
				'date_to'	:	$("#date_to").val()
			}
			
		$(".sorting").removeClass("sorting-active");
		
		$(".table_holder").fadeOut(function(){
			$(".loading-stick-circle").fadeIn();
		});
		
		$.ajax({
				url		:	app_url + "/sorting",
				data	: 	data,
				type	: 	"POST",
				success	: function(r){
					$(".loading-stick-circle").fadeOut(function(){
						$(".table_holder").fadeIn(function(){
							$(this).html(r);
							$("." +data['type'] +"-"+ data['sorting']).addClass("sorting-active");
						});
					})
				}
		});	
			
	})*/
	
	
	
});
	
</script>