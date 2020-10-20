<table class="table table-hover">
 <tr>
 <th>Nursery Group</th>
 <th>Farm</th>
 <th>Pigs</th>
 <th></th>
 </tr>
@forelse($pending_data as $k => $v)
<input name="farrowing_group[]" type="hidden" value="{{$v['group_id']}}"/>
<input name="number_of_pigs[]" type="hidden" value="{{$v['number_of_pigs']}}"/>
<tr class="farrow-groups farrow-{{$v['id']}}">
 <td>{{$v['group_name']}}</td>
 <td>{{$v['farm_name']}}</td>
 <td>{{$v['number_of_pigs']}}</td>
 <td>
 	<button type="button" class="btn btn-default btn-xs glyphicon glyphicon-remove btn-danger pull-right" aria-label="Left Align" id="{{$v['id']}}" title="Remove this group"></button>	
 </td>
</tr>
@empty

@endforelse
</table>