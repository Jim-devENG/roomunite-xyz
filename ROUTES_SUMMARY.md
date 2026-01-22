# Laravel Routes Summary - RoomUnite Application

## Overview
This document provides a comprehensive summary of all routes defined in `routes/web.php`.

---

## Public Routes (No Authentication Required)

### Debug & Utility Routes
- `GET enable-debugger` → `HomeController@activateDebugger`
- `GET|POST create-users-wallet` → `HomeController@walletUser`
- `POST set_session` → `HomeController@setSession`

### Cron Jobs
- `GET cron/ical-synchronization` → `CronController@iCalendarSynchronization`

### Public Pages (with locale middleware)
- `GET /` → `HomeController@index`
- `POST search/result` → `SearchController@searchResult`
- `GET search` → `SearchController@index`
- `GET|POST properties/{slug}` → `PropertyController@single` (named: `property.single`)
- `GET|POST property/get-price` → `PropertyController@getPrice`
- `GET set-slug/` → `PropertyController@set_slug`
- `GET signup` → `LoginController@signup`
- `POST /checkUser/check` → `LoginController@check` (named: `checkUser.check`)

### Social Authentication Callbacks
- `GET googleAuthenticate` → `LoginController@googleAuthenticate`
- `GET facebookAuthenticate` → `LoginController@facebookAuthenticate`

### iCalendar Export
- `GET icalender/export/{id}` → `CalendarController@icalendarExport`

### Static Pages & Utilities
- `GET {name}` → `HomeController@staticPages` (catch-all for static pages)
- `POST duplicate-phone-number-check` → `UserController@duplicatePhoneNumberCheck`
- `POST duplicate-phone-number-check-for-existing-customer` → `UserController@duplicatePhoneNumberCheckForExistingCustomer`

---

## Admin Routes (Admin Authentication Required)

### Admin Authentication
- `GET admin/login` → `Admin\AdminController@login` (only if admin NOT logged in)
- `POST admin/authenticate` → `Admin\AdminController@authenticate`

### Admin Dashboard & Profile
- `GET admin/` → Redirects to `admin/dashboard`
- `GET admin/dashboard` → `Admin\DashboardController@index`
- `GET|POST admin/profile` → `Admin\AdminController@profile`
- `GET admin/logout` → `Admin\AdminController@logout`

### Customer Management
- `GET admin/customers` → `Admin\CustomerController@index` (permission: `customers`)
- `GET admin/customers/customer_search` → `Admin\CustomerController@searchCustomer` (permission: `customers`)
- `POST admin/add-ajax-customer` → `Admin\CustomerController@ajaxCustomerAdd` (permission: `add_customer`)
- `GET|POST admin/add-customer` → `Admin\CustomerController@add` (permission: `add_customer`)

#### Customer Details (permission: `edit_customer`)
- `GET|POST admin/edit-customer/{id}` → `Admin\CustomerController@update`
- `GET admin/customer/properties/{id}` → `Admin\CustomerController@customerProperties`
- `GET admin/customer/bookings/{id}` → `Admin\CustomerController@customerBookings`
- `POST admin/customer/bookings/property_search` → `Admin\BookingsController@searchProperty`
- `GET admin/customer/payouts/{id}` → `Admin\CustomerController@customerPayouts`
- `GET admin/customer/payment-methods/{id}` → `Admin\CustomerController@paymentMethods`
- `GET admin/customer/wallet/{id}` → `Admin\CustomerController@customerWallet`

#### Customer Exports (permission: `edit_customer`)
- `GET admin/customer/properties/{id}/property_list_csv` → `Admin\PropertiesController@propertyCsv`
- `GET admin/customer/properties/{id}/property_list_pdf` → `Admin\PropertiesController@propertyPdf`
- `GET admin/customer/bookings/{id}/booking_list_csv` → `Admin\BookingsController@bookingCsv`
- `GET admin/customer/bookings/{id}/booking_list_pdf` → `Admin\BookingsController@bookingPdf`
- `GET admin/customer/payouts/{id}/payouts_list_pdf` → `Admin\PayoutsController@payoutsPdf`
- `GET admin/customer/payouts/{id}/payouts_list_csv` → `Admin\PayoutsController@payoutsCsv`
- `GET admin/customer/customer_list_csv` → `Admin\CustomerController@customerCsv`
- `GET admin/customer/customer_list_pdf` → `Admin\CustomerController@customerPdf`

### Messaging (permission: `manage_messages`)
- `GET admin/messages` → `Admin\AdminController@customerMessage`
- `GET|POST admin/delete-message/{id}` → `Admin\AdminController@deleteMessage`
- `GET|POST admin/send-message-email/{id}` → `Admin\AdminController@sendEmail`
- `GET|POST admin/upload_image` → `Admin\AdminController@uploadImage` (named: `upload`)
- `GET admin/messaging/host/{id}` → `Admin\AdminController@hostMessage`
- `POST admin/reply/{id}` → `Admin\AdminController@reply`

### Property Management
- `GET admin/properties` → `Admin\PropertiesController@index` (permission: `properties`)
- `GET|POST admin/add-properties` → `Admin\PropertiesController@add` (permission: `add_properties`)
- `GET admin/properties/property_list_csv` → `Admin\PropertiesController@propertyCsv`
- `GET admin/properties/property_list_pdf` → `Admin\PropertiesController@propertyPdf`

#### Property Editing (permission: `edit_properties`)
- `GET|POST admin/listing/{id}/photo_message` → `Admin\PropertiesController@photoMessage`
- `GET|POST admin/listing/{id}/photo_delete` → `Admin\PropertiesController@photoDelete`
- `GET|POST admin/listing/{id}/update_status` → `Admin\PropertiesController@update_status`
- `POST admin/listing/photo/make_default_photo` → `Admin\PropertiesController@makeDefaultPhoto`
- `POST admin/listing/photo/make_photo_serial` → `Admin\PropertiesController@makePhotoSerial`
- `GET|POST admin/listing/{id}/{step}` → `Admin\PropertiesController@listing` (steps: basics|description|location|amenities|photos|pricing|calendar|details|booking)

#### Property Actions
- `GET|POST admin/edit_property/{id}` → `Admin\PropertiesController@update` (permission: `edit_properties`)
- `GET admin/delete-property/{id}` → `Admin\PropertiesController@delete` (permission: `delete_property`)

### Calendar Management
- `POST admin/ajax-calender/{id}` → `Admin\CalendarController@calenderJson`
- `POST admin/ajax-calender-price/{id}` → `Admin\CalendarController@calenderPriceSet`
- `POST admin/ajax-icalender-import/{id}` → `Admin\CalendarController@icalendarImport`
- `GET admin/icalendar/synchronization/{id}` → `Admin\CalendarController@icalendarSynchronization`

### Booking Management (permission: `manage_bookings`)
- `GET admin/bookings` → `Admin\BookingsController@index`
- `GET admin/bookings/property_search` → `Admin\BookingsController@searchProperty`
- `GET admin/bookings/customer_search` → `Admin\BookingsController@searchCustomer`
- `GET admin/bookings/detail/{id}` → `Admin\BookingsController@details`
- `GET admin/bookings/edit/{req}/{id}` → `Admin\BookingsController@updateBookingStatus`
- `POST admin/bookings/pay` → `Admin\BookingsController@pay`
- `GET admin/booking/need_pay_account/{id}/{type}` → `Admin\BookingsController@needPayAccount`
- `GET admin/booking/booking_list_csv` → `Admin\BookingsController@bookingCsv`
- `GET admin/booking/booking_list_pdf` → `Admin\BookingsController@bookingPdf`

### Payout Management (permission: `view_payouts`)
- `GET admin/payouts` → `Admin\PayoutsController@index`
- `GET|POST admin/payouts/edit/{id}` → `Admin\PayoutsController@edit`
- `GET admin/payouts/details/{id}` → `Admin\PayoutsController@details`
- `GET admin/payouts/payouts_list_pdf` → `Admin\PayoutsController@payoutsPdf`
- `GET admin/payouts/payouts_list_csv` → `Admin\PayoutsController@payoutsCsv`

### Reviews Management (permission: `manage_reviews`)
- `GET admin/reviews` → `Admin\ReviewsController@index`
- `GET|POST admin/edit_review/{id}` → `Admin\ReviewsController@edit`
- `GET admin/reviews/review_search` → `Admin\ReviewsController@searchReview`
- `GET admin/reviews/review_list_csv` → `Admin\ReviewsController@reviewCsv`
- `GET admin/reviews/review_list_pdf` → `Admin\ReviewsController@reviewPdf`

### Reports (permission: `view_reports`)
- `GET admin/sales-report` → `Admin\ReportsController@salesReports`
- `GET admin/sales-analysis` → `Admin\ReportsController@salesAnalysis`
- `GET admin/reports/property-search` → `Admin\ReportsController@searchProperty`
- `GET admin/overview-stats` → `Admin\ReportsController@overviewStats`

### Amenities Management (permission: `manage_amenities`)
- `GET admin/amenities` → `Admin\AmenitiesController@index`
- `GET|POST admin/add-amenities` → `Admin\AmenitiesController@add`
- `GET|POST admin/edit-amenities/{id}` → `Admin\AmenitiesController@update`
- `GET admin/delete-amenities/{id}` → `Admin\AmenitiesController@delete`

### Pages Management (permission: `manage_pages`)
- `GET admin/pages` → `Admin\PagesController@index`
- `GET|POST admin/add-page` → `Admin\PagesController@add`
- `GET|POST admin/edit-page/{id}` → `Admin\PagesController@update`
- `GET admin/edit-page-ajax/{id}` → `Admin\PagesController@editPageAjax` (named: `adminEditPageAjax`)
- `GET admin/delete-page/{id}` → `Admin\PagesController@delete`
- `GET|POST admin/upload_image` → `Admin\PagesController@uploadImage` (named: `upload`)

### Admin Users Management (permission: `manage_admin`)
- `GET admin/admin-users` → `Admin\AdminController@index`
- `GET|POST admin/add-admin` → `Admin\AdminController@add`
- `GET|POST admin/edit-admin/{id}` → `Admin\AdminController@update`
- `GET|POST admin/delete-admin/{id}` → `Admin\AdminController@delete`

### Settings (permission: `general_setting`)
- `GET|POST admin/settings` → `Admin\SettingsController@general`
- `GET|POST admin/settings/preferences` → `Admin\SettingsController@preferences` (permission: `preference`)
- `GET admin/settings/system` → `Admin\SettingsController@system`
- `POST admin/settings/delete_logo` → `Admin\SettingsController@deleteLogo`
- `POST admin/settings/delete_favicon` → `Admin\SettingsController@deleteFavIcon`
- `GET|POST admin/settings/fees` → `Admin\SettingsController@fees` (permission: `manage_fees`)
- `GET|POST admin/settings/email` → `Admin\SettingsController@email` (permission: `email_settings`)
- `GET|POST admin/settings/api-informations` → `Admin\SettingsController@apiInformations` (permission: `api_informations`)
- `GET|POST admin/settings/payment-methods` → `Admin\SettingsController@paymentMethods` (permission: `payment_settings`)
- `GET|POST admin/settings/social-links` → `Admin\SettingsController@socialLinks` (permission: `social_links`)
- `GET|POST admin/settings/social-logins` → `Admin\SettingsController@socialLogin` (permission: `social_logins`)
- `GET|POST admin/settings/sms` → `Admin\SettingsController@smsSettings`

#### Banners (permission: `manage_banners`)
- `GET admin/settings/banners` → `Admin\BannersController@index`
- `GET|POST admin/settings/add-banners` → `Admin\BannersController@add`
- `GET|POST admin/settings/edit-banners/{id}` → `Admin\BannersController@update`
- `GET admin/settings/delete-banners/{id}` → `Admin\BannersController@delete`

#### Starting Cities (permission: `starting_cities_settings`)
- `GET admin/settings/starting-cities` → `Admin\StartingCitiesController@index`
- `GET|POST admin/settings/add-starting-cities` → `Admin\StartingCitiesController@add`
- `GET|POST admin/settings/edit-starting-cities/{id}` → `Admin\StartingCitiesController@update`
- `GET admin/settings/delete-starting-cities/{id}` → `Admin\StartingCitiesController@delete`

#### Property Types (permission: `manage_property_type`)
- `GET admin/settings/property-type` → `Admin\PropertyTypeController@index`
- `GET|POST admin/settings/add-property-type` → `Admin\PropertyTypeController@add`
- `GET|POST admin/settings/edit-property-type/{id}` → `Admin\PropertyTypeController@update`
- `GET admin/settings/delete-property-type/{id}` → `Admin\PropertyTypeController@delete`

#### Space Types (permission: `space_type_setting`)
- `GET admin/settings/space-type` → `Admin\SpaceTypeController@index`
- `GET|POST admin/settings/add-space-type` → `Admin\SpaceTypeController@add`
- `GET|POST admin/settings/edit-space-type/{id}` → `Admin\SpaceTypeController@update`
- `GET admin/settings/delete-space-type/{id}` → `Admin\SpaceTypeController@delete`

#### Bed Types (permission: `manage_bed_type`)
- `GET admin/settings/bed-type` → `Admin\BedTypeController@index`
- `GET|POST admin/settings/add-bed-type` → `Admin\BedTypeController@add`
- `GET|POST admin/settings/edit-bed-type/{id}` → `Admin\BedTypeController@update`
- `GET admin/settings/delete-bed-type/{id}` → `Admin\BedTypeController@delete`

#### Currency (permission: `manage_currency`)
- `GET admin/settings/currency` → `Admin\CurrencyController@index`
- `GET|POST admin/settings/add-currency` → `Admin\CurrencyController@add`
- `GET|POST admin/settings/edit-currency/{id}` → `Admin\CurrencyController@update`
- `GET admin/settings/delete-currency/{id}` → `Admin\CurrencyController@delete`

#### Country (permission: `manage_country`)
- `GET admin/settings/country` → `Admin\CountryController@index`
- `GET|POST admin/settings/add-country` → `Admin\CountryController@add`
- `GET|POST admin/settings/edit-country/{id}` → `Admin\CountryController@update`
- `GET admin/settings/delete-country/{id}` → `Admin\CountryController@delete`

#### Amenities Types (permission: `manage_amenities_type`)
- `GET admin/settings/amenities-type` → `Admin\AmenitiesTypeController@index`
- `GET|POST admin/settings/add-amenities-type` → `Admin\AmenitiesTypeController@add`
- `GET|POST admin/settings/edit-amenities-type/{id}` → `Admin\AmenitiesTypeController@update`
- `GET admin/settings/delete-amenities-type/{id}` → `Admin\AmenitiesTypeController@delete`

#### Language (permission: `manage_language`)
- `GET admin/settings/language` → `Admin\LanguageController@index`
- `GET|POST admin/settings/add-language` → `Admin\LanguageController@add`
- `GET|POST admin/settings/edit-language/{id}` → `Admin\LanguageController@update`
- `GET admin/settings/delete-language/{id}` → `Admin\LanguageController@delete`

#### Metas (permission: `manage_metas`)
- `GET admin/settings/metas` → `Admin\MetasController@index`
- `GET|POST admin/settings/edit_meta/{id}` → `Admin\MetasController@update`

#### Bank Management (permission: `payment_settings`)
- `GET|POST admin/settings/bank/add` → `Admin\BankController@addBank`
- `GET|POST admin/settings/bank/edit/{bank}` → `Admin\BankController@editBank`
- `GET admin/settings/bank/{bank}` → `Admin\BankController@show`
- `GET admin/settings/bank/delete/{bank}` → `Admin\BankController@deleteBank`

#### Roles (permission: `manage_roles`)
- `GET admin/settings/roles` → `Admin\RolesController@index`
- `GET|POST admin/settings/add-role` → `Admin\RolesController@add`
- `GET|POST admin/settings/edit-role/{id}` → `Admin\RolesController@update`
- `GET admin/settings/delete-role/{id}` → `Admin\RolesController@delete`

#### Database Backup (permission: `database_backup`)
- `GET admin/settings/backup` → `Admin\BackupController@index`
- `GET admin/backup/save` → `Admin\BackupController@add`
- `GET admin/backup/download/{id}` → `Admin\BackupController@download`

#### Email Templates (permission: `manage_email_template`)
- `GET admin/email-template/{id}` → `Admin\EmailTemplateController@index`
- `POST admin/email-template/{id}` → `Admin\EmailTemplateController@update`

#### Testimonials (permission: `manage_testimonial`)
- `GET admin/testimonials` → `Admin\TestimonialController@index`
- `GET|POST admin/add-testimonials` → `Admin\TestimonialController@add`
- `GET|POST admin/edit-testimonials/{id}` → `Admin\TestimonialController@update`
- `GET admin/delete-testimonials/{id}` → `Admin\TestimonialController@delete`

---

## User Authentication Routes (Only if NOT logged in)

### Login & Registration
- `GET login` → `LoginController@index`
- `GET auth/login` → Redirects to `login`
- `GET register` → `HomeController@register`
- `POST create` → `UserController@create`
- `POST authenticate` → `LoginController@authenticate`
- `GET|POST forgot_password` → `LoginController@forgotPassword`
- `GET users/reset_password/{secret?}` → `LoginController@resetPassword`
- `POST users/reset_password` → `LoginController@resetPassword`

### Social Login
- `GET googleLogin` → `LoginController@googleLogin` (middleware: `social_login:google_login`)
- `GET facebookLogin` → `LoginController@facebookLogin` (middleware: `social_login:facebook_login`)

### User Verification
- `POST users/veriff-complete` → `UserController@identificationVeriffComplete`
- `POST users/veriff-process` → `UserController@identificationVeriffProcess`

---

## User Routes (User Authentication Required)

### Dashboard & Profile
- `GET dashboard` → `UserController@dashboard`
- `GET|POST users/profile` → `UserController@profile`
- `GET|POST users/profile/media` → `UserController@media`
- `GET|POST users/account-preferences` → `UserController@accountPreferences`
- `GET|POST users/security` → `UserController@security`

### User Verification
- `GET users/edit-verification` → `UserController@verification`
- `GET users/confirm_email/{code?}` → `UserController@confirmEmail`
- `GET users/new_email_confirm` → `UserController@newConfirmEmail`

### Social Account Management
- `GET facebookLoginVerification` → `UserController@facebookLoginVerification`
- `GET facebookConnect/{id}` → `UserController@facebookConnect`
- `GET facebookDisconnect` → `UserController@facebookDisconnectVerification`
- `GET googleLoginVerification` → `UserController@googleLoginVerification`
- `GET googleConnect/{id}` → `UserController@googleConnect`
- `GET googleDisconnect` → `UserController@googleDisconnect`

### User Profile & Reviews
- `GET users/show/{id}` → `UserController@show`
- `GET|POST users/reviews` → `UserController@reviews`
- `GET|POST users/reviews_by_you` → `UserController@reviewsByYou`
- `GET|POST reviews/edit/{id}` → `UserController@editReviews`
- `GET|POST reviews/details` → `UserController@reviewDetails`

### Property Management
- `GET|POST properties` → `PropertyController@userProperties`
- `GET|POST property/create` → `PropertyController@create`
- `GET|POST listing/{id}/photo_message` → `PropertyController@photoMessage` (middleware: `checkUserRoutesPermissions`)
- `GET|POST listing/{id}/photo_delete` → `PropertyController@photoDelete` (middleware: `checkUserRoutesPermissions`)
- `POST listing/photo/make_default_photo` → `PropertyController@makeDefaultPhoto`
- `POST listing/photo/make_photo_serial` → `PropertyController@makePhotoSerial`
- `GET|POST listing/update_status` → `PropertyController@updateStatus`
- `GET|POST listing/{id}/{step}` → `PropertyController@listing` (steps: basics|description|external_link|location|amenities|photos|pricing|calendar|details|booking|cancellation_policy|payment_processing)

### Favourites
- `GET user/favourite` → `PropertyController@userBookmark`
- `POST add-edit-book-mark` → `PropertyController@addEditBookMark`

### Calendar Management
- `POST ajax-calender/{id}` → `CalendarController@calenderJson`
- `POST ajax-calender-price/{id}` → `CalendarController@calenderPriceSet`
- `POST ajax-icalender-import/{id}` → `CalendarController@icalendarImport`
- `GET icalendar/synchronization/{id}` → `CalendarController@icalendarSynchronization`

### Payments & Bookings
- `POST currency-symbol` → `PropertyController@currencySymbol`
- `GET|POST payments/book/{id?}` → `PaymentController@index`
- `POST payments/create_booking` → `PaymentController@createBooking`
- `GET payments/success` → `PaymentController@success`
- `GET payments/cancel` → `PaymentController@cancel`
- `GET payments/stripe` → `PaymentController@stripePayment`
- `POST payments/stripe-request` → `PaymentController@stripeRequest`
- `GET|POST payments/bank-payment` → `PaymentController@bankPayment`
- `GET booking/{id}` → `BookingController@index` (where id is numeric)
- `GET booking_payment/{id}` → `BookingController@requestPayment` (where id is numeric)
- `GET booking/requested` → `BookingController@requested`
- `GET booking/itinerary_friends` → `BookingController@requested`
- `POST booking/accept/{id}` → `BookingController@accept`
- `POST booking/decline/{id}` → `BookingController@decline`
- `GET booking/expire/{id}` → `BookingController@expire`
- `GET|POST my-bookings` → `BookingController@myBookings`
- `POST booking/host_cancel` → `BookingController@hostCancel`

### Trips
- `GET|POST trips/active` → `TripsController@myTrips`
- `GET booking/receipt` → `TripsController@receipt`
- `POST trips/guest_cancel` → `TripsController@guestCancel`

### Messaging
- `GET|POST inbox` → `InboxController@index`
- `POST messaging/booking/` → `InboxController@message`
- `POST messaging/reply/` → `InboxController@messageReply` (text only)
- `POST messaging/upload-file/` → `InboxController@uploadFile` (named: `messaging.upload-file`, files only)

### Account Management
- `GET users/account_delete/{id}` → `UserController@accountDelete`
- `GET users/account_default/{id}` → `UserController@accountDefault`
- `GET users/transaction-history` → `UserController@transactionHistory`
- `POST users/account_transaction_history` → `UserController@getCompletedTransaction`

### Payout Management
- `GET|POST users/payout` → `PayoutController@index`
- `GET|POST users/payout/setting` → `PayoutController@setting`
- `GET|POST users/payout/edit-payout/` → `PayoutController@edit`
- `GET|POST users/payout/delete-payout/{id}` → `PayoutController@delete`
- `GET|POST users/payout-list` → `PayoutController@payoutList`
- `GET|POST users/payout/success` → `PayoutController@success`

### Logout
- `GET logout` → Logs out user, flushes session, redirects to login

---

## Route Statistics

### Total Routes by Category:
- **Public Routes**: ~15 routes
- **Admin Routes**: ~150+ routes
- **User Authentication Routes**: ~10 routes
- **User Routes**: ~60+ routes

### Middleware Groups:
1. **locale** - Language localization
2. **guest:admin** - Admin must be logged in
3. **no_auth:admin** - Admin must NOT be logged in
4. **no_auth:users** - User must NOT be logged in
5. **guest:users** - User must be logged in
6. **permission:** - Various permission-based middleware

### Named Routes:
- `property.single`
- `checkUser.check`
- `upload` (multiple instances)
- `adminEditPageAjax`
- `messaging.upload-file`

---

## Notes

- Routes are organized by authentication level and functionality
- Many admin routes require specific permissions
- User routes require authentication via `guest:users` middleware
- Some routes support both GET and POST methods for flexibility
- The catch-all route `{name}` at the end handles static pages
- iCalendar synchronization routes exist for both admin and user contexts




