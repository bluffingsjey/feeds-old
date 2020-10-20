@extends('app')


@section('content')

<div class="col-md-10 farm-profile-holder">
<h3>Farms Profile  <span><a href="/farms/create" class="btn btn-sm btn-success pull-right">Add farm</a></span></h3>

</div>

<script type="text/javascript">

$(document).ready(function(){

  var farms_profile = {!!$farms_profile!!};
  var farms_elements = "";
  $.each(farms_profile, function(key,val){

      farms_elements += "<div class='row'>";
      farms_elements += "<div class='col-md-12'>";
      farms_elements += "<div class='panel panel-info'>";
      farms_elements += "<div class='panel-heading'><strong>Farms Info</strong>";
      farms_elements += "<span class='pull-right'><a href='addfarmuser/"+val['id']+"' class='btn btn-xs btn-info'>+ Add User</a></span>";
      farms_elements += "</div>";
      farms_elements += "<div class='panel-body'>";

      farms_elements += "<div class='col-md-4'>";
      farms_elements += "<dl class='dl-horizontal'>";
      farms_elements += "<dt>Farm Name:</dt>";
      farms_elements += "<dd>"+val['name']+"</dd>";
      farms_elements += "</dl>";
      farms_elements += "<dl class='dl-horizontal'>";
      farms_elements += "<dt>Delivery Time:</dt>";
      farms_elements += "<dd>"+val['delivery_time']+" Hour/s</dd>";
      farms_elements += "</dl>";

      $.each(val['users'], function(k,v){
            farms_elements += "<dl class='dl-horizontal user-list-"+v['user_id']+"-"+val['id']+"'>";
            farms_elements += "<dt>Username:</dt>";
            farms_elements += "<dd>"+v['username']+"</dd>";
            farms_elements += "<dt>Password:</dt>";
            farms_elements += "<dd>"+v['username']+"</dd>";
            farms_elements += "<dd><button type='button' id='"+v['user_id']+"' farm_id='"+val['id']+"' class='btn btn-xs btn-danger btn-rm-"+v['user_id']+"-"+val['id']+"' style='margin-top: 5px;'>remove user</button></dd>";
            farms_elements += "</dl>";

            $(".container").delegate(".btn-rm-"+v['user_id']+"-"+val['id'],"click", function(e) {
                    user_id = $(this).attr("id");
                    farm_id = $(this).attr("farm_id");
                    removeUser(user_id,farm_id);
            });

      });

      farms_elements += "</div>";
      farms_elements += "<div class='col-md-8'>";
      farms_elements += "<div class='table-responsive'>";
      farms_elements += "<table class='table table-bordered'>";
      farms_elements += "<thead>";
      farms_elements += "<tr>";
      farms_elements += "<th>Bin Number</th>";
      farms_elements += "<th>Bin Alias</th>";
      farms_elements += "<th>Size</th>";
      farms_elements += "<th>Feed Type</th>";
      farms_elements += "<th>Pigs</th>";
      farms_elements += "</tr>";
      farms_elements += "</thead>";
      farms_elements += "<tbody>";

      $.each(val['bins'], function(k,v){
        var num_of_pigs = 0;
        if(v['number_of_pigs'] == null){
          num_of_pigs = 0;
        } else {
          num_of_pigs = v['number_of_pigs'];
        }
            farms_elements += "<tr>";
            farms_elements += "<td>"+v['bin_number']+"</td>";
            farms_elements += "<td>"+v['alias']+"</td>";
            farms_elements += "<td>"+v['bin_size']+" Tons</td>";
            farms_elements += "<td>"+v['feed_type']+"</td>";
            farms_elements += "<td>"+num_of_pigs+"</td>";
            farms_elements += "</tr>";
      });

      farms_elements += "</tbody>";
      farms_elements += "</table>";
      farms_elements += "</div>";
      farms_elements += "</div>";

      farms_elements += "</div>";
      farms_elements += "</div>";

      farms_elements += "</div>";
      farms_elements += "</div>";

  });


  console.log(farms_elements);

  function removeUser(user_id,farm_id){
    $.ajax({
      url		:	app_url+"/removefarmer",
      data	:	{'user_id': user_id,'farm_id':farm_id},
      type    : 	"POST",
      success: function(r){
        $(".user-list-"+user_id+"-"+farm_id).html("");
      }
    });
  }

  $('.farm-profile-holder').append(farms_elements);

});



</script>

@stop
