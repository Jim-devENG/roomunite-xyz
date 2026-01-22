# API Settings Documentation

## Location
**Admin Panel**: Settings → Api Credentials  
**URL**: `/admin/settings/api-informations`  
**Route**: `admin/settings/api-informations`  
**Controller**: `App\Http\Controllers\Admin\SettingsController@apiInformations`  
**View**: `resources/resources/views/admin/api_credentials.blade.php`

## Currently Configured APIs

### 1. Facebook OAuth API
- **Client ID**: Stored in `settings` table (type: `facebook`, name: `client_id`)
- **Client Secret**: Stored in `settings` table (type: `facebook`, name: `client_secret`)
- **Purpose**: Social login authentication
- **Usage**: Facebook login integration

### 2. Google OAuth API
- **Client ID**: Stored in `settings` table (type: `google`, name: `client_id`)
- **Client Secret**: Stored in `settings` table (type: `google`, name: `client_secret`)
- **Purpose**: Social login authentication
- **Usage**: Google login integration

### 3. Google Maps API
- **Browser Key**: Stored in `settings` table (type: `googleMap`, name: `key`)
- **Server Key**: Stored in `settings` table (type: `googleMap`, name: `server_key`) - Optional
- **Purpose**: Map display and location services
- **Usage**: Property location maps, address autocomplete

## Database Structure

All API credentials are stored in the `settings` table with the following structure:
- `name`: The setting name (e.g., `client_id`, `client_secret`, `key`)
- `value`: The actual API key/secret value
- `type`: The API provider type (`facebook`, `google`, `googleMap`)

## Form Fields

The API credentials form includes:
1. Facebook Client ID (required)
2. Facebook Client Secret (required)
3. Google Client ID (required)
4. Google Client Secret (required)
5. Google Map Browser Key (required)
6. Google Map Server Key (optional - not shown in form but handled in controller)

## Validation Rules

All fields are required except `google_map_server_key`:
- `facebook_client_id`: required
- `facebook_client_secret`: required
- `google_client_id`: required
- `google_client_secret`: required
- `google_map_key`: required

## How to Add New APIs

To add a new API configuration:

1. **Update the Controller** (`app/app/Http/Controllers/Admin/SettingsController.php`):
   - Add new API type to the GET method (line 537-541)
   - Add validation rules for new fields (line 543-549)
   - Add update logic in POST method (line 566-575)

2. **Update the View** (`resources/resources/views/admin/api_credentials.blade.php`):
   - Add new form fields to the `$form_data` array (line 9-15)
   - Add validation rules in JavaScript (line 23-41)

3. **Database**:
   - API credentials are automatically stored in the `settings` table
   - Use appropriate `type` value for the new API (e.g., `stripe`, `paypal`, `twilio`)

## Example: Adding Stripe API

```php
// In SettingsController@apiInformations GET method:
$data['stripe'] = Settings::where('type', 'stripe')->pluck('value', 'name')->toArray();

// In SettingsController@apiInformations POST method:
Settings::where(['name' => 'publishable_key', 'type' => 'Stripe'])->update(['value' => $request->stripe_publishable_key]);
Settings::where(['name' => 'secret_key', 'type' => 'Stripe'])->update(['value' => $request->stripe_secret_key]);
```

## Access Control

The API settings page requires the `api_informations` permission:
- **Permission**: `api_informations`
- **Middleware**: `permission:api_informations`
- **Check**: `Helpers::has_permission(Auth::guard('admin')->user()->id, 'api_informations')`

## Related Settings

Other API-related settings may be found in:
- **Email Settings**: `/admin/settings/email` - SMTP configuration
- **SMS Settings**: `/admin/settings/sms` - SMS gateway configuration
- **Payment Methods**: `/admin/settings/payment-methods` - Payment gateway APIs

