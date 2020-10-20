/*
*	Main directive for the handapp
*/

app.directive('datepicker', function(){
	return {
		restrict:"A",
		link:function(scope,el,attr){
		  scope.$watch('timepicker', function(){
			el.datepicker();  
		  })
		}
	  };
});

app.directive('timepicker', function(){
	return {
		restrict:"A",
		link:function(scope,el,attr){
		  scope.$watch('timepicker', function(){
			el.timepicker({
				hourMin: 0,
				hourMax: 11,
				controlType: 'select',
				oneLine: true,
				timeFormat: 'hh:mm',
				minDate: new Date(2080, 1, 1)	
			});  
		  })
		}
	};	
});

app.directive('loadMe', function(){
	return {
		restrict:"A",
		link: function(scope, element, attr){
				element.bind('click', function(){
					alert("testing");
				})
			}
		}	
});