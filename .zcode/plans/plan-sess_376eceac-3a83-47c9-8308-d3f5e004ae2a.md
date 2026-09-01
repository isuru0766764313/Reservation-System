## Plan: Centralize Reservation Status Badge Logic

### Problem
The status badge rendering logic is duplicated inline across blade files with hardcoded magic numbers and repeated payment queries. Adding a new status means changing multiple blade files, and migration changes are risky with existing data.

### Scope of Duplication Found
| Location | Lines | Description |
|---|---|---|
| `CustomerDashboardPage.blade.php` | 957-988 | Main table status column (primary target) |
| `AdminDashboard.blade.php` | 588-619 | Admin table status column (same pattern, slightly different labels) |
| `CustomerDashboardPage.blade.php` | 1089-1095 | Secondary status block (simpler, uses `getReservationStatusLabel`) |
| `CustomerDashboardPage.blade.php` | 1671-1702 | Modal payment detail status (different labels) |

### Approach

**Create one new static method** in `ReservationController.php`:

```php
public static function getCustomerStatusBadge($reservation): array
```

This function:
- Takes the full `$reservation` model (has access to `->status` and `->payments` relationship)
- Uses `switch($status)` for the outer status check (instructor preference)
- Uses `if/elseif` inside cases 2 and 3 for payment sub-status checks
- Returns `['label' => '...', 'class' => '...']` — the blade renders a single `<span>`
- Handles **all 7 statuses** including the previously-missing status `6` (Rejected)

### Files to Change

1. **`app/Http/Controllers/ReservationController.php`** — Add `getCustomerStatusBadge()` method (after line 748, before closing `}`)

2. **`resources/views/CustomerDashboardPage.blade.php`** — Replace lines 957-988 (the big if/elseif block) with:
```blade
@php $badge = \App\Http\Controllers\ReservationController::getCustomerStatusBadge($reservation); @endphp
<span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
```
This eliminates the duplicate `@php` block (lines 958-961) that queries payments — the function handles that internally.

3. **`resources/views/AdminDashboard.blade.php`** — Same refactoring for lines 588-619 (the admin has near-identical logic with slightly different wording). The existing `getCustomerStatusBadge` function can be reused directly since the labels are similar enough; or a separate context parameter can differentiate admin vs customer labels if needed.

### What This Solves
- ✅ New status? Add one `case` to the function — all blades update automatically
- ✅ No migration changes needed — status columns stay as-is
- ✅ `getPaymentStatusLabel` (currently unused) remains available for future use
- ✅ Eliminates duplicate `$reservation->payments->where(...)` queries in blade
- ✅ Status 6 (Rejected) gets its own explicit case instead of falling through to `else`