@extends('template')

@section('main')
<div class="container-fluid container-fluid-90 margin-top-85 min-height">
	@if(Session::has('message'))
		<div class="row mt-5">
			<div class="col-md-12 text-13 alert mb-0 {{ Session::get('alert-class') }} alert-dismissable fade in  text-center opacity-1">
				<a href="#"  class="close " data-dismiss="alert" aria-label="close">&times;</a>
				{{ Session::get('message') }}
			</div>
		</div>
	@endif

	<div class="row justify-content-center">
		<div class="col-md-8 mb-5 mt-3 main-panel p-5 border rounded">
		    @if(Auth::user()->verified > 0)
    			<form action="{{ url('payments/create_booking') }}" method="post" id="checkout-form">
    				{{ csrf_field() }}
    				<div class="row justify-content-center">
    				<input name="property_id" type="hidden" value="{{ $property_id }}">
    				<input name="checkin" type="hidden" value="{{ $checkin }}">
    				<input name="checkout" type="hidden" value="{{ $checkout }}">
    				<input name="number_of_guests" type="hidden" value="{{ $number_of_guests }}">
    				<input name="nights" type="hidden" value="{{ $nights }}">
    				<input name="currency" type="hidden" value="{{ $result->property_price->code }}">
    				<input name="booking_id" type="hidden" value="{{ $booking_id }}">
    				<input name="booking_type" type="hidden" value="{{ $booking_type }}">
    
    				@if($status == "" && $booking_type == "request")
    					<div class="h2 pb-4 m-0 text-24">{{ trans('messages.listing_book.request_message') }}</div>
    				@endif
    				@if($booking_type == "instant"|| $status == "Processing" )
    					<div class="col-md-12 p-0">
    						<label for="exampleInputEmail1">{{ trans('messages.payment.country') }}</label>
    					</div>
    
    					<div class="col-sm-12 p-0 pb-3">
    						<select name="payment_country" id="country-select" data-saving="basics1" class="form-control mb20">
    							@foreach($country as $key => $value)
    							<option value="{{ $key }}" {{ ($key == $default_country) ? 'selected' : '' }}>{{ $value }}</option>
    							@endforeach
    						</select>
    					</div>
    					<div class="col-sm-12 p-0">
    						<label for="exampleInputEmail1">{{ trans('messages.payment.payment_type') }}</label>
    					</div>
    
    					<div class="col-sm-12 p-0 pb-3">
    						<select name="payment_method" class="form-control mb20" id="payment-method-select">
    							@if($paypal_status->value == 1)
    								<option value="paypal" data-payment-type="payment-method" data-cc-type="visa" data-cc-name="" data-cc-expire="">
    								{{ trans('messages.payment.paypal') }}
    								</option>
    							@endif
    
    							@if($stripe_status->value == 1)
    								<option value="stripe" data-payment-type="payment-method" data-cc-type="visa" data-cc-name="" data-cc-expire="">
    								{{ trans('messages.payment.stripe') }}
    								</option>
                                @endif
    
                                @if($banks >= 1)
                                    <option value="bank" data-payment-type="payment-method" data-cc-type="bank" data-cc-name="" data-cc-expire="">
                                        {{ trans('messages.payment.bank') }}
                                    </option>
                                @endif
    
    							@if(!$paypal_status->value == 1 && !$stripe_status->value == 1 && !$banks >= 1)
    								<option value="">
    								{{ trans('messages.payment.disable') }}
    								</option>
    							@endif
    						</select>
    						<div class="paypal-div {{$paypal_status->value != 1 ? 'display-off' : ''}}">
    							<span id='paypal-text'>{{ trans('messages.payment.redirect_to_paypal') }}</span>
    						</div>
    
    					</div>
    
    				@endif
    
    					<div class="col-sm-12 p-0">
    						<label for="message">Message (optional)</label>
    					</div>
    
    					<div class="col-sm-12 p-0 pb-3">
    						<textarea name="message_to_host" placeholder="{{ trans('messages.trips_active.type_message') }}" class="form-control mb20" rows="7"></textarea>
    					</div>
    
    
    					<div class="col-sm-12 p-0 text-right mt-4">
    						<button id="payment-form-submit" type="submit" class="btn vbtn-outline-success text-16 font-weight-700 pl-5 pr-5 pt-3 pb-3">
    							<i class="spinner fa fa-spinner fa-spin d-none"></i>
    							{{ ($booking_type == 'instant') ? trans('messages.listing_book.book_now') : trans('messages.property.continue') }}
    						</button>
    					</div>
    				</div>
    			</form>
			@else
			    <div class="col-md-12">
					<div class="card card-default p-3 mt-3">
						<div class="card-body">
							<p class="text-center m-0"><i class="fas fa-exclamation-triangle mr-2 text-16 align-middle badge-warning rounded-circle p-3 vbadge-error"></i>To make a listing, you have to verify your identity in your profile. Thank you!</p>
							<div class="w-100 d-flex justify-content-center mt-3">
							    <a href="{{ url('users/edit-verification') }}" class="small text-success">
							        Click here
							    </a>
							</div>
							
						</div>
					</div>
				</div>
			@endif
		</div>
		<div class="col-md-4  mt-3 mb-5">
				<div class="card p-3">
					<a href="{{ url('/') }}/properties/{{ $result->slug}}">
						<img class="card-img-top p-2 rounded" src="{{ $result->cover_photo }}" alt="{{ $result->name }}" height="180px">
					</a>

					<div class="card-body p-2">
						<a href="{{ url('/') }}/properties/{{ $result->slug}}">
							<p class="text-16 font-weight-700 mb-0">{{ $result->name }}</p>
						</a>

						<p class="text-14 mt-2 text-muted mb-0">
							<i class="fas fa-map-marker-alt"></i>
							{{$result->property_address->address_line_1}}, {{ $result->property_address->state }}, {{ $result->property_address->country_name }}
						</p>
						<div class="border p-4 mt-4 text-center rounded-3">
							<p class="text-16 mb-0">
								<strong class="font-weight-700 secondary-text-color">{{ $result->property_type_name }}</strong>
								{{trans('messages.payment.for')}}
								<strong class="font-weight-700 secondary-text-color">{{ $number_of_guests }} {{trans('messages.payment.guest')}}</strong>
							</p>
							<div class="text-16"><strong>{{ date('D, M d, Y', strtotime($checkin)) }}</strong> to <strong>{{ date('D, M d, Y', strtotime($checkout)) }}</strong></div>
						</div>

						<div class="border p-4 rounded-3 mt-4">
						    <p class="text-16 font-weight-700 mb-4 px-4">Payment Dates</p>
						    @foreach( $price_list->weeks_with_dates as $date_price)
    							<div class="d-flex justify-content-between text-16">
    								<div>
    									<p class="pl-4">{{ $date_price->date }}</p>
    								</div>
    								<div>
    									<p class="pr-4">${!! $date_price->price !!}</p>
    								</div>
    							</div>
							@endforeach

							<!-- @foreach( $price_list->date_with_price as $date_price)
							<div class="d-flex justify-content-between text-16">
								<div>
									<p class="pl-4">{{ $date_price->date }}</p>
								</div>
								<div>
									<p class="pr-4">{!! $date_price->price !!}</p>
								</div>
							</div>
							@endforeach -->
							<hr>
							<div class="d-flex justify-content-between text-16">
								<div>
									<p class="pl-4">{{trans('messages.payment.week')}}</p>
								</div>
								<div>
									<p class="pr-4">{{ $price_list->total_weeks }}</p>
								</div>
							</div>

							<div class="d-flex justify-content-between text-16">
								<div>
									<p class="pl-4">{!! $price_list->per_week_price_with_symbol !!} x {{ $price_list->total_weeks }} {{trans('messages.payment.weeks')}}</p>
								</div>
								<div>
									<p class="pr-4">{!! $price_list->week_data_total_price_with_symbol !!}</p>
								</div>
							</div>
							
							@if($price_list->week_data_discount)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4">{{trans('messages.payment.discount')}}</p>
									</div>

									<div>
										<p class="pr-4">- {!! $price_list->week_data_discount_with_symbol !!}</p>
									</div>
								</div>
							@endif

							@if($price_list->week_data_service_fee)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4">{{trans('messages.payment.service_fee')}}</p>
									</div>

									<div>
										<p class="pr-4">{!! $price_list->week_data_service_fee_with_symbol !!}</p>
									</div>
								</div>
							@endif

							@if($price_list->week_data_additional_guest)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4">{{trans('messages.payment.additional_guest_fee')}}</p>
									</div>

									<div>
										<p class="pr-4">{!! $price_list->week_data_additional_guest_fee_with_symbol !!}</p>
									</div>
								</div>
							@endif


							@if($price_list->cleaning_fee)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4">{{trans('messages.payment.cleaning_fee')}}</p>
									</div>

									<div>
										<p class="pr-4">{!! $price_list->cleaning_fee_with_symbol !!}</p>
									</div>
								</div>
							@endif

							@if($price_list->iva_tax)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4">{{trans('messages.property_single.iva_tax')}}</p>
									</div>

									<div>
										<p class="pr-4">{!! $price_list->iva_tax_with_symbol !!}</p>
									</div>
								</div>
							@endif

							@if($price_list->week_data_accomodation_tax)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4">{{trans('messages.property_single.accommodatiton_tax')}}</p>
									</div>

									<div>
										<p class="pr-4">{!! $price_list->week_data_accomodation_tax_with_symbol !!}</p>
									</div>
								</div>
							@endif
							
							@if($price_list->damages_insurance)
								<div class="d-flex justify-content-between text-16">
									<div>
										<p class="pl-4 mb-0">{{trans('messages.payment.damages_insurance')}}</p>
										<p class="pl-4 text-muted" style="line-height: 10px;"><small>non-refundable charge</small></p>
									</div>

									<div>
										<p class="pr-4">{!! $price_list->damages_insurance_with_symbol !!}</p>
									</div>
								</div>
							@endif
							<hr>

							<div class="d-flex justify-content-between font-weight-700">
								<div>
									<p class="pl-4">Due Now</p>
								</div>

								<div>
									<p class="pr-4">{!! $price_list->weekly_total_price_with_symbol !!}</p>
								</div>
							</div>
							<hr>

							<div class="d-flex justify-content-between font-weight-700">
								<div>
									<p class="pl-4">{{trans('messages.payment.total')}}</p>
								</div>

								<div>
									<p class="pr-4">{!! $price_list->week_data_total_with_symbol !!}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<p class="exfont text-14">
							{{trans('messages.payment.paying_in')}}
							<strong><span id="payment-currency">{!! moneyFormat($currencyDefault->org_symbol,$currencyDefault->code) !!}</span></strong>.
							{{trans('messages.payment.your_total_charge')}}
							<strong><span id="payment-total-charge">{!! $price_list->week_data_total_with_symbol !!}</span></strong>.
							{{trans('messages.payment.exchange_rate_booking')}} {!! moneyFormat($currentCurrency->symbol, 1) !!} {!! $currentCurrency->code !!} to {!! moneyFormat($result->property_price->currency->org_symbol, $price_rate ) !!} {{ $result->property_price->currency_code }} ( {{trans('messages.listing_book.host_currency')}} ).
						</p>
					</div>
				</div>


		</div>
	</div>
</div>
@push('scripts')
<script type="text/javascript" src="{{ url('public/js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
$('#payment-method-select').on('change', function(){
  var payment = $(this).val();
  if(payment !== 'paypal'){
      $('.paypal-div').addClass('display-off')
  }
  else {
      $('.paypal-div').removeClass('display-off')
  }
});

$(document).ready(function() {
    $('#checkout-form').validate({
        submitHandler: function(form)
        {
 			$("#payment-form-submit").on("click", function (e)
            {
            	$("#payment-form-submit").attr("disabled", true);
                e.preventDefault();
            });


            $(".spinner").removeClass('d-none');
            $("#save_btn-text").text("{{trans('messages.users_profile.save')}} ..");
            return true;
        }
    });
});


$('#country-select').on('change', function() {
  var country = $(this).find('option:selected').text();
  $('#country-name-set').html(country);
})
</script>
@endpush
@stop
