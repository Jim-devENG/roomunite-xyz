@extends('template')
@push('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('css/css/daterangepicker.min.css')}}" />
    <style>
        .vbtn-outline-success:hover {
            background: #1dbf73 !important;
        }

        .btn-outline-danger:hover {
            background: #dc3545 !important;
        }
    </style>
@endpush

@section('main')
	<input type="hidden" id="front_date_format_type" value="{{ Session::get('front_date_format_type')}}">
	<section class="hero-banner magic-ball">
		<div class="main-banner"  style="background-image: url('{{ isset($banner_url) ? $banner_url : (defined("BANNER_URL") ? BANNER_URL : asset("images/default-banner.jpg")) }}') !important;">
			<div class="container">
				<div class="row align-items-center text-center text-md-left">
					<div class="col-md-6 col-lg-5 mb-5 mb-md-0">
						<div class="main_formbg item animated zoomIn mt-80">
							<h1 class="pt-4 ">{{trans('messages.home.make_your_reservation')}}</h1>
							<form id="front-search-form" method="post" action="{{url('search')}}">
								{{ csrf_field() }}
								<div class="row">
									<div class="col-md-12">
										<div class="input-group pt-4">
											<input class="form-control p-3 text-14" id="front-search-field" placeholder="{{trans('messages.home.where_want_to_go')}}" autocomplete="off" name="location" type="text" required>
										</div>
									</div>

									<div class="col-md-12 mt-5">
										<div class="d-flex" id="daterange-btn">
											<div class="input-group mr-2 pt-4" >
												<input class="form-control p-3 border-right-0 border text-14 checkinout" name="checkin" id="startDate" type="text" placeholder="{{trans('messages.search.check_in')}}" autocomplete="off" readonly="readonly" required>
												<span class="input-group-append">
													<div class="input-group-text">
														<i class="fa fa-calendar success-text text-14"></i>
													</div>
												</span>
											</div>

											<div class="input-group ml-2 pt-4">
												<input class="form-control p-3 border-right-0 border text-14 checkinout" name="checkout" id="endDate" placeholder="{{trans('messages.search.check_out')}}" type="text" readonly="readonly" required>
												<span class="input-group-append">
													<div class="input-group-text">
													<i class="fa fa-calendar success-text text-14"></i>
													</div>
												</span>
											</div>
										</div>
									</div>

									<div class="col-md-6 mt-5 pt-4">
										<div class="input-group">
											<select id="front-search-guests" class="form-control  text-14">
											<option class="p-4 text-14" value="1">1 {{trans('messages.home.guest')}}</option>
											@for($i=2;$i<=16;$i++)
												<option  class="p-4 text-14" value="{{ $i }}"> {{ ($i == '16') ? $i.'+ '.trans('messages.home.guest') : $i.' '.trans('messages.property_single.guest') }} </option>
											@endfor
											</select>
										</div>
									</div>

									<div class="col-md-12 front-search mt-5 pb-3 pt-4">
										<button type="submit" class="btn vbtn-default btn-block p-3 text-16">{{trans('messages.home.search')}}</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	@if(!$starting_cities->isEmpty())
	<section class="bg-gray mt-70 pb-2">
		<div class="container-fluid container-fluid-90">
			<div class="row">
				<div class="section-intro text-center">
					<p class="item animated fadeIn text-24 font-weight-700 m-0 text-capitalize">{{trans('messages.home.top_destination')}}</p>
					<p class="mt-3">{{trans('messages.home.destination_slogan')}} </p>
				</div>
			</div>

			<div class="row mt-2">
				@foreach($starting_cities as $city)
				<div class="col-md-4 mt-5">
				<a href="{{URL::to('/')}}/search?location={{ $city->name }}&checkin=&checkout=&guest=1">
						<div class="grid item animated zoomIn">
							<figure class="effect-ming">
								<img src="{{ $city->image_url }}" alt="city" class="lazy"/>
									<figcaption>
										<p class="text-18 font-weight-700 position-center">{{$city->name}}</p>
									</figcaption>
							</figure>
						</div>
					</a>
				</div>
				@endforeach
			</div>
		</div>
	</section>
	@endif

	@if(!$properties->isEmpty())
		<section class="recommandedbg bg-gray mt-4 magic-ball magic-ball-about pb-5">
			<div class="container-fluid container-fluid-90">
				<div class="row">
					<div class="recommandedhead section-intro text-center mt-70">
						<p class="item animated fadeIn text-24 font-weight-700 m-0">Featured Bedrooms</p>
						<p class="mt-2 d-none"></p>
					</div>
				</div>

				<div class="row mt-5">
					@foreach($properties as $property)
					<div class="col-md-6 col-lg-4 col-xl-3 pl-3 pr-3 pb-3 mt-4">
						<div class="card h-100 card-shadow card-1">
							<div class="grid">
								<a href="properties/{{ $property->slug }}" aria-label="{{ $property->name}}">
									<figure class="effect-milo">
										<img src="{{ $property->cover_photo }}" class="room-image-container200 lazy" alt="{{ $property->name}}" loading="lazy"/>
										<figcaption>
										</figcaption>
									</figure>
								</a>
							</div>

							<div class="card-body p-0 pl-1 pr-1">
								<div class="d-flex">
									<div>
										<div class="profile-img pl-2">
											@if($property->users)
												<a href="{{ url('users/show/'.$property->host_id) }}"><img src="{{ $property->users->profile_src }}" alt="{{ $property->name}}" class="img-fluid lazy"></a>
											@else
												<img src="{{ asset('images/default-profile.png') }}" alt="{{ $property->name}}" class="img-fluid lazy">
											@endif
										</div>
									</div>

									<div class="p-2 text">
										<a class="text-color text-color-hover" href="properties/{{ $property->slug }}">
											<p class="text-16 font-weight-700 text"> {{ $property->name}}</p>
										</a>
										<p class="text-13 mt-2 mb-0 text"><i class="fas fa-map-marker-alt"></i> {{$property->property_address ? $property->property_address->city : 'N/A'}}</p>
									</div>
								</div>

								<div class="review-0 p-3">
									<div class="d-flex justify-content-between">

										<div class="d-flex">
                                            <div class="d-flex align-items-center">
											<span><i class="fa fa-star text-14 secondary-text-color"></i>
												@if( $property->guest_review)
                                                    {{ $property->avg_rating }}
                                                @else
                                                    0
                                                @endif
                                                ({{ $property->guest_review }})</span>
                                            </div>

                                            <div class="">
                                                @auth
                                                    <a class="btn btn-sm book_mark_change"
                                                       data-status="{{$property->book_mark}}" data-id="{{$property->id}}"
                                                       style="color:{{($property->book_mark == true) ? '#1dbf73':''}}; ">
                                                    <span style="font-size: 22px;">
                                                        <i class="fas fa-heart pl-2"></i>
                                                    </span>
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>


										<div>
											@php
												$property_price = $property->property_price ?? null;
												$currency = $property_price ? ($property_price->currency ?? null) : null;
											@endphp
											@if($property_price && $currency)
												<span class="font-weight-700">{!! moneyFormat( $currency->symbol, $property_price->price) !!}</span> / {{trans('messages.property_single.night')}}
											@else
												<span class="font-weight-700">N/A</span>
											@endif
										</div>
									</div>
								</div>

								<div class="card-footer text-muted p-0 border-0">
									<div class="d-flex bg-white justify-content-between pl-2 pr-2 pt-2 mb-3">
										<div>
											<ul class="list-inline">
												<li class="list-inline-item  pl-4 pr-4 border rounded-3 mt-2 bg-light text-dark">
														<div class="vtooltip"> <i class="fas fa-user-friends"></i> {{ $property->accommodates }}
														<span class="vtooltiptext text-14">{{ $property->accommodates }} {{trans('messages.property_single.guest')}}</span>
													</div>
												</li>

												<li class="list-inline-item pl-4 pr-4 border rounded-3 mt-2 bg-light">
													<div class="vtooltip"> <i class="fas fa-bed"></i> {{ $property->bedrooms }}
														<span class="vtooltiptext  text-14">{{ $property->bedrooms }} {{trans('messages.property_single.bedroom')}}</span>
													</div>
												</li>

												<li class="list-inline-item pl-4 pr-4 border rounded-3 mt-2 bg-light">
													<div class="vtooltip"> <i class="fas fa-bath"></i> {{ $property->bathrooms }}
														<span class="vtooltiptext  text-14 p-2">{{ $property->bathrooms }} {{trans('messages.property_single.bathroom')}}</span>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</section>
	@endif

	@if(!$testimonials->isEmpty())
	<section class="testimonialbg pb-70">
		<div class="testimonials">
			<div class="container">
				<div class="row">
					<div class="recommandedhead section-intro text-center mt-70">
						<p class="animated fadeIn text-24 text-color font-weight-700 m-0">Customer Reviews</p>
						<p class="mt-2 d-none"></p>
					</div>
				</div>

				<div class="row mt-5">
					@foreach($testimonials as $testimonial)
					<?php $i = 0; ?>
						<div class="col-md-4 mt-4">
							<div class="item h-100 card-1">
								<img src="{{$testimonial->image_url}}" alt="{{$testimonial->name}}" class="lazy">
								<div class="name">{{$testimonial->name}}</div>
									<small class="desig">{{$testimonial->designation}}</small>
									<p class="details">{{ substr($testimonial->description, 0, 200) }} </p>
									<ul>
										@for ($i = 0; $i < 5; $i++)
											@if($testimonial->review >$i)
												<li><i class="fa fa-star secondary-text-color" aria-hidden="true"></i></li>
											@else
												<li><i class="fa fa-star rating" aria-hidden="true"></i></li>
											@endif
										@endfor
									</ul>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</section>
	@endif
@stop

@push('scripts')

<!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/66227aee1ec1082f04e4a35f/1hrrb0d8k';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
	<script type="text/javascript" src='https://maps.google.com/maps/api/js?key={{ @$map_key }}&libraries=places'></script>
	<script type="text/javascript" src="{{ asset('js/js/moment.min.js') }}"></script>
    @auth
        <script src="{{ asset('js/js/sweetalert.min.js') }}"></script>
    @endauth
	<script type="text/javascript" src="{{ asset('js/js/daterangepicker.min.js')}}"></script>
	<script type="text/javascript" src="{{ asset('js/js/front.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/js/daterangecustom.js')}}"></script>
	<script type="text/javascript">
		$(function() {
			dateRangeBtn(moment(),moment(), null, '{{$date_format}}');
		});

        @auth
        $(document).on('click', '.book_mark_change', function(event){
            event.preventDefault();
            var property_id = $(this).data("id");
            var property_status = $(this).data("status");
            var user_id = "{{Auth::id()}}";
            var dataURL = APP_URL+'/add-edit-book-mark';
            var that = this;
            if (property_status == "1")
            {
                var title = "{{trans('messages.favourite.remove')}}";

            } else {

                var title = "{{trans('messages.favourite.add')}}";
            }

            swal({
                title: title,
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "{{trans('messages.general.no')}}",
                        value: null,
                        visible: true,
                        className: "btn btn-outline-danger text-16 font-weight-700  pt-3 pb-3 pl-5 pr-5",
                        closeModal: true,
                    },
                    confirm: {
                        text: "{{trans('messages.general.yes')}}",
                        value: true,
                        visible: true,
                        className: "btn vbtn-outline-success text-16 font-weight-700 pl-5 pr-5 pt-3 pb-3 pl-5 pr-5",
                        closeModal: true
                    }
                },
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            url: dataURL,
                            data:{
                                "_token": "{{ csrf_token() }}",
                                'id':property_id,
                                'user_id':user_id,
                            },
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {

                                $(that).removeData('status')
                                if(data.favourite.status == 'Active') {
                                    $(that).css('color', '#1dbf73');
                                    $(that).attr("data-status", 1);
                                    swal('success', '{{trans('messages.success.favourite_add_success')}}');

                                } else {
                                    $(that).css('color', 'black');
                                    $(that).attr("data-status", 0);
                                    swal('success', '{{trans('messages.success.favourite_remove_success')}}');


                                }
                            }
                        });

                    }
                });
        });
        @endauth
	</script>
@endpush

