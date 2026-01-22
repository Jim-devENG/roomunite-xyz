@extends('template')
@section('main')
<div class="margin-top-85">
	<div class="row m-0">
		<!-- sidebar start-->
		@include('users.sidebar')
		<!--sidebar end-->
		<div class="col-md-10">
			<div class="main-panel min-height mt-4">
				<div class="row justify-content-center">
					<div class="col-md-3 pl-4 pr-4">
						@include('listing.sidebar')
					</div>

					<div class="col-md-9 mt-4 mt-sm-0 pl-4 pr-4">
						<form method="post" id="list_des" action="{{ url('listing/' . $result->id . '/' . $step) }}"  accept-charset='UTF-8'>
							{{ csrf_field() }}
							<div class="col-md-12 border mt-4 pb-5 rounded-3 pl-sm-0 pr-sm-0 ">
								<div class="form-group col-md-12 main-panelbg pb-3 pt-3 mt-sm-0 ">
									<h4 class="text-18 font-weight-700 pl-3">External Link</h4>
								</div>

								<div class="row mt-4">
									<div class="col-md-12 pl-5 pr-5">
										<label>External Link Title <span class="text-danger"></span></label>
										<input type="" name="external_link_title" id="external_link_title" class="form-control text-16 mt-2" value="{{ old('external_link_title', $result->external_link_title)  }}" placeholder="" maxlength="1000">
										<span class="text-danger">{{ $errors->first('external_link_title') }}</span>
									</div>
								</div>

								<div class="row mt-4">
									<div class="col-md-12 pl-5 pr-5">
										<label>External Link Url <span class="text-danger"></span></label>
										<textarea class="form-control text-16 mt-2" name="external_link_url" rows="6" placeholder=""  ng-model="external_link_url">{{ old('external_link_url', $result->external_link_url)  }} </textarea>
										<span class="text-danger">{{ $errors->first('external_link_url') }}</span>
									</div>
								</div>

							</div>

							<div class="col-md-12 p-0 mt-4 mb-5">
								<div class="row m-0 justify-content-between">
									<div class="mt-4">
										<a  href="{{ url('listing/' . $result->id . '/location') }}" class="btn btn-outline-danger secondary-text-color-hover text-16 font-weight-700  pt-3 pb-3 pl-5 pr-5">
											{{ __('Back') }}
										</a>
									</div>

									<div class="mt-4">
										<button type="submit" class="btn vbtn-outline-success text-16 font-weight-700 pl-5 pr-5 pt-3 pb-3 pl-5 pr-5" id="btn_next"><i class="spinner fa fa-spinner fa-spin d-none" ></i>
											<span id="btn_next-text">{{ __('Next') }}</span>
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('validation_script')
<script type="text/javascript" src="{{ asset('public/js/jquery.validate.min.js') }}"></script>

	<script type="text/javascript">
		'use strict'
		let nextText = "{{ __('Next') }}..";
		let fieldRequiredText = "{{ __('This field is required.') }}";
		let maxlengthText = "{{ __('Please enter no more than 500 characters.') }}";
		let page = 'description';
	</script>
	<script type="text/javascript" src="{{ asset('public/js/listings.min.js') }}"></script>

@endsection
