<?php

/**
 * User Model
 *
 * User Model manages User operation.
 *
 * @category   User
 * @package    vRent
 * @author     Techvillage Dev Team
 * @copyright  2020 Techvillage
 * @license
 * @version    2.7
 * @link       http://techvill.net
 * @since      Version 1.3
 * @deprecated None
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Http\Helpers\Common;
use App\Models\UserDetails;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['profile_src'];

    public function users_verification()
    {
        return $this->hasOne('App\Models\UsersVerification', 'user_id', 'id');
    }

    public function payouts()
    {
        return $this->hasMany('App\Models\Payouts', 'user_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Account', 'user_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\Bookings', 'user_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification', 'user_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'user_id', 'id');
    }


    public function user_details()
    {
        return $this->hasMany('App\Models\UserDetail', 'user_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'user_id', 'id');
    }

    public function withdraw()
    {
        return $this->hasMany('App\Models\Withdraw', 'user_id', 'id');
    }

    public function properties()
    {
        return $this->hasMany('App\Models\Properties', 'host_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Reviews', 'sender_id', 'id');
    }

    public function getProfileSrcAttribute()
    {
        $profileImage = $this->attributes['profile_image'] ?? '';
        if ($profileImage == '') {
            $src = asset('images/default-profile.png');
        } else {
            $src = asset('images/profile/'.$this->attributes['id'].'/'.$profileImage);
        }

        return $src;
    }

    public function details_key_value()
    {
        $details = UserDetails::where('user_id', $this->attributes['id'])->pluck('value', 'field');
        return $details;
    }

    public function getAccountSinceAttribute()
    {
        $since = date('F Y', strtotime($this->attributes['created_at']));
        return $since;
    }

    public function getFirstNameAttribute()
    {
        // Check if first_name exists in attributes
        if (isset($this->attributes['first_name'])) {
            return $this->attributes['first_name'];
        }
        
        // Try to get from UserDetails
        $details = $this->details_key_value();
        return $details['first_name'] ?? '';
    }

    public function getLastNameAttribute()
    {
        // Check if last_name exists in attributes
        if (isset($this->attributes['last_name'])) {
            return $this->attributes['last_name'];
        }
        
        // Try to get from UserDetails
        $details = $this->details_key_value();
        return $details['last_name'] ?? '';
    }

    public function getFullNameAttribute()
    {
        // Get first and last name
        $first_name = $this->first_name;
        $last_name = $this->last_name;
        
        // If both are empty, try to use 'name' field as fallback
        if (empty($first_name) && empty($last_name)) {
            $name = $this->attributes['name'] ?? '';
            if (!empty($name)) {
                return ucfirst($name);
            }
            return 'User';
        }
        
        // Build full name
        $first = !empty($first_name) ? ucfirst($first_name) : '';
        $last = !empty($last_name) ? ucfirst($last_name) : '';
        
        return trim($first . ' ' . $last);
    }
}
