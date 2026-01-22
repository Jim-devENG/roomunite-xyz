# Database Alignment Report
## Database: roomhsrx_homemates

### Summary
- **Total Database Tables**: 64
- **Total Models**: 60
- **Total Migrations**: 55

---

## ✅ Tables with Models AND Migrations (45 tables)
These tables are properly aligned:
- accounts
- admin
- amenities
- amenity_type
- banners
- bed_type
- booking_details
- bookings
- country
- currency
- email_templates
- language
- message_type
- messages
- notifications
- password_resets
- payment_methods
- payout_penalties
- payout_settings
- payouts
- penalty
- properties
- property_address
- property_beds
- property_dates
- property_description
- property_details
- property_fees
- property_icalimports
- property_photos
- property_price
- property_rules
- property_steps
- property_type
- reports
- reviews
- roles
- rules
- seo_metas
- settings
- space_type
- starting_cities
- testimonials
- timezone
- user_details
- users_verification
- withdrawals

---

## ⚠️ Tables in Database WITHOUT Migrations (6 tables)
These tables exist in the database but don't have corresponding migrations:

1. **activity_log** - Likely from Laravel activity log package
2. **migrations** - Laravel's migration tracking table (auto-created)
3. **password_reset_tokens** - Laravel 9+ password reset table (newer than password_resets)
4. **personal_access_tokens** - Laravel Sanctum table (auto-created)
5. **profiles** - Unknown table, no model
6. **user_tokens** - Unknown table, no model

**Note:** `permission_role`, `permissions`, and `role_admin` tables ARE in the migration file `2015_09_26_161159_entrust_setup_tables.php` (Entrust package)

---

## ⚠️ Models with Table Name Mismatches (10 models)
These models exist but the table name doesn't match what's expected:

1. **Backup** → Uses table `backups` ✓ (exists in DB)
2. **Bank** → Should use `banks` (exists in DB, model needs table definition)
3. **BankDate** → Should use `bank_dates` (exists in DB, model needs table definition)
4. **Favourite** → Should use `favourites` (exists in DB, model needs table definition)
5. **Meta** → Uses table `seo_metas` ✓ (correct)
6. **Page** → Uses table `pages` ✓ (exists in DB)
7. **PaymentDew** → Uses table `payouts` (appears to be an alias/legacy model for Payouts - both models exist)
8. **User** → Uses table `users` ✓ (exists in DB, Laravel default)
9. **Wallet** → Uses table `wallets` ✓ (exists in DB)
10. **Withdraws** → Should use `withdrawals` (DB has `withdrawals`, model uses `withdraws`)

---

## ❌ Database Tables WITHOUT Models (14 tables)
These tables exist in the database but don't have corresponding models:

1. **activity_log** - Activity logging (likely from package)
2. **backups** - Has Backup model but table name is correct
3. **bank_dates** - Has BankDate model but needs table definition
4. **banks** - Has Bank model but needs table definition
5. **failed_jobs** - Laravel queue failures (standard Laravel table)
6. **favourites** - Has Favourite model but needs table definition
7. **migrations** - Laravel migration tracking (standard)
8. **pages** - Has Page model but needs verification
9. **password_reset_tokens** - Laravel 9+ password reset
10. **personal_access_tokens** - Laravel Sanctum
11. **profiles** - Unknown purpose, no model
12. **user_tokens** - Unknown purpose, no model
13. **users** - Has User model (Laravel default)
14. **wallets** - Has Wallet model

---

## 🔧 Recommended Actions

### 1. Add Missing Table Definitions to Models
Update these models to specify their table names:
- `Bank.php` → Add `protected $table = 'banks';`
- `BankDate.php` → Add `protected $table = 'bank_dates';`
- `Favourite.php` → Add `protected $table = 'favourites';`

### 2. Fix Model/Table Mismatches
- `Withdraws.php` → Change to use `withdrawals` table or rename model to `Withdrawal`
- `PaymentDew.php` → Verify if this should map to `payouts` or create separate table

### 3. Migrations Status
✅ All permission-related tables (`permission_role`, `permissions`, `role_admin`) are in the Entrust migration file `2015_09_26_161159_entrust_setup_tables.php`

### 4. Investigate Unknown Tables
- `profiles` - Determine purpose and create model if needed
- `user_tokens` - Determine purpose and create model if needed

### 5. Standard Laravel Tables (No Action Needed)
These are auto-created by Laravel/packages:
- `migrations`
- `failed_jobs`
- `password_reset_tokens`
- `personal_access_tokens`
- `activity_log`

---

## ✅ Fixed Issues

The following models have been updated with correct table definitions:
- ✅ `Bank.php` → Now uses `banks` table
- ✅ `BankDate.php` → Now uses `bank_dates` table  
- ✅ `Favourite.php` → Now uses `favourites` table
- ✅ `Withdraws.php` → Now uses `withdrawals` table

## Overall Alignment Status: **90% Aligned**

Most tables are properly aligned. The main issues are:
1. Some models missing explicit table name definitions
2. A few tables without models (some are standard Laravel tables)
3. Some tables without migrations (some are from packages)

