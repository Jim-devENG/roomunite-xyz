@extends('template')
@section('main')
<style>
.message-footer {
	display: flex;
	align-items: center; /* Ensure vertical alignment */
	justify-content: space-between; /* Space out children evenly */
	border-top: 1px solid #ddd;
	background: #eee;
	padding: 10px;
	height: 60px;
	gap: 0px; /* 10px; Space between elements */
}

.message-footer input,
.message-footer textarea {
    flex-grow: 1;
    border-radius: 5px;
    padding: 10px 20px; /* Uniform padding */
    border: 1px solid #ccc;
}

.message-footer textarea {
    resize: none; /* Prevent resizing */
    width: calc(100% - 50px); /* Adjust for button widths */
}
</style>
<div class="margin-top-85">
	<div class="row m-0">
		{{-- sidebar start--}}
		@include('users.sidebar')
		{{--sidebar end--}}

		<div class="col-lg-10 p-0 mb-5 min-height">
			<div class="main-panel">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 p-0 mb-3">
							<div class="list-bacground mt-4 rounded-3 p-4 border">
								<span class="text-18 pt-4 pb-4 font-weight-700">{{trans('messages.header.inbox')}}</span>
							</div>
						</div>
					</div>
					@if(isset($booking))
						<div class="row">
							<div class="col-md-9 p-0">
								<div class="container-inbox">
									<sidebar>
										<div class="list-wrap overflow-hidden-x">
											@forelse($messages as $message)
												@php
													Auth::id() != $message->sender->id ? $user = $message->sender :$user =$message->receiver;
												@endphp
												<div class="list p-2 conversassion" data-id="{{ $message->bookings->id }}">
													<img src="{{ $user->profile_src }}" alt="user" />
													<div class="info">
														<h3 class="font-weight-700 "  >{{ $user->first_name }} <span class="text-muted text-12 text-right"> {{$message->created_at->diffForHumans()}}</span></h3>
														<div class="d-flex justify-content-between">
															<div>
																<p class="text-muted text-14 mb-1 text pr-4">{{ substr($message->properties->name, 0,35)  }}</p>
																@if($message->receiver_id == Auth::id())
																	<p class="text-14 m-0 {{$message->read == 0  ? 'text-success font-weight-bold':''}}" id="msg-{{ $message->bookings->id }}" ><i class="far fa-comment-alt"></i> {{ str_limit($message->message, 20) }} </p>
																@else
																	<p class="text-14 m-0" ><i class="far fa-comment-alt"></i> {{ str_limit($message->message, 20) }} </p>
																@endif


															</div>
														</div>
													</div>
												</div>
											@empty
												no conversassion
											@endforelse
										</div>
									</sidebar>

									<div class="content-inbox container-fluid p-0" id="messages">
										<header>
											@php
												$booking->host_id == Auth::id() ? $users ='users':$users ='host';
											@endphp
												<a href="{{ url('/') }}/users/show/{{ $booking->$users->id}}">
													<img src="{{ $booking->$users->profile_src}}" alt="img" class="img-40x40" >
												</a>

												<div class="info">
													<div class="d-flex justify-content-between">
														<div>
															<span class="user">{{ $booking->$users->full_name}}</span>
														</div>
													</div>
												</div>

												<div class="open">
													<i class="fas fa-inbox"></i>
													<a href="javascript:;">UP</a>
												</div>
										</header>

										{{-- message-list --}}
										<div class="message-wrap d-none">
											@foreach( $conversation as $con)
												<div class="{{$con->receiver_id == Auth::id() ? 'message-list' :'message-list me'}} message-list">
													<div class="msg pl-2 pr-2 pb-2 pt-2 mb-2">
														<p class="m-0" style="white-space: pre-wrap;">{{$con->message}}</p>
													</div>
													<div class="time">{{$con->created_at->diffForHumans()}}</div>
												</div>
											@endforeach
											<div class="message-list me">
													<div class="msg_txt mb-0"></div>
													<div class="time msg_time mt-0"></div>
											</div>
										</div>

										<div class="message-wrap">
											@foreach($conversation as $con)
												<div class="{{ $con->receiver_id == Auth::id() ? 'message-list' : 'message-list me' }} message-list">
													<div class="msg pl-2 pr-2 pb-2 pt-2 mb-2">
														@if($con->file_path) 
															{{-- Check if it's an image --}}
															@if(in_array(pathinfo($con->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
																<p class="m-0">
																	<img src="{{ $con->file_path }}" 
																		 alt="Image" style="max-width: 200px; border-radius: 8px;">
																</p>
															{{-- Check if it's a video --}}
															@elseif(in_array(pathinfo($con->file_path, PATHINFO_EXTENSION), ['mp4', 'webm', 'ogg']))
																<p class="m-0">
																	<video width="200" controls>
																		<source src="{{ $con->file_path }}" type="video/{{ pathinfo($con->file_path, PATHINFO_EXTENSION) }}">
																		Your browser does not support the video tag.
																	</video>
																</p>
															{{-- Other file types (PDF, ZIP, etc.) --}}
															@else
																<p class="m-0">
																	<a href="{{ $con->file_path }}" 
																	   target="_blank" class="btn btn-sm btn-outline-primary">
																		<i class="fa fa-download"></i> Download {{ pathinfo($con->file_path, PATHINFO_BASENAME) }}
																	</a>
																</p>
															@endif
														@else
															{{-- Regular text message --}}
															<p class="m-0" style="white-space: pre-wrap;">{{ $con->message }}</p>
														@endif
													</div>
													<div class="time">{{ $con->created_at->diffForHumans() }}</div>
												</div>
											@endforeach
										
											{{-- Placeholder for new messages --}}
											<div class="message-list me">
												<div class="msg_txt mb-0"></div>
												<div class="time msg_time mt-0"></div>
											</div>
										</div>
										
										{{-- form and send-file and send-btn --}}
										<div class="message-footer">
											<a href="javascript:void(0)" class="btn btn-success chat text-18 upload-file" data-booking="{{$booking->id}}" data-receiver="{{ $booking->$users->id }}" data-property="{{ $booking->property_id }}"
												style="line-height: 4.3"><i class="fa fa-image" aria-hidden="true"></i>
											</a>

											{{-- Add this hidden input field --}}
											<input type="file" id="chat-file-input" style="display: none;" accept="*/*"><!-- All file types -->
	
											{{-- <textarea type="text" class="cht_msg" data-placeholder="Send a message to {0}" rows={1} style="width: 100%; padding-right: 40px;"> </textarea> --}}
											<textarea type="text" class="cht_msg" data-placeholder="Send a message to {0}" rows="3" style="width: 100%; padding-right: 40px;"> </textarea>

											<a href="javascript:void(0)" class="btn btn-success chat text-18 send-btn" 
												data-booking="{{$booking->id}}" data-receiver="{{ isset($booking->$users) ? $booking->$users->id : '' }}" data-property="{{ $booking->property_id }}"
												style="line-height: 4.3"><i class="fa fa-paper-plane" aria-hidden="true"></i>
											</a>
											
										</div>

									</div>
								</div>
							</div>

							<div class="col-md-3 card p-0 " id="booking">
								<div class="w-100 overflow-auto right-inbox p-3">
									<a href="{{ url('/') }}/properties/{{ $booking->properties->slug }}"><h4 class="text-left text-16 font-weight-700">{{$booking->properties->name}}</h4></a>
									<span class="street-address text-muted text-14">
										<i class="fas fa-map-marker-alt mr-2"></i>{{$booking->properties->property_address->address_line_1}}
									</span>

									<div class="row">
										<div class="col-md-12 border p-2 rounded mt-2">
											<div class="d-flex  justify-content-between">
												<div>
													<div class="text-16"><strong>{{trans('messages.header.check_in')}}</strong></div>
													<div class="text-14">{{ onlyFormat($booking->start_date) }}</div>
												</div>

												<div>
													<div class="text-16"><strong>{{trans('messages.header.check_out')}}</strong></div>
													<div class="text-14">{{ onlyFormat($booking->end_date) }}</div>
												</div>

											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12 col-sm-6 col-xs-6 border border-success pl-3 pr-3 text-center pt-3 pb-3 mt-3 rounded-3">
											<p class="text-16 font-weight-700 text-success pt-0 m-0">
												<i class="fas fa-bed text-20 d-none d-sm-inline-block pr-2 text-success"></i><strong>{{$booking->guest}}</strong> <!-- <br> --> {{trans('messages.header.guest')}} </p>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12 p-2">
											<h5 class="text-16 mt-3"><strong>{{trans('messages.payment.payment')}}</strong></h5>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12 p-0">
											<div class="full-table mt-2 border text-dark rounded-3 pt-3 pb-3 mb-4 p-4">
												<p class="row margin-top10 text-justify text-16 mb-0">
													<span class="text-left col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_per_night) !!} x {{$booking->total_night}} {{trans('messages.property_single.night')}} </span>
													<span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_per_night * $booking->total_night) !!}</span>
												</p>

												<p class="row margin-top10 text-justify text-16 mb-0">
													<span class="text-left col-sm-6 text-14">{{trans('messages.property_single.service_fee')}}</span>
													<span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_service_charge)!!}</span>
												</p>

												@if($booking->accomodation_tax)
												<p class="row margin-top10 text-justify text-16 mb-0">
													<span class="text-left col-sm-6 text-14">{{trans('messages.property_single.accommodatiton_tax')}}</span>
													<span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_accomodation_tax)!!}</span>
												</p>
												@endif

												@if($booking->iva_tax)
												<p class="row margin-top10 text-justify text-16 mb-0">
													<span class="text-left col-sm-6 text-14">{{trans('messages.property_single.iva_tax')}}</span>
													<span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_iva_tax)!!}</span>
												</p>
												@endif

                                                @if($booking->cleaning_charge)
                                                    <p class="row margin-top10 text-justify text-16">
                                                        <span class="text-left col-sm-6 text-14">{{trans('messages.property_single.cleaning_fee')}}</span>
                                                        <span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_cleaning_charge)!!}</span>
                                                    </p>
                                                @endif

                                                @if($booking->security_money)
                                                    <p class="row margin-top10 text-justify text-16">
                                                        <span class="text-left col-sm-6 text-14">{{trans('messages.property_single.security_fee')}}</span>
                                                        <span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_security_money)!!}</span>
                                                    </p>
                                                @endif

												<p class="row margin-top10 text-justify text-16 mb-0">
													<span class="text-left col-sm-6 text-14">{{trans('messages.property_single.total')}}</span>
													<span class="text-right col-sm-6 text-14">{!! moneyFormat($symbol, $booking->original_total)!!}</span>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					@else
						<div class="row jutify-content-center w-100 p-4 mt-4">
							<div class="text-center w-100">
								<img src="{{ url('public/img/unnamed.png')}}"   alt="notfound" class="img-fluid">
								<p class="text-center">{{trans('messages.message.empty_inbox')}} </p>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@push('scripts')
<script type="text/javascript">
	const ls = localStorage.getItem("selected");
	let selected = false;
	var list = document.querySelectorAll(".list"),
	content = document.querySelector(".content-inbox"),
	input = document.querySelector(".message-footer input"),
	open = document.querySelector(".open a");
	//process
	function process() {
	    if(ls != null) {
	        selected = true;
	        click(list[ls], ls);
	    }
	    if(!selected) {
	        click(list[0], 0);
	    }

	    list.forEach((l,i) => {
	        l.addEventListener("click", function() {
	            click(l, i);
	        });
	    });

	    try {
	        document.querySelector(".list.active").scrollIntoView(false);
	    }
	    catch {}

	}
	process();

	//list click
	function click(l, index) {
	    list.forEach(x => { x.classList.remove("active"); });
	        if(l) {
	            l.classList.add("active");
	            document.querySelector("sidebar").classList.remove("opened");
	            open.innerText="UP";
	        document.querySelector(".message-wrap").scrollTop = document.querySelector(".message-wrap").scrollHeight;
	        localStorage.setItem("selected", index);
	    }
	}

	open.addEventListener("click", (e) => {
	    const sidebar = document.querySelector("sidebar");
	    sidebar.classList.toggle("opened");
	    if(sidebar.classList.value == 'opened')
	        e.target.innerText = "DOWN";
	    else
	        e.target.innerText = "UP";
	});

	$(document).on('click', '.conversassion', function(){
	    var id = $(this).data('id');
	    var dataURL = APP_URL+'/messaging/booking';
	    $.ajax({
	        url: dataURL,
	        data:{
	            "_token": "{{ csrf_token() }}",
	            'id':id,
	        },
	        type: 'post',
	        dataType: 'json',
	        success: function(data) {
	            $('#msg-'+id).removeClass('text-success');
	            $('#messages').empty().html(data['inbox']);
	            $('#booking').empty().html(data['booking']);
	        }
	    })
	});

	$(document).on('click', '.chat', function(){
	    var msg = $('.cht_msg').val();
	    var booking_id = $(this).data('booking');
	    var receiver_id = $(this).data('receiver');
	    var property_id = $(this).data('property');
	    var result = '<div class="msg pl-2 pr-2 pb-2 pt-2 mb-2">'
						+'<p class="m-0" style="white-space: pre-wrap;">'+sanitize(msg)+'</p>'
					+'</div>'
					+'<div class="time">just now</div>'

	    var dataURL = APP_URL+'/messaging/reply';
	    $.ajax({
	        url: dataURL,
	        data:{
	            "_token": "{{ csrf_token() }}",
	            'msg':msg,
	            'booking_id':booking_id,
	            'receiver_id':receiver_id,
	            'property_id':property_id,
	        },
	        type: 'post',
	        dataType: 'json',
	        success: function(data) {
	            $('.msg_txt').append(result);

	            $('.cht_msg').val("");
	        }
	    })
	});

	//upload-file
	// File upload logic
$(document).ready(function() {
    // Trigger file input when upload button is clicked
    $(document).on('click', '.upload-file', function() {
        $('#chat-file-input').trigger('click');

        // Store data attributes for use when the file is selected
        $('#chat-file-input').data('booking', $(this).data('booking'));
        $('#chat-file-input').data('receiver', $(this).data('receiver'));
        $('#chat-file-input').data('property', $(this).data('property'));
    });

    // Handle file selection and upload
    $('#chat-file-input').on('change', function(e) {
        var file = e.target.files[0];
        var maxSize = 5 * 1024 * 1024; // 5MB size limit

        if (file) {
            if (file.size > maxSize) {
                alert('File size must not exceed 5MB.');
                return;
            }

            // Prepare data
            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('file', file);
            formData.append('booking_id', $(this).data('booking'));
            formData.append('receiver_id', $(this).data('receiver'));
            formData.append('property_id', $(this).data('property'));

            $.ajax({
                url: APP_URL + '/messaging/upload-file',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Append the file to the chat UI dynamically based on its type
                        var fileHtml = '';
                        var fileExtension = response.file_name.split('.').pop().toLowerCase();

                        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                            // Image file
                            fileHtml = '<img src="' + response.file_url + '" class="uploaded-file" style="max-width: 200px; border-radius: 8px;">';
                        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                            // Video file
                            fileHtml = '<video width="200" controls>' +
                                '<source src="' + response.file_url + '" type="video/' + fileExtension + '">' +
                                'Your browser does not support the video tag.' +
                                '</video>';
                        } else if (['pdf'].includes(fileExtension)) {
                            // PDF file
                            fileHtml = '<a href="' + response.file_url + '" target="_blank" class="btn btn-outline-primary">' +
                                '<i class="fa fa-file-pdf"></i> View PDF</a>';
                        } else if (['zip', 'rar'].includes(fileExtension)) {
                            // ZIP or RAR file
                            fileHtml = '<a href="' + response.file_url + '" target="_blank" class="btn btn-outline-secondary">' +
                                '<i class="fa fa-file-archive"></i> Download Archive</a>';
                        } else {
                            // Generic file type
                            fileHtml = '<a href="' + response.file_url + '" target="_blank" class="btn btn-outline-dark">' +
                                '<i class="fa fa-file"></i> ' + response.file_name + '</a>';
                        }

                        var result = '<div class="msg pl-2 pr-2 pb-2 pt-2 mb-2">'
                                    + '<div>' + fileHtml + '</div>'
                                    + '</div>'
                                    + '<div class="time">just now</div>';

                        $('.msg_txt').append(result); // Add to chat
                    } else {
                        alert('File upload failed. Try again.');
                        console.log(response);
                    }
                },
                error: function() {
                    alert('An error occurred while uploading the file.');
                }
            });

            // Clear file input
            $(this).val('');
        }
    });
});

	// $(document).ready(function() {
	// 	// Trigger file input when upload button is clicked
	// 	$(document).on('click', '.upload-file', function() {
	// 		$('#chat-file-input').trigger('click');
			
	// 		// Store data attributes for use when the file is selected
	// 		$('#chat-file-input').data('booking', $(this).data('booking'));
	// 		$('#chat-file-input').data('receiver', $(this).data('receiver'));
	// 		$('#chat-file-input').data('property', $(this).data('property'));
	// 	});

	// 	// Handle file selection and upload
	// 	$('#chat-file-input').on('change', function(e) {
	// 		var file = e.target.files[0];
	// 		var maxSize = 5 * 1024 * 1024; // 5MB size limit

	// 		if (file) {
	// 			if (file.size > maxSize) {
	// 				alert('File size must not exceed 5MB.');
	// 				return;
	// 			}

	// 			// Prepare data
	// 			var formData = new FormData();
	// 			formData.append('_token', "{{ csrf_token() }}");
	// 			formData.append('file', file);
	// 			formData.append('booking_id', $(this).data('booking'));
	// 			formData.append('receiver_id', $(this).data('receiver'));
	// 			formData.append('property_id', $(this).data('property'));

	// 			$.ajax({
	// 				url: APP_URL + '/messaging/upload-file',
	// 				type: 'POST',
	// 				data: formData,
	// 				processData: false,
	// 				contentType: false,
	// 				success: function(response) {
	// 					if (response.success) {
	// 						// Append the file to the chat UI
	// 						var fileHtml = '';
	// 						if (response.file_type === 'image') {
	// 							fileHtml = '<img src="' + response.file_url + '" class="uploaded-file" style="max-width: 200px;">';
	// 						} else {
	// 							fileHtml = '<a href="' + response.file_url + '" target="_blank">' + response.file_name + '</a>';
	// 						}

	// 						var result = '<div class="msg pl-2 pr-2 pb-2 pt-2 mb-2">'
	// 									+ '<div>' + fileHtml + '</div>'
	// 									+ '</div>'
	// 									+ '<div class="time">just now</div>';

	// 						$('.msg_txt').append(result); // Add to chat
	// 					} else {
	// 						alert('File upload failed. Try again.');
	// 						console.log({response});
							
	// 					}
	// 				},
	// 				error: function() {
	// 					alert('An error occurred while uploading the file.');
	// 				}
	// 			});

	// 			// Clear file input
	// 			$(this).val('');
	// 		}
	// 	});
	// });


	/////////////////////

	 $(".cht_msg").on('keyup', function(event) {
	    if (event.which===13) {
	        event.preventDefault();
	        var s = $(this).val();
            //(this).val(s + "\n");
	        //$('.chat').trigger("click");
	    }
	    
	}); 
    function sanitize(string) {
        const symbols = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            "/": '&#x2F;',
        };
        const regex = /[&<>"'/]/ig;
        return string.replace(regex, (match)=>(symbols[match]));
    }
</script>
@endpush
