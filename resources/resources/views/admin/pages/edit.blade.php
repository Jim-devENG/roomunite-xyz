@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Edit Page Form
      <small>edit page form</small>
    </h1>
    @include('admin.common.breadcrumb')
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- right column -->
      <div class="col-md-12">
        <div id="successMessage"></div>
        <!-- Horizontal Form -->
        <div class="box">
          <!-- /.box-header -->
          <div class="box-header with-border">
            <h3 class="box-title">Edit Page Form</h3>
          </div>
          <!-- form start -->
          <form class="form-horizontal" id="edit_page" action="{{url('admin/edit-page/'.$result->id)}}" method="post" name="edit_page" accept-charset='UTF-8' {{-- onsubmit="return contentValidate();" --}}>
            {{ csrf_field() }}

            <div class="box-body">

              <input type="hidden" id="pageId" value="{{$result->id}}">
              <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

              <div class="form-group">
               <label for="exampleInputPassword1" class="control-label col-sm-3">Name<span class="text-danger">*</span></label>
               <div class="col-sm-6">
                 <input type="text" class="form-control pageName" value="{{$result->name}}" name="name" placeholder="" id="geturl">
               </div>
             </div>
             <div class="form-group">
               <label for="exampleInputPassword1" class="control-label col-sm-3">URL<span class="text-danger">*</span></label>
               <div class="col-sm-6">
                 <input type="text" class="form-control pageUrl" name="url" id="page_url" placeholder="" value="{{$result->url}}">
               </div>
             </div>
            
             <div class="form-group">
               <label for="inputEmail3" class="col-sm-3 control-label">Content<span class="text-danger">*</span></label>  
               <div class="col-sm-6">
                 <textarea id="content" name="content" placeholder="" rows="10" cols="80" class="">{{$result->content}} </textarea>
                 <span id="content-validation-error"></span>
               </div>
             </div>

             <div class="form-group">
              <label for="exampleInputPassword1" class="control-label col-sm-3">Position</label>
               <div class="col-sm-6">
                 <select name="position" class="form-control pagePosition">
                   <option value="first" {{ ($result->position) == 'first' ? 'selected': ""}}> First Column </option>
                   <option value="second" {{ ($result->position) == 'second' ? 'selected': ""}}> Second Column </option>
                   <option value="third" {{ ($result->position) == 'third' ? 'selected': ""}}> Third Column </option>
                   <option value="fourth" {{ ($result->position) == 'fourth' ? 'selected': ""}}> Fourth Column </option>
                 </select>
               </div>
             </div>
              <div class="form-group">
              <label for="exampleInputPassword1" class="control-label col-sm-3">Status</label>
               <div class="col-sm-6">
                 <select name="status" class="form-control pageStatus">
                   <option value="Active" {{ ($result->status) == 'Active' ? 'selected': ""}}> Active </option>
                   <option value="Inactive" {{ ($result->status) == 'Inactive' ? 'selected': ""}}>Inactive</option>
                 </select>
               </div>
              </div>

              <textarea style="display: none;" id="hiddenContent" name="hiddenContent"></textarea>

           </div>
           <!-- /.box-body -->
           <div class="box-footer">
            <button type="submit" class="btn btn-info" id="submitBtn">Submit</button>
            <a href="{{url('admin/pages')}}" class="btn btn-danger btn-sm">
              Cancel
            </a>
           
          </div>
          <!-- /.box-footer -->
        </form>
      </div>
      <!-- /.box -->

      <!-- /.box -->
    </div>
    <!--/.col (right) -->
  </div>
</section>
</div>
@endsection
@push('scripts')
<script 
{{-- src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js" --}}
></script>
<script src="//cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script> <script> CKEDITOR.replace('content'); </script>
 
<script>
  // window.onload = function() {
  //   CKEDITOR.replace( 'content', {
  //     filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
  //     filebrowserUploadMethod: 'form'

  //   });
  // };
</script>

<script>

//   $(document).ready(function() {
//    $(document).on('submit', 'form', function() {
//      $('button').attr('disabled', 'disabled');
//    });
//  });
</script>

<script>
   $(document).ready(function() {
   $('#geturl').on('blur keyup', function() {
     var pagUrl = $('#geturl').val();
         pagUrl = pagUrl.toLowerCase();
         pagUrl = pagUrl.replace(/[^a-zA-Z0-9]+/g,'-');
       if (pagUrl !='') {

      $('#page_url').val(pagUrl);
    } 

  });
 }); 
</script>

<script type="text/javascript">
//
//  $(document).ready(function () {
//   $('#edit_page').validate({
//     ignore: [],
//     rules: {
//       name: {
//         required: true
//       },
//       url:{
//         required:true
//       },
//         content: {

//           required: function(textarea) {
//             CKEDITOR.instances[textarea.id].updateElement(); // update textarea
//             console.log('textarea: ', textarea.value);
//             // var editorcontent = textarea.value.replace(/<[^>]*>/gi, ''); // strip tags
//             return editorcontent.length === 0;
//           }
//         }
//      },
//               errorPlacement: function (error, element) {
//                 if (element.prop('type') === 'textarea') {
//                   $('#content-validation-error').html(error);
//                 } else {
//                   error.insertAfter(element);
//                 }
//               }
//       });
// });
</script>

<script>
  $(document).ready(function() {
      // CKEDITOR.replace('content', {
      //     filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
      //     filebrowserUploadMethod: 'form'
      // });
  
      $('#edit_page').submit(function(event) {
          // Prevent default form submission
          event.preventDefault();
  
          var pageName = $('.pageName').val()
          var pageUrl = $('.pageUrl').val()
          var pagePosition = $('.pagePosition').val()
          var pageStatus = $('.pageStatus').val()
          // Grab the CKEditor content
          var content = CKEDITOR.instances['content'].getData();
          // console.log('content2: ', content);
  
          // Set the CKEditor content in the hidden input field
          // var hiddenContent = $('#hiddenContent').val(content);

          // Define the route name with the ID
          var routeNameWithId = '{{ route('adminEditPageAjax', ['id' => $result->id]) }}'; // Replace $id with the actual ID value
    
           // console.log("AJAX URL:" + adminEditPageAjax);
          //  console.log("AJAX ROUTE:" + routeNameWithId);

          // AJAX request to submit the form data
          $.ajax({
              url: routeNameWithId,
              method: 'POST',
              data: {
                  // Include other form fields here if needed
                  pageName: pageName,
                  pageUrl: pageUrl,
                  pagePosition: pagePosition,
                  pageStatus: pageStatus,
                  content: content,
                  _token: '{{ csrf_token() }}'
              },
              success: function(response) {
                  // Handle success response
                  console.log(response);

                  // Scroll to the top of the screen
                  $('html, body').animate({ scrollTop: 0 }, 'fast');

                  // Refresh the window after a short delay
                  // setTimeout(function() {
                  //     location.reload();
                  // }, 2000); // Refresh after 2 seconds (adjust the delay as needed)

                  // Display a success message dynamically
                  var successMsg = '<div class="alert alert-success" role="alert">Your changes have been saved successfully!</div>';
                  $('#successMessage').html(successMsg);

                  // Redirect to another page after a short delay
                  // setTimeout(function() {
                  //     window.location.href = '/admin/pages'; // Replace '/another-page' with the URL of the page you want to redirect to
                  // }, 2000); // Redirect after 2 seconds (adjust the delay as needed)
              },
              error: function(xhr, status, error) {
                  // Handle error
                  console.error(error);
              }
          });

          // alert(hiddenContent)
  
          // Submit the form
          // $(this).unbind('submit').submit();
      });
  });
</script>


  
@endpush