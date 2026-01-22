<?php

namespace App\Http\Controllers;

use Cache;
use Auth;
use DB;
use Session;

use App\Http\Helpers\Common;
use App\Http\Controllers\CalendarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

use App\Models\{Favourite,
    User,
    Properties,
    PropertyDetails,
    PropertyAddress,
    PropertyPhotos,
    PropertyPrice,
    PropertyType,
    PropertyDates,
    PropertyDescription,
    PropertyIcalimport,
    Currency,
    Settings,
    Bookings,
    SpaceType,
    BedType,
    PropertySteps,
    Country,
    Amenities,
    AmenityType};
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->helper = new Common;
    }

    public function userProperties(Request $request)
    {
        switch ($request->status) {
            case 'Listed':
            case 'Unlisted':
                $pram = [['status', '=', $request->status]];
                break;
            default:
                $pram = [];
                break;
        }

        $data['status'] = $request->status;
        $data['properties'] = Properties::with('property_price.currency', 'property_address')
                                ->where('host_id', Auth::id())
                                ->where($pram)
                                ->orderBy('id', 'desc')
                                ->paginate(Session::get('row_per_page'));
        $data['currentCurrency'] =  $this->helper->getCurrentCurrency();
        return view('property.listings', $data);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = array(
                'property_type_id'  => 'required',
                'space_type'        => 'required',
                'accommodates'      => 'required',
                'map_address'       => 'required',
                'furnishing'        => 'required',
                'stay_term'         => 'required',
            );

            $fieldNames = array(
                'property_type_id'  => 'Home Type',
                'space_type'        => 'Room Type',
                'accommodates'      => 'Accommodates',
                'map_address'       => 'City',
                'furnishing'        => 'Furnishing',
                'stay_term'         => 'Stay Term',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            
            Log::info("CURRENCY RATE!");
            Log::info(\Session::get('currency'));
           /* Log::info("PROPERTY!");
            Log::info(SpaceType::getAll()->find($request->space_type)->name.' in '.$request->city);*/

            if ($validator->fails()) {
                Log::info("FAIL!");
                Log::info($validator->errors());
                return back()->withErrors($validator)->withInput();
            } else {
                
            
                $property                  = new Properties;
                $property->host_id         = Auth::id();
                $property->name            = SpaceType::getAll()->find($request->space_type)->name.' in '.$request->city;
                $property->property_type   = $request->property_type_id;
                $property->space_type      = $request->space_type;
                $property->accommodates    = $request->accommodates;
                $property->stay_duration_type   = $request->stay_term;
                $property->furnishing_status    = $request->furnshing;
                $property->save();
                
                $property_address                 = new PropertyAddress;
                $property_address->property_id    = $property->id;
                $property_address->address_line_1 = $request->route;
                $property_address->city           = $request->city;
                $property_address->state          = $request->state;
                $property_address->country        = $request->country;
                $property_address->postal_code    = $request->postal_code;
                $property_address->latitude       = $request->latitude;
                $property_address->longitude      = $request->longitude;
                $property_address->save();

                $property_price                 = new PropertyPrice;
                $property_price->property_id    = $property->id;
                $property_price->currency_code  = \Session::get('currency');
                $property_price->price    = 0;
                $property_price->save();

                $property_steps                   = new PropertySteps;
                $property_steps->property_id      = $property->id;
                $property_steps->save();

                $property_description              = new PropertyDescription;
                $property_description->property_id = $property->id;
                $property_description->save();
                

                // Send Mail to Admin
                $property_mail = [
                    'title' => $property->name,
                    'description' => $property_description->summary,
                    'price' => $property_price->price,
                    'location' => $property_address->address_line_1 ? $property_address->address_line_1 : $property_address->address_line_2,
                ];


                // Admin email address
                $adminEmail = 'support@roomunite.com';

                // Email subject
                $subject = 'New Property Created';

                // Prepare the email content
                $data = [
                    'property' => $property_mail, // Pass the property details to the email template
                ];

                // Send the email to the admin
                // Mail::send('emails.new_property_template', $data, function($message) use ($adminEmail, $subject) {
                //     $message->to($adminEmail)->subject($subject);
                // });

                // // Send the email to the host
                // $host = Auth::user();
                // $hostEmail = $host->email;
                // Mail::send('emails.new_property_host', $data, function($message) use ($hostEmail, $subject) {
                //     $message->to($hostEmail)->subject($subject);
                // });
                
               
                return redirect('listing/'.$property->id.'/basics');
            }
        }
        
        $data['property_type'] = PropertyType::getAll()->where('status', 'Active')->pluck('name', 'id');
        $data['space_type']    = SpaceType::getAll()->where('status', 'Active')->pluck('name', 'id');
        
        return view('property.create', $data);
    }

    public function listing(Request $request, CalendarController $calendar)
    {
        $step            = $request->step;
        $property_id     = $request->id;
        $data['step']    = $step;
        
        $data['result']  = Properties::where('host_id', Auth::id())->findOrFail($property_id);
        
        $data['details'] = PropertyDetails::pluck('value', 'field');
        $data['missed']  = PropertySteps::where('property_id', $request->id)->first();

        $data['property_cancellation_policies'] = DB::table('property_cancellation_policies')->get();
        // $data['selected_cancellation_policy'] = $data['result']['property_cancellation_policy'];
        
        if ($step == 'basics') {
            if ($request->isMethod('post')) {
                $property                     = Properties::find($property_id);
                $property->bedrooms           = $request->bedrooms;
                $property->beds               = $request->beds;
                $property->bathrooms          = $request->bathrooms;
                $property->bed_type           = $request->bed_type;
                $property->property_type      = $request->property_type;
                $property->space_type         = $request->space_type;
                $property->accommodates       = $request->accommodates;
                $property->stay_duration_type = $request->stay_term;
                $property->furnishing_status  = $request->furnshing;
                $property->save();

                $property_steps         = PropertySteps::where('property_id', $property_id)->first();
                $property_steps->basics = 1;
                $property_steps->save();
                return redirect('listing/'.$property_id.'/description');
            }

            $data['bed_type']       = BedType::getAll()->pluck('name', 'id');
            $data['property_type']  = PropertyType::getAll()->where('status', 'Active')->pluck('name', 'id');
            $data['space_type']     = SpaceType::getAll()->pluck('name', 'id');
            $data['furnishing_type'] = [0 => "Furnished", 1 => "Unfurnished", 2 => "Semi-Furnished"];
            $data['stay_duration_term'] = ["short" => "Less than 6 months", "long" => "6 months and above"];
            //if($this->scattered()) {
                //Session::flush();
                //return view('vendor.installer.errors.user');
            //}
            
        } elseif ($step == 'description') {
            
            if ($request->isMethod('post')) {

                $rules = array(
                    'name'     => 'required|max:50',
                    'summary'  => 'required|max:5000'
                );

                $fieldNames = array(
                    'name'     => 'Name',
                    'summary'  => 'Summary',
                );

                $validator = Validator::make($request->all(), $rules);
                $validator->setAttributeNames($fieldNames);

                if ($validator->fails())
                {
                    return back()->withErrors($validator)->withInput();
                }
                else
                {
                   // if($property->stay_term == 0) {
                        $property           = Properties::find($property_id);
                        $property->name     = $request->name;
                        $property->slug     = $this->helper->pretty_url($request->name);
                        $property->save();
    
                        $property_description              = PropertyDescription::where('property_id', $property_id)->first();
                        $property_description->summary     = $request->summary;
                        $property_description->save();
    
                        $property_steps              = PropertySteps::where('property_id', $property_id)->first();
                        $property_steps->description = 1;
                        $property_steps->save();
                        return redirect('listing/'.$property_id.'/external_link');
                  //  }
                   
                }
            }
            $data['description'] = PropertyDescription::where('property_id', $property_id)->first();
        } elseif ($step == 'details') {
            if ($request->isMethod('post')) {
                $property_description                       = PropertyDescription::where('property_id', $property_id)->first();
                $property_description->about_place          = $request->about_place;
                $property_description->place_is_great_for   = $request->place_is_great_for;
                $property_description->guest_can_access     = $request->guest_can_access;
                $property_description->interaction_guests   = $request->interaction_guests;
                $property_description->other                = $request->other;
                $property_description->about_neighborhood   = $request->about_neighborhood;
                $property_description->get_around           = $request->get_around;
                $property_description->save();

                return redirect('listing/'.$property_id.'/description');
            }
        } elseif ($step == 'external_link') {
            
        if ($request->isMethod('post')) {

            $rules = array(
                'external_link_title'     => 'required',
                'external_link_url'  => 'required'
            );

            $fieldNames = array(
                'external_link_title'     => 'External Link Title',
                'external_link_url'  => 'External Link Url',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
               // if($property->stay_term == 0) {
                    $property           = Properties::find($property_id);
                    $property->external_link_title     = $request->external_link_title;
                    $property->external_link_url     = $request->external_link_url;
                    $property->save();

                    $property_steps              = PropertySteps::where('property_id', $property_id)->first();
                    $property_steps->external_link = 1;
                    $property_steps->save();
                    return redirect('listing/'.$property_id.'/location');
              //  }
               
            }
        }
            // $data['external_link_title'] = $property->external_link_title ? $property->external_link_title : null;
            // $data['external_link_url'] = $property->external_link_url ? $property->external_link_url : null;
        } elseif ($step == 'location') {
            if ($request->isMethod('post')) {
                    $rules = array(
                        'address_line_1'    => 'required|max:250',
                        'address_line_2'    => 'max:250',
                        'country'           => 'required',
                        'city'              => 'required',
                        'state'             => 'required',
                        'latitude'          => 'required|not_in:0',
                    );

                    $fieldNames = array(
                        'address_line_1' => 'Address Line 1',
                        'country'        => 'Country',
                        'city'           => 'City',
                        'state'          => 'State',
                        'latitude'       => 'Map',
                    );

                    $messages = [
                        'not_in' => 'Please set :attribute pointer',
                    ];

                    $validator = Validator::make($request->all(), $rules, $messages);
                    $validator->setAttributeNames($fieldNames);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    } else {
                        $property_address                 = PropertyAddress::where('property_id', $property_id)->first();
                        $property_address->address_line_1 = $request->address_line_1;
                        $property_address->address_line_2 = $request->address_line_2;
                        $property_address->latitude       = $request->latitude;
                        $property_address->longitude      = $request->longitude;
                        $property_address->city           = $request->city;
                        $property_address->state          = $request->state;
                        $property_address->country        = $request->country;
                        $property_address->postal_code    = $request->postal_code;
                        $property_address->save();

                        $property_steps           = PropertySteps::where('property_id', $property_id)->first();
                        $property_steps->location = 1;
                        $property_steps->save();

                        return redirect('listing/'.$property_id.'/amenities');
                    }
                }
            $data['country']       = Country::pluck('name', 'short_name');
        } elseif ($step == 'amenities') {
            if ($request->isMethod('post') && is_array($request->amenities)) {
                $rooms            = Properties::find($request->id);
                $rooms->amenities = implode(',', $request->amenities);
                $rooms->save();
                return redirect('listing/'.$property_id.'/photos');
            }
                $data['property_amenities'] = explode(',', $data['result']->amenities);
                $data['amenities']          = Amenities::where('status', 'Active')->get();
                $data['amenities_type']     = AmenityType::get();
        } elseif ($step == 'photos') {
            if($request->isMethod('post')) {
                if($request->crop == 'crop' && $request->photos) {
                    $baseText = explode(";base64,", $request->photos);
                    $name = explode(".", $request->img_name);
                    $convertedImage = base64_decode($baseText[1]);
                    $request->request->add(['type'=>end($name)]);
                    $request->request->add(['image'=>$convertedImage]);


                    $validate = Validator::make($request->all(), [
                        'type' => 'required|in:png,jpg,JPG,JPEG,jpeg,bmp|max:2048',
                        'img_name' => 'required',
                        'photos' => 'required',
                    ]);
                } else {
                    $validate = Validator::make($request->all(), [
                        'file' => 'required|file|mimes:jpg,jpeg,bmp,png,gif,JPG|max:2048', // 2MB max size
                        'file' => 'dimensions:min_width=640,min_height=360'
                    ]);
                }

                if($validate->fails()) {
                    return back()->withErrors($validate)->withInput();
                }

                $path = public_path('images/property/'.$property_id.'/');

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                if($request->crop == "crop") {
                    $image = $name[0].uniqid().'.'.end($name);
                    $uploaded = file_put_contents($path . $image, $convertedImage);
                } else {
                    if (isset($_FILES["file"]["name"])) {
                        $tmp_name = $_FILES["file"]["tmp_name"];
                        $name = str_replace(' ', '_', $_FILES["file"]["name"]);
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        $image = time() . '_' . $name;
                        $path = 'public/images/property/' . $property_id;
                        if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'JPG') {
                            $uploaded = move_uploaded_file($tmp_name, $path . "/" . $image);
                        }
                    }
                }

                if ($uploaded) {
                    $photos = new PropertyPhotos;
                    $photos->property_id = $property_id;
                    $photos->photo = $image;
                    $photos->serial = 1;
                    $photos->cover_photo = 1;

                    $exist = PropertyPhotos::orderBy('serial', 'desc')
                        ->select('serial')
                        ->where('property_id', $property_id)
                        ->take(1)->first();

                    if (!empty($exist->serial)) {
                        $photos->serial = $exist->serial + 1;
                        $photos->cover_photo = 0;
                    }
                    $photos->save();
                    $property_steps = PropertySteps::where('property_id', $property_id)->first();
                    $property_steps->photos = 1;
                    $property_steps->save();
                }

                return redirect('listing/'.$property_id.'/photos')->with('success', 'File Uploaded Successfully!');

            }

            $data['photos'] = PropertyPhotos::where('property_id', $property_id)
                ->orderBy('serial', 'asc')
                ->get();

        } elseif ($step == 'pricing') {
            if ($request->isMethod('post')) {
                $bookings = Bookings::where('property_id', $property_id)->where('currency_code', '!=', $request->currency_code)->first();
                if($bookings) {
                    return back()->withErrors(['currency' => trans('messages.error.currency_change')]);
                }
                $rules = array(
                    'price' => 'required|numeric|min:5',
                    'weekly_discount' => 'nullable|numeric|max:99|min:0',
                    'monthly_discount' => 'nullable|numeric|max:99|min:0'
                );

                $fieldNames = array(
                    'price'  => 'Price',
                    'weekly_discount' => 'Weekly Discount Percent',
                    'monthly_discount' => 'Monthly Discount Percent'
                );

                $validator = Validator::make($request->all(), $rules);
                $validator->setAttributeNames($fieldNames);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    $property_price                    = PropertyPrice::where('property_id', $property_id)->first();
                    $property_price->price             = $request->price;
                    $property_price->weekly_discount   = $request->weekly_discount;
                    $property_price->monthly_discount  = $request->monthly_discount;
                    $property_price->currency_code     = $request->currency_code;
                    $property_price->cleaning_fee      = $request->cleaning_fee;
                    $property_price->guest_fee         = $request->guest_fee;
                    $property_price->guest_after       = $request->guest_after;
                // $property_price->security_fee      = $request->security_fee;
                    $property_price->weekend_price     = $request->weekend_price;
                    $property_price->save();

                    $property_steps = PropertySteps::where('property_id', $property_id)->first();
                    $property_steps->pricing = 1;
                    $property_steps->save();

                    return redirect('listing/'.$property_id.'/booking');
                }
            }
        } elseif ($step == 'booking') {
            if ($request->isMethod('post')) {

                $property_steps          = PropertySteps::where('property_id', $property_id)->first();
                $property_steps->booking = 1;
                $property_steps->save();

                $properties               = Properties::find($property_id);
                $properties->booking_type = $request->booking_type;
                $properties->max_days = $request->max_days;
                $properties->min_days = $request->min_days;
            
                $properties->save();

                // return redirect('listing/'.$property_id.'/calendar');
                return redirect('listing/' . $property_id . '/cancellation_policy');
            }
        } elseif ($step == 'cancellation_policy') {
            if ($request->isMethod('post')) {

                    if(empty($request->cancellation_policy)){
                        return redirect('listing/' . $property_id . '/cancellation_policy');
                    }
        
                    $property_steps          = PropertySteps::where('property_id', $property_id)->first();
                    $property_steps->cancellation_policy = !empty($request->cancellation_policy) ? 1 : 0;
                    $property_steps->save();

                    $properties = Properties::find($property_id);
                    $ppty_cp = DB::table('property_cancellation_policies')->where('id', $request->cancellation_policy)->first();
                    $properties->property_cancellation_policy_id = (int) $request->cancellation_policy;
                    $properties->cancellation_policy_title = $ppty_cp->title;
                    $properties->cancellation_policy_desc = $ppty_cp->description;
                    $properties->save();

                    return redirect('listing/' . $property_id . '/calendar');
                }
            $cancellation_policy = Properties::where('id', $property_id)->first();
            $data['cancellation_policy'] = $cancellation_policy->cancellation_policy;

        } elseif ($step == 'payment_processing') {
                if ($request->isMethod('post')) {

                    $property_steps          = PropertySteps::where('property_id', $property_id)->first();
                    $property_steps->payment_processing = 1;
                    $property_steps->save();

                    $properties               = Properties::find($property_id);
                    $properties->payment_processing = $request->payment_processing;
                    $properties->status       = ( $properties->steps_completed == 0 ) ?  'Unlisted' : 'Unlisted';
                    $properties->save();


                    return redirect('listing/'.$property_id.'/calendar');
                }
            }
            
        elseif ($step == 'calendar') {
                $data['calendar'] = $calendar->generate($request->id);
                
                $propertyIcal = PropertyIcalimport::where('property_id', $request->id)->count();
                if ($propertyIcal > 0) {
                    $propertyIcal = PropertyIcalimport::orderBy('id', 'DESC')->where('property_id', $request->id)->first();
                }
                $data['propertyIcalImport'] = $propertyIcal ? $propertyIcal : null;
                // return $data['calendar'];
            }

            return view("listing.$step", $data);
    }

    public function updateStatus(Request $request)
    {
        $property_id = $request->id;
        $reqstatus = $request->status;
        if ($reqstatus == 'Listed') {
            $status = 'Unlisted';
        }else{
            $status = 'Listed';
        }
        $properties         = Properties::where('host_id', Auth::id())->find($property_id);
        $properties->status = $status;
        $properties->save();
        return  response()->json($properties);

    }

    public function getPrice(Request $request)
    {

        return $this->helper->getPrice($request->property_id, $request->checkin, $request->checkout, $request->guest_count);
    }

    public function single(Request $request)
    {
        
        /*
        
        ->where('properties.host_id', '!=', Auth::id())
        */

        $data['property_slug'] = $request->slug;


        $data['result'] = $result = Properties::with('property_price.currency', 'property_address')->where('slug', $request->slug)->first();
        // return $result;

        if ( empty($result)  ) {
            abort('404');
        }

         $data['property_id'] = $id = $result->id;

        $data['property_photos']     = PropertyPhotos::where('property_id', $id)->orderBy('serial', 'asc')
            ->get();

        $data['amenities']        = Amenities::normal($id);
        $data['safety_amenities'] = Amenities::security($id);

        $property_address         = $data['result']->property_address;

        $latitude                 = !empty($property_address) && isset($property_address->latitude) ? $property_address->latitude : null;
        $longitude                = !empty($property_address) && isset($property_address->longitude) ? $property_address->longitude : null;

        $data['checkin']          = (isset($request->checkin) && $request->checkin != '') ? $request->checkin:'';
        $data['checkout']         = (isset($request->checkout) && $request->checkout != '') ? $request->checkout:'';

        $data['guests']           = (isset($request->guests) && $request->guests != '')?$request->guests:'';

        // Only query similar properties if we have valid coordinates
        if (!empty($latitude) && !empty($longitude) && is_numeric($latitude) && is_numeric($longitude)) {
            $data['similar']  = Properties::join('property_address', function ($join) {
                                            $join->on('properties.id', '=', 'property_address.property_id');
            })
                                        ->select(DB::raw('properties.*, ( 3959 * acos( cos( radians('.floatval($latitude).') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.floatval($longitude).') ) + sin( radians('.floatval($latitude).') ) * sin( radians( latitude ) ) ) ) as distance'))
                                        ->having('distance', '<=', 30)
                                        ->where('properties.id', '!=', $id)
                                        ->where('properties.status', 'Listed')
                                        ->with(['property_address', 'property_price.currency', 'users'])
                                        ->get();
        } else {
            // If no coordinates, just get similar properties by status without distance calculation
            $data['similar'] = Properties::where('properties.id', '!=', $id)
                                        ->where('properties.status', 'Listed')
                                        ->with(['property_address', 'property_price.currency', 'users'])
                                        ->limit(10)
                                        ->get();
        }

        $data['title']    =   $data['result']->name.' in '.(!empty($data['result']->property_address) ? $data['result']->property_address->city : '');
        $data['symbol'] = $this->helper->getCurrentCurrencySymbol();
        $data['shareLink'] = url('/').'/'.'properties/'.$data['property_id'];

        $data['date_format'] = Settings::getAll()->firstWhere('name', 'date_format_type')->value;
        $data['max_days'] = $data['result']->max_days;
        $data['min_days'] = $data['result']->min_days;
        return view('property.single', $data);
    }

    public function currencySymbol(Request $request)
    {
        $symbol          = Currency::code_to_symbol($request->currency);
        $data['success'] = 1;
        $data['symbol']  = $symbol;

        return json_encode($data);
    }

    public function photoMessage(Request $request)
    {
        $property = Properties::find($request->id);
        if ($property->host_id == \Auth::user()->id) {
            $photos = PropertyPhotos::find($request->photo_id);
            $photos->message = $request->messages;
            $photos->save();
        }

        return json_encode(['success'=>'true']);
    }

    public function photoDelete(Request $request)
    {
        $property   = Properties::find($request->id);
        if ($property->host_id == \Auth::user()->id) {
            $photos = PropertyPhotos::find($request->photo_id);
            $photos->delete();
        }

        return json_encode(['success'=>'true']);
    }

    public function makeDefaultPhoto(Request $request)
    {

        if ($request->option_value == 'Yes') {
            PropertyPhotos::where('property_id', '=', $request->property_id)
            ->update(['cover_photo' => 0]);

            $photos = PropertyPhotos::find($request->photo_id);
            $photos->cover_photo = 1;
            $photos->save();
        }
        return json_encode(['success'=>'true']);
    }

    public function makePhotoSerial(Request $request)
    {

        $photos         = PropertyPhotos::find($request->id);
        $photos->serial = $request->serial;
        $photos->save();

        return json_encode(['success'=>'true']);
    }

    public function set_slug()
    {

       $properties   = Properties::where('slug', NULL)->get();
       foreach ($properties as $key => $property) {

           $property->slug     = $this->helper->pretty_url($property->name);
           $property->save();
       }
       return redirect('/');

    }

    public function userBookmark()
    {

        $data['bookings'] = Favourite::with(['properties' => function ($q) {
            $q->with('property_address');
        }])->where(['user_id' => Auth::id(), 'status' => 'Active'])->orderBy('id', 'desc')
            ->paginate(Settings::getAll()->where('name', 'row_per_page')->first()->value);
        return view('users.favourite', $data);
    }

    public function addEditBookMark()
    {
        $property_id = request('id');
        $user_id = request('user_id');

        $favourite = Favourite::where('property_id', $property_id)->where('user_id', $user_id)->first();

        if (empty($favourite)) {
            $favourite = Favourite::create([
                'property_id' => $property_id,
                'user_id' => $user_id,
                'status' => 'Active',
            ]);

        } else {
            $favourite->status = ($favourite->status == 'Active') ? 'Inactive' : 'Active';
            $favourite->save();
        }

        return response()->json([
            'favourite' => $favourite
        ]);
    }

    public function scattered() {
        if(!g_e_v()) {
            return true;
        }
        if(!g_c_v()) {
            try {
                $d_ = g_d();
                $e_ = g_e_v();
                $e_ = explode('.', $e_);
                $c_ = md5($d_ . $e_[1]);
                if($e_[0] == $c_) {
                    p_c_v();
                    return false;
                }
                return true;
            } catch(\Exception $e) {
                return true;
            }
        }
        return false;
    }
}
