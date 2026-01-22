@extends('template')
@section('main')
<?php
    //$property_cancellation_policies
?>
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
                            @foreach($property_cancellation_policies as $policy)
                                <div class="col-md-12 border mt-4 pb-5 rounded-3 pl-sm-0 pr-sm-0">
                                    <div class="d-flex justify-content-between form-group col-md-12 main-panelbg pb-3 pt-3 mt-sm-0 ">
                                        <h4 class="text-18 font-weight-700 pl-3">{{ $policy->title }}</h4>
                                        <div>
                                            <input type="radio" name="cancellation_policy" {{ ($result->property_cancellation_policy_id==$policy->id) ? "checked" : "" }} value="{{ $policy->id }}">
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-md-12 pl-5 pr-5">
                                            <h5>{{ $policy->description }}</h5>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                                
                           

							<div class="col-md-12 p-0 mt-4 mb-5">
								<div class="row m-0 justify-content-between">
									<div class="mt-4">
										<a  href="{{ url('listing/' . $result->id . '/booking') }}" class="btn btn-outline-danger secondary-text-color-hover text-16 font-weight-700  pt-3 pb-3 pl-5 pr-5">
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
    let page = 'cancellation_policy';
</script>
<script type="text/javascript" src="{{ asset('public/js/listings.min.js') }}"></script>

@endsection
