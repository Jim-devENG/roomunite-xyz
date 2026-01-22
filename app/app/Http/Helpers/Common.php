<?php
namespace App\Http\Helpers;

use Symfony\Component\HttpFoundation\Response;
use View;
use Session;
use App\Models\Meta;
use App\Models\Notification;
use App\Models\Permissions;
use App\Models\RoleAdmin;
use App\Models\PermissionRole;
use App\Models\Properties;
use App\Models\PropertyDates;
use App\Models\PropertyPrice;
use App\Models\PropertyFees;
use App\Models\penalty;
use App\Models\Currency;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class Common {

    function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
    }

    function d($var,$a=false)
    {
          echo "<pre>";
          print_r($var);
          echo "</pre>";
          if($a)exit;
    }

    public function content_read($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        // Add timeout protection to prevent hanging
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5 second connection timeout

        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        // Return false on error instead of hanging
        if ($data === false || !empty($error)) {
            return false;
        }

        return $data;
    }

    public function one_time_message($class, $message)
    {

        if ($class == 'error') $class = 'danger';
        Session::flash('alert-class', 'alert-'.$class);
        Session::flash('message', $message);
    }

    public static function key_value($key, $value, $ar)
    {
        $ret = [];
        foreach($ar as $k => $v) {
            $ret[$v[$key]] = $v[$value];
        }
        return $ret;
    }

    public function current_action($route)
    {
        $current = explode('@', $route);
        View::share('current_action',$current[1]);
    }

    public static function has_permission($user_id, $permissions = '')
    {
        $permissions      = explode('|', $permissions);
        $user_permissions = Permissions::getAll()->whereIn('name', $permissions);
        $permission_id = [];
        $i = 0;
        foreach ($user_permissions as $value) {
            $permission_id[$i++] = $value->id;
        }
        $role = RoleAdmin::getAll()->where('admin_id', $user_id)->first();

        if (count($permission_id) && isset($role->role_id)) {
            $has_permit = PermissionRole::where('role_id', $role->role_id)->whereIn('permission_id', $permission_id);
            return $has_permit->count();
        }
        else return 0;
    }

    public static function meta($url, $field)
    {
        $metas = Meta::where('url', $url);

        if($metas->count())
            return $metas->first()->$field;
        else if($field == 'title')
            return 'Page Not Found';
        else
            return '';
    }

    public function vrCacheForget($key)
    {
        Cache::forget($key);
    }

    function backup_tables($host,$user,$pass,$name,$tables = '*')
    {
        try {
            $con = mysqli_connect($host,$user,$pass,$name);
        } catch (Exception $e) {

        }

        if (mysqli_connect_errno()) {
            $this->one_time_message('danger', "Failed to connect to MySQL: ".mysqli_connect_error());
            return 0;
        }

        if ($tables == '*') {
             $tables = array();
             $result = mysqli_query($con, 'SHOW TABLES');
            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        $return = '';
        foreach($tables as $table) {
            $result = mysqli_query($con, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);


            $row2 = mysqli_fetch_row(mysqli_query($con, 'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row2[1]).";\n\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j < ($num_fields-1)) { $return.= ','; }
                    }
                    $return.= ");\n";
                }
            }

            $return.="\n\n\n";
        }

        $backup_name = date('Y-m-d-His').'.sql';

        $handle = fopen(storage_path("db-backups").'/'.$backup_name,'w+');
        fwrite($handle,$return);
        fclose($handle);

        return $backup_name;
    }

    public function add_notification($user_id, $message)
    {
        $notification = new Notification;
        $notification->user_id = $user_id;
        $notification->message = $message;
        $notification->status = 'unread';
        $notification->save();
    }

    public static function thousandsCurrencyFormat($num)
    {
      if($num < 1000) return $num;
      $x = round($num);
      $x_number_format = number_format($x);
      $x_array = explode(',', $x_number_format);
      $x_parts = array('k', 'm', 'b', 't');
      $x_count_parts = count($x_array) - 1;
      $x_display = $x;
      $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
      $x_display .= $x_parts[$x_count_parts - 1];
      return $x_display;
    }

    public function senitize($val)
    {
        $inp = trim($val);
        $inp = strip_tags($inp);
        $inp = htmlspecialchars($inp);
        return $inp;
    }

    public function pretty_url($str)
    {
        $url = $this->convert_to_url_friendly($str);
        $turl = $url;
        $i = 0;
        while(1){
            $i++;
            $cnt = Properties::where('slug', $turl)->count();
            if($cnt != 0)
                $turl = $url.'-'.$i;
            else break;
        }
        return $turl;
    }

    public function convert_to_url_friendly($str, $replace=array(), $delimiter='-')
    {
        if( !empty($replace) ) {
            $str = str_replace((array)$replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public function currency_rate($from, $to)
    {
        $from_rate = Currency::getAll()->firstWhere('code',$from)->rate;
        $to_rate   = Currency::getAll()->firstWhere('code',$to)->rate;

        $rate = round($from_rate / $to_rate, 6);
        if((int) $rate)
            return $rate;
        return round($from_rate / $to_rate, 6);
    }

    public function convert_currency($from = '', $to = '', $price)
    {
        $from = currentCurrency($from);
        $to = currentCurrency($to);
        $price       = str_replace(']','',$price);//For Php Version 7.2
        $base_amount = (float) $price / $from->rate;

        return round($base_amount * $to->rate, $to->rate > 1000 ? 0 : 2);
    }

    public function getCurrentCurrencySymbol() {
        return Session::get('currency') ? Currency::code_to_symbol(Session::get('currency'))
            : Currency::getAll()->firstWhere('default', 1)->symbol;
    }
    public function getCurrentCurrencyCode() {
        return Session::get('currency') ?? Currency::getAll()->firstWhere('default', 1)->code;
    }

    public function getCurrentCurrency() {
        return Session::get('currency') ? Currency::firstWhere('code', Session::get('currency'))
            : Currency::getAll()->firstWhere('default', 1);
    }

    public function getPrice($propertyId, $checkIn, $checkOut, $guestCount, $force = false)
    {
        $checkIn = setDateForDb($checkIn);
        $checkOut = setDateForDb($checkOut);
        $from = new DateTime($checkIn);
        $to = new DateTime($checkOut);
        $date1 = date('Y-m-d', strtotime($checkIn));
        $enddate = date('Y-m-d', strtotime($checkOut));
        $date2 = date('Y-m-d', (strtotime('-1 day', strtotime($enddate))));
        $totalNights = date_diff($from, $to)->format('%a');
        $dates = $this->get_days($date1, $date2);
        $propertyDates = PropertyDates::where(['property_id' => $propertyId])->whereIn('date', $dates)->get();
        if ($propertyDates->where('status', 'Not available')->count() > 0 && !$force) {
            $result['status'] = "Not available";
            return json_encode($result);
        }
        $property = Properties::with('property_price')->firstWhere('id', $propertyId);
        $result['property_default'] = [
            'price' => $property->property_price->price,
            'currency_code' => $property->property_price->currency_code,
            'symbol' => Currency::getAll()->firstWhere('code', $property->property_price->currency_code)->symbol,
            'rate' => Currency::getAll()->firstWhere('code', $property->property_price->currency_code)->rate,
            'local_to_propertyRate' => $this->currency_rate($property->property_price->currency_code, $this->getCurrentCurrencyCode()),
        ];
        $differentPriceDatePrice = $force ? $propertyDates->pluck('price', 'date')->toArray()
            : $propertyDates->where('status', 'Available')->pluck('price', 'date')->toArray();
        $differentPrice = 0;
        $weekendPrice = 0;
        $nightPrice = 0;
        $monthPrice = 0;
        $weekPrice = 0;
        $countDays = 0;
        $countWeeks = 0;
        $countMonths = 0;


        currentCurrency()->rate > 1000 ? $decimals = 0 : $decimals = 2;

        $symbol = $this->getCurrentCurrencySymbol();

        foreach ($dates as $date) {
            if (in_array($date, array_keys($differentPriceDatePrice))) {
                $differentPrice += $this->convert_currency($property->property_price->currency_code, '', $differentPriceDatePrice[$date]);
            } else if (date('N', strtotime($date)) == 5 && $property->property_price->weekend_price != 0) {
                $weekendPrice += $property->property_price->weekend_price;
            } else if (date('N', strtotime($date)) == 6 && $property->property_price->weekend_price != 0) {
                $weekendPrice += $property->property_price->weekend_price;
            } else {
                $nightPrice += $property->property_price->price;
            }
            $countDays++;
            if ($countDays % 30 == 0 && $property->property_price->monthly_discount != 0) {
                $countMonths++;
                $countWeeks = 0;
                $weekPrice = 0;
                $temp_total = $differentPrice + $weekendPrice + $nightPrice;
                $monthPrice = $temp_total;
            } else if (($countDays - ($countMonths * 28)) % 7 == 0 && $property->property_price->weekly_discount != 0) {
                $countWeeks++;
                $temp_total = $differentPrice + $weekendPrice + $nightPrice;
                $weekPrice = $temp_total - $monthPrice;
            }
        }

        foreach ($dates as $key => $value) {

            if (in_array($value, array_keys($differentPriceDatePrice))) {
                $allDate[$key]['price'] = moneyFormat($symbol, numberFormat($this->convert_currency($property->property_price->currency_code, '', $differentPriceDatePrice[$value]), $decimals));
                $allDate[$key]['original_price'] = $this->convert_currency($property->property_price->currency_code, '', $differentPriceDatePrice[$value]);

            } else if (date('N', strtotime($value)) == 5 && $property->property_price->weekend_price != 0) {

                $allDate[$key]['price'] = moneyFormat($symbol, numberFormat($property->property_price->weekend_price, $decimals));
                $allDate[$key]['original_price'] = $property->property_price->weekend_price;

            } else if (date('N', strtotime($value)) == 6 && $property->property_price->weekend_price != 0) {

                $allDate[$key]['price'] = moneyFormat($symbol, numberFormat($property->property_price->weekend_price, $decimals));

                $allDate[$key]['original_price'] = $property->property_price->weekend_price;

            } else {

                $allDate[$key]['price'] = moneyFormat($symbol, numberFormat($property->property_price->price, $decimals));
                $allDate[$key]['original_price'] = $property->property_price->price;

            }

            $allDate[$key]['date'] = onlyFormat($value);

        }

        $result['date_with_price'] = $allDate;

        $minDayStay = PropertyDates::where(['property_id' => $propertyId])->whereIn('date', $dates)->where('min_stay', 1)->max('min_day');

        if ($minDayStay) {
            if ($countDays < $minDayStay) {
                $result['status'] = 'minimum stay';
                $result['minimum'] = $minDayStay;
                return json_encode($result);
            }
        }
        

        $propertyFees = PropertyFees::pluck('value', 'field');
        $remainingDayPrice = ($differentPrice + $weekendPrice + $nightPrice) - $monthPrice - $weekPrice;
        
        $weeks_dec = $countDays / 7;
        $weeks_val = ceil($weeks_dec);
        
        $tot_week = $weeks_val * $property->property_price->price;
        
        Log::info("WEEK PRICE");
        Log::info($property->property_price->price);
        Log::info("WEEK COUNT");
        Log::info($weeks_val);
        Log::info("WEEK TOTAL");
        Log::info($tot_week);
        
        $firstDate = date('Y-m-d', strtotime($checkIn));
       // $date2 = date('Y-m-d', (strtotime('-1 day', strtotime($enddate))));
        
        $result['week_price_decimal'] = round($property->property_price->price, 2);
        

        $result['total_night_price'] = $remainingDayPrice + $monthPrice + $weekPrice;
        $result['week_data_total_price'] = $tot_week;
        
        $result['discount'] = round(($monthPrice * $property->property_price->monthly_discount) / 100, $decimals) + round(($weekPrice * $property->property_price->weekly_discount) / 100, $decimals);
        $result['property_price'] = round($result['total_night_price'] / $countDays, $decimals);
        $result['total_nights'] = $countDays;
        $result['total_weeks'] = $weeks_val;
        
        $result['service_fee'] = round(($propertyFees['guest_service_charge'] / 100) * $result['total_night_price'], $decimals);
        $result['damages_insurance'] = round($propertyFees['damages_insurance'], $decimals);
        $result['host_fee'] = round(($propertyFees['host_service_charge'] / 100) * $result['total_night_price'], $decimals);
        $result['iva_tax'] = round(($propertyFees['iva_tax'] / 100) * ($result['total_night_price'] - $result['discount']), $decimals);
        $result['accomodation_tax'] = round(($propertyFees['accomodation_tax'] / 100) * ($result['total_night_price'] - $result['discount']), $decimals);
        
        
        /***** Weekly Data  *****/
        $result['week_data_discount'] = round(($tot_week * $property->property_price->weekly_discount) / 100, $decimals);
        
        $result['week_data_service_fee'] = round(($propertyFees['guest_service_charge'] / 100) * $tot_week, $decimals);
        
        $result['week_data_host_fee'] = round(($propertyFees['host_service_charge'] / 100) * $tot_week, $decimals);
        
        $result['week_data_accomodation_tax'] = round(($propertyFees['accomodation_tax'] / 100) * ($tot_week - $result['week_data_discount']), $decimals);
        
        //Log::info("ACCOMMODATION TAX");
       // Log::info($propertyFees['accomodation_tax']);
        
        $result['additional_guest'] = 0;
        $result['week_data_additional_guest'] = 0;
        $result['security_fee'] = 0;
        $result['cleaning_fee'] = 0;

        if ($guestCount > $property->property_price->guest_after) {
            $additional_guest_count = $guestCount - $property->property_price->guest_after;
            $result['additional_guest'] = $additional_guest_count * $property->property_price->guest_fee * $countDays;
            
            $result['week_data_additional_guest'] = $additional_guest_count * $property->property_price->guest_fee * $weeks_val;
        }

        if ($property->property_price->security_fee)
            $result['security_fee'] = $property->property_price->security_fee;

        if ($property->property_price->cleaning_fee)
            $result['cleaning_fee'] = $property->property_price->cleaning_fee;

/*$result['total'] = $result['service_fee'] + $result['total_night_price'] + $result['additional_guest']
            + $result['damages_insurance'] + $result['cleaning_fee'] + $result['iva_tax'] + $result['accomodation_tax'];
            
            */
            
        /*$result['total'] = $result['service_fee'] + $result['total_night_price'] + $result['additional_guest']
            + $result['damages_insurance'] + $result['cleaning_fee'] + $result['security_fee'] + $result['accomodation_tax'] - $result['discount']; */
            
        $result['total'] = $result['service_fee'] + $result['total_night_price'] + $result['additional_guest']
            + $result['damages_insurance'] + $result['cleaning_fee'] + $result['accomodation_tax'] - $result['discount'];
            
        $result['week_data_total'] = $result['week_data_service_fee'] + $result['week_data_total_price'] + $result['week_data_additional_guest']
            + $result['damages_insurance'] + $result['cleaning_fee'] + $result['week_data_accomodation_tax'] - $result['week_data_discount'];
            
        $result['subtotal'] = $result['total'];
        
        $result['week_data_subtotal'] = $result['week_data_total'];

        $result['total_night_price_with_symbol'] = moneyFormat($symbol, numberFormat($result['total_night_price'], $decimals));
        
        $result['week_data_total_price_with_symbol'] = moneyFormat($symbol, numberFormat($result['week_data_total_price'], 2));
        
        $result['service_fee_with_symbol'] = moneyFormat($symbol, numberFormat($result['service_fee'], $decimals));
        
        $result['week_data_service_fee_with_symbol'] = moneyFormat($symbol, numberFormat($result['week_data_service_fee'], 2));
        
         $result['damages_insurance_with_symbol'] = moneyFormat($symbol, numberFormat($result['damages_insurance'], $decimals));
         
        $result['total_with_symbol'] = moneyFormat($symbol, numberFormat($result['total'], $decimals));
        
        $result['week_data_total_with_symbol'] = moneyFormat($symbol, numberFormat($result['week_data_total'], 2));
        
        $result['iva_tax_with_symbol'] = moneyFormat($symbol, numberFormat($result['iva_tax'], $decimals));
        
        $result['accomodation_tax_with_symbol'] = moneyFormat($symbol, numberFormat($result['accomodation_tax'], $decimals));
        
        $result['week_data_accomodation_tax_with_symbol'] = moneyFormat($symbol, numberFormat($result['week_data_accomodation_tax'], 2));
        
        $result['additional_guest_fee_with_symbol'] = moneyFormat($symbol, numberFormat($result['additional_guest'], $decimals));
        
        $result['week_data_additional_guest_fee_with_symbol'] = moneyFormat($symbol, numberFormat($result['week_data_additional_guest'], 2));
        
        $result['security_fee_with_symbol'] = moneyFormat($symbol, numberFormat($result['security_fee'], $decimals));
        
        $result['cleaning_fee_with_symbol'] = moneyFormat($symbol, numberFormat($result['cleaning_fee'], $decimals));
        
        $result['per_night_price_with_symbol'] = moneyFormat($symbol, numberFormat($result['total_night_price'] / $countDays, $decimals));
        
        $result['per_week_price_with_symbol'] = moneyFormat($symbol, numberFormat($property->property_price->price, 2));
        
        $result['discount_with_symbol'] = moneyFormat($symbol, numberFormat($result['discount'], 2));
        
        $result['week_data_discount_with_symbol'] = moneyFormat($symbol, numberFormat($result['week_data_discount'], 2));
        
        
        $result['weekly_total_price'] = $result['week_data_total'] / $weeks_val;
        
        $result['weekly_total_price_with_symbol'] = moneyFormat($symbol, numberFormat($result['weekly_total_price'], 2));
        
        $result['currency'] = $this->getCurrentCurrencyCode();
        
        for ($i = 0; $i < $weeks_val; $i++) {

            $row = ["week" => $i + 1, "price" => $result['week_price_decimal']];
            $weekly_tot_price = $result['week_data_total'] / $weeks_val;
            $rowDates = ["week" => $i + 1, "date" => $firstDate, "price" => round($weekly_tot_price, 2)];
            
            $payDates = date('Y-m-d', (strtotime('+5 day', strtotime($firstDate))));
            $firstDate = $payDates;
            
            $allWeeks[$i] = $row;
            $paymentDates[$i] = $rowDates;
        
        }
        
        Log::info("WEEKS PAY DATES");
        Log::info($paymentDates);
        
        $result['weeks_with_price'] = $allWeeks;
        $result['weeks_with_dates'] = $paymentDates;
        

        return json_encode($result);
    }

    public function get_days($startDate, $endDate)
    {
        $days []     = $startDate;
        $startDate   = is_numeric($startDate) ? $startDate : strtotime($startDate);
        $endDate     = is_numeric($endDate) ? $endDate : strtotime($endDate);

        $startDate   = gmdate("Y-m-d", $startDate);
        $endDate     = gmdate("Y-m-d", $endDate);
        $currentDate = $startDate;
        while($currentDate < $endDate) {
            $currentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($currentDate)));
            $days[]      = $currentDate;
        }
        return $days;
    }

    public function y_m_d_convert($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function host_penalty_check($penalty, $booking_amount,$currency_code)
    {
        $penalty_id = '';
        $penalty_amnt = '';

        $penalty_sum = 0;
        if ($penalty->count() > 0 ) {

            $host_amount = $booking_amount;

            foreach ($penalty as $pen) {

                $host_amount = $this->convert_currency($currency_code,$pen->currency_code,$host_amount);

                $remaining_penalty = $pen->remaining_penalty;

                if ($host_amount > $remaining_penalty) {

                    $host_amount = $host_amount - $remaining_penalty ;

                    $penalty = Penalty::find($pen->id);
                    $penalty->remaining_penalty     = 0;
                    $penalty->status                = "Completed";
                    $penalty->save();

                    $penalty_id .= $pen->id.',';
                    $penalty_amnt .= $remaining_penalty.',';
                    $penalty_sum += $remaining_penalty;
                } else {

                    $amount_reamining = $remaining_penalty - $host_amount;

                    $penalty = Penalty::find($pen->id);

                    $penalty->remaining_penalty  = $amount_reamining;

                    $penalty->save();

                    $penalty_id .= $pen->id.',';
                    $penalty_amnt .= $host_amount.',';
                    $penalty_sum += $host_amount;
                    $host_amount = 0;
                }

                $host_amount = $this->convert_currency($pen->currency_code,$currency_code,$host_amount);
            }

            $penalty_amnt   = rtrim($penalty_amnt, ',');
            $penalty_id     = rtrim($penalty_id, ',');
        } else {
            $host_amount = $booking_amount;

            $penalty_id  = 0;
            $penalty_amnt = '';
            $penalty_sum = 0;
        }

        $result['host_amount']     = $host_amount;
        $result['penalty_ids']     = $penalty_id;
        $result['penalty_total']   = $penalty_sum;
        $result['panalty_amounts'] = $penalty_amnt;

        return $result;
    }

    function randomCode($length=20)
    {
        $var_num = 3;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num_set = '0123456789';
        $low_ch_set = 'abcdefghijklmnopqrstuvwxyz';
        $high_ch_set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';

        $randomString .= $num_set[rand(0, strlen($num_set) - 1)];
        $randomString .= $low_ch_set[rand(0, strlen($low_ch_set) - 1)];
        $randomString .= $high_ch_set[rand(0, strlen($high_ch_set) - 1)];

        for ($i = 0; $i < $length-$var_num; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        $randomString = str_shuffle($randomString);

        return $randomString;
    }

    public static function dateRange($startDate, $endDate, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates   = array();
        $current = strtotime($startDate);
        $endDate = strtotime($endDate);
        if ($current > $endDate) {
            return $dates;
        }
        while( $current <= $endDate ) {

            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public function uploadSingleFile($file, $path = 'public/uploads/') {
        $tmp_name     = $file["tmp_name"];
        $name         = str_replace(' ', '_', $file["name"]);
        $ext          = pathinfo($name, PATHINFO_EXTENSION);
        $name         = explode('.', $name)[0] .time().'.'.$ext;
        try {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            move_uploaded_file($tmp_name, $path . $name);
            return $name;
        } catch (\Exception $e) {
            return false;
        }
    }

}
