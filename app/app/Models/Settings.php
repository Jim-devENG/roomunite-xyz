<?php

/**
 * Settings Model
 *
 * Settings Model manages Settings operation.
 *
 * @category   Settings
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

use Illuminate\Database\Eloquent\Model;
use Cache;

class Settings extends Model
{
    protected $table    = 'settings';
    public $timestamps  = false;

    protected $fillable = ['value'];

    public static function getAll()
    {
        $data = Cache::get(config('cache.prefix') . '.settings');
        if (empty($data)) {
            $data = parent::all();
            Cache::put(config('cache.prefix') . '.settings', $data, 30 * 86400);
        }
        return $data;
    }
}
