# Implementation Plan - Package Booking Revamp

The new package system works as follows:

## Core Concept
Instead of customer manually clicking a package card, the **package is auto-selected** based on total chargeable hours (R).

## How R is calculated
- **Q** = Event hours (end_time - start_time)
- **P** = Chargeable pre-post hours = max(0, preArrange-2) + max(0, postArrange-2)
- **R** = Q + P (chargeable total hours)

## Package Ranges (from DB columns)
Each package has: `duration` (min hours), `hourly_rate`, `maximum_hours` (range extension)
- Package A: min=duration=2, max=duration+ext_hourly=2+2=4
- Package B: min=5, max=5+3=8
- Package C: min=9, max=9+1=10
- Package D: min=11, max=11+13=24

## Charge Calculation
- Gross = (R × hourly_rate) + sum(unit_charge × R for each unit facility in package)
- Final = Gross - discount (if any)

## Changes Needed

- [ ] 1. Add data-hourly-rate, data-min-hours, data-max-hours, data-unit-charges to package cards
- [ ] 2. Remove auto-end-time calculation (end time freely selectable for packages)
- [ ] 3. Rewrite validateCustomTimePackage() to auto-select package based on R
- [ ] 4. Update initializePackageForm() to not disable end time
- [ ] 5. Update renderTimeSlots() - don't disable end time for packages
- [ ] 6. Update filterTimeOptions() - don't disable end time for packages
- [ ] 7. Update handleTimeSlotSelection() - don't disable end time
- [ ] 8. Update updateReserveButtonState() - remove selectedPackageDuration check
- [ ] 9. Update updateTotalCharge() for package mode
- [ ] 10. Modify package card click handler to remove manual selection
