@extends('app')

@section('content')
<style>
.release-notes-header {
  background: #154682;
}
.r-close {
  color: #FFFFFF;
}
.r-title {
  color: #FFFFFF;
}
.r-notes-content {
  max-height: 600px;
  overflow-y: scroll;
}
</style>
<script type="text/javascript" src="{{ asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>tinymce.init({ selector:'textarea', height: 500, menubar: false });</script>

<div class="col-md-10">
  <div class="row">
    <div class="col-md-12" style="padding-bottom: 10px;">
      <textarea class="description">Type the discription of the release notes here...</textarea>
    </div>
    <div class="col-md-12">
      <div class="col-md-6">
        <button class="btn btn-block btn-success save-r-notes">Save</button>
      </div>
      <div class="col-md-6">
        <button class="btn btn-block btn-warning preview-r-notes" type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Preview</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header release-notes-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="r-close">&times;</span></button>
        <h4 class="modal-title r-title" id="myModalLabel">Software Updates</h4>
      </div>
      <div class="modal-body r-notes-content">
        ...
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-save-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content modal-sm">
      <div class="modal-header release-notes-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="r-close">&times;</span></button>
        <h4 class="modal-title r-title" id="myModalLabel"></h4><br/>
      </div>
      <div class="modal-body save-notes-content text-center">
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(){

  $(".save-r-notes").click(function(){
    tinyMCE.triggerSave();
    var description = $(".description").val();
    if(description == "<p>Type the discription of the release notes here...</p>"){
      $(".save-notes-content").html("");
      $(".save-notes-content").html("Please type the description of the release notes");
      $(".bs-save-modal-lg").modal("show");
      return false;
    }
    $(".r-notes-content").html();
    $(".r-notes-content").html($(".description").val());

    $.ajax({url	:	app_url + '/savereleasenotes',
            data : {description:description},
        		type	:	'post',
        		success: function(data){
              var text = "";
              if(data == "success"){
                text = "Release notes saved!";
              }else {
                text = "Something went wrong, release notes not save.";
              }
              $(".save-notes-content").html("");
              $(".save-notes-content").html(text);
              $(".bs-save-modal-lg").modal("show");
        		}
    })

  });

  $('.bs-example-modal-lg').on('show.bs.modal', function () {
    tinyMCE.triggerSave();
    $(".r-notes-content").html();
    $(".r-notes-content").html($(".description").val());
  })

  $(".preview-r-notes").click(function(){
    $(".r-notes-content").html();
    $(".r-notes-content").html($(".description").val());
  })
});
</script>
@stop
