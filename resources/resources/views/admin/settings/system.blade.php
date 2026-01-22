@extends('admin.template')
@push('css')
<link href="{{ asset('backend/css/preferences.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('main')

  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-3 settings_bar_gap">
          @include('admin.common.settings_bar')
        </div>
        <!-- right column -->
        <div class="col-md-9">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">System Information</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
              <div class="form-group">
                <label class="col-sm-3 control-label">PHP Version:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $php_version }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Laravel Version:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $laravel_version }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Server Software:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $server_software }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">PHP OS:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $php_os }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Server Name:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $server_name }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Document Root:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $document_root }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">App Environment:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $app_env }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">App Debug:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $app_debug }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Database Connection:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $db_connection }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Cache Driver:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $cache_driver }}</p>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Session Driver:</label>
                <div class="col-sm-9">
                  <p class="form-control-static">{{ $session_driver }}</p>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <a class="btn btn-default" href="{{ url('admin/settings') }}">Back to Settings</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->

          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>

@endsection




