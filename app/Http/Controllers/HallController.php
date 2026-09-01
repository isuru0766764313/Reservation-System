<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HallModel;
use App\Models\FixedPriceFacilitiesModel;
use App\Models\UnitPriceFacilitiesModel;
use App\Models\PackagesModel;
use App\Models\HallUnAvailability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class HallController extends Controller
{
    function open_create_new_hall_page()
    {
        $admin = Auth::guard('admin')->user();
        return view('InsertData', compact('admin'));
    }
    public function InsertHallData(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:wedding,party,exhibition,reception,sport,arena,concert,memorial,lecture,building,floor,room,outdoortheator,multipurpose,resorts,bangalow,conference,banquet,convention,crematorium,auditorium,community,stadium,outdoorground',
            'price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                Rule::requiredIf(function () use ($request) {
                    return $request->booking_method !== 'package';
                }),
            ],
            'discount'=> 'nullable|numeric|min:0|max:99999999.99',
            'cancellation_fee'=> 'nullable|numeric|min:0|max:99999999.99',
            'deposit'=> 'nullable|numeric|min:0|max:99999999.99',
            'max_pre_arrange_hours' => 'nullable|integer|min:0|max:24',
            'max_post_arrange_hours' => 'nullable|integer|min:0|max:24',
            'booking_method' => 'required|string|in:regular,package,both',
            'capacity' => 'required|integer|min:1',           
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'area' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',            

            'fixedpricefacility' => 'array',
            'fixedpricefacility.*.name' => 'nullable|string',
            'fixedpricefacility.*.charge' => 'nullable|numeric|min:0',

            'unitpricefacility' => 'array',
            'unitpricefacility.*.name' => 'nullable|string',
            'unitpricefacility.*.charge' => 'nullable|numeric|min:0',

            'availability' => 'required|array|min:1',
            'availability.*.date' => 'required|date|after_or_equal:today',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time',

            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max per image

            'pdf' => 'sometimes|file|mimes:pdf|max:10240', // Max 10MB PDF
            'clearence_form' => 'sometimes|file|mimes:pdf|max:10240' // Max 10MB PDF

        ]);

        $halldata = $request->except(['fixedpricefacility','unitpricefacility','availability', '_token', 'images']);

        $halldata['admin_id'] = $admin->id;


        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image){
                $path = $image->store('halls/images', 'public'); // Store in storage/app/public/halls/images
                $imagePaths[] = $path; // Save path (e.g., "halls/images/filename.jpg")
            }
        }
        // Add image paths to data (will be automatically cast to JSON array)
        $halldata['images'] = $imagePaths;

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('halls/terms', 'public'); // Store in storage/app/public/halls/terms
            $halldata['pdf'] = $pdfPath; // Save path (e.g., "halls/terms/filename.pdf")
        }

        if ($request->hasFile('clearence_form')) {
            $ClearencepdfPath = $request->file('clearence_form')->store('halls/clearence', 'public'); // Store in storage/app/public/halls/clearence
            $halldata['clearence_form'] = $ClearencepdfPath; // Save path (e.g., "halls/terms/filename.pdf")
        }

        $hall = HallModel::create($halldata);

        if ($hall)
        {
            // Save fixed priced facilities as items
            if ($request->has('fixedpricefacility')) {
                foreach ($request->fixedpricefacility as $fpfacility)
                {
                    if (!empty($fpfacility['name'])) { // Only create if name is not empty
                        FixedPriceFacilitiesModel::create
                        ([
                            'hall_id' => $hall->id,
                            'name' => $fpfacility['name'],
                            'charge' => $fpfacility['charge']
                        ]);
                    }
                }
            }
            // Save unit priced facilities as items
            if ($request->has('unitpricefacility')) {
                foreach ($request->unitpricefacility as $upfacility)
                {
                    if (!empty($upfacility['name'])) { // Only create if name is not empty
                        UnitPriceFacilitiesModel::create
                        ([
                            'hall_id' => $hall->id,
                            'name' => $upfacility['name'],
                            'charge' => $upfacility['charge']
                        ]);
                    }
                }
            }
            // Save un-availability time periods as slots
            foreach ($request->availability as $slot) 
            {
                HallUnAvailability::create
                ([
                    'hall_id' => $hall->id,
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time']
                ]);
            }
            //return back()->with('success', 'Hall added successfully!');
            //return redirect()->intended(route('load_back_hall_data_insert_page', ['hall_id' => $hall->id]))->with('success', 'Login successful!')->with('hall','$hall');
            return redirect()->intended(route('admin.dashboard.route'));
        }
        else
        {
            return back()->with('error', 'Something went wrong. Pls try again');
        }
    }

    function open_hall_update_page(HallModel $hall)
    {
        $admin = Auth::guard('admin')->user();
        $hall->load(['fixedfacilities', 'unitfacilities', 'availability','reservations','packages']);
        return view('InsertData', compact('admin','hall'));
    }

    public function hall_update(Request $request, HallModel $hall)
    {
        $admin = Auth::guard('admin')->user();

        // Use the same validation rules as InsertHallData
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:wedding,party,exhibition,reception,sport,arena,concert,memorial,lecture,building,floor,room,outdoortheator,multipurpose,resorts,bangalow,conference,banquet,convention,crematorium,auditorium,community,stadium,outdoorground',
            'price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                Rule::requiredIf(function () use ($request) {
                    return $request->booking_method !== 'package';
                }),
            ],
            'discount'=> 'nullable|numeric|min:0|max:99999999.99',
            'cancellation_fee'=> 'nullable|numeric|min:0|max:99999999.99',
            'deposit'=> 'nullable|numeric|min:0|max:99999999.99',
            'max_pre_arrange_hours' => 'nullable|integer|min:0|max:24',
            'max_post_arrange_hours' => 'nullable|integer|min:0|max:24',
            'booking_method' => 'required|string|in:regular,package,both',
            'capacity' => 'required|integer|min:1',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'area' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',

            'fixedpricefacility' => 'array',
            'fixedpricefacility.*.name' => 'nullable|string',
            'fixedpricefacility.*.charge' => 'nullable|numeric|min:0',

            'unitpricefacility' => 'array',
            'unitpricefacility.*.name' => 'nullable|string',
            'unitpricefacility.*.charge' => 'nullable|numeric|min:0',

            'availability' => 'required|array|min:1',
            'availability.*.date' => 'required|date|after_or_equal:today',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time',

            'images' => 'sometimes|array|min:1|max:5', // Changed to 'sometimes' for updates
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            'pdf' => 'sometimes|file|mimes:pdf|max:10240',// Max 10MB PDF
            'clearence_form' => 'sometimes|file|mimes:pdf|max:10240' // Max 10MB PDF

        ]);

        $halldata = $request->except(['fixedpricefacility', 'unitpricefacility', 'availability', '_token', 'images', 'deleted_images']);

        // Handle image uploads - merge with existing images if any
        if ($request->hasFile('images'))
        {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('halls/images', 'public');
                $imagePaths[] = $path;
            }

            // If there are existing images, merge with new ones
            $existingImages = $hall->images ?? [];
            $halldata['images'] = array_merge($existingImages, $imagePaths);

            // Limit to 5 images total
            if (count($halldata['images']) > 5) {
                $halldata['images'] = array_slice($halldata['images'], 0, 5);
            }
        }

        // Handle image deletions
        if ($request->has('deleted_images')) {
            $currentImages = $hall->images ?? [];
            $imagesToKeep = array_diff($currentImages, $request->deleted_images);

            // Delete files from storage
            foreach ($request->deleted_images as $imageToDelete) {
                if (Storage::disk('public')->exists($imageToDelete)){
                    Storage::disk('public')->delete($imageToDelete);
                }
            }

            $halldata['images'] = array_values($imagesToKeep); // Reindex array
        }

        // Handle PDF upload
        if ($request->hasFile('pdf')){
            // Delete old PDF if exists
            if ($hall->pdf && Storage::disk('public')->exists($hall->pdf)){
                Storage::disk('public')->delete($hall->pdf);
            }

            $pdfPath = $request->file('pdf')->store('halls/terms', 'public');
            $halldata['pdf'] = $pdfPath;
        }

        // Handle Clearence PDF upload
        if ($request->hasFile('clearence_form'))
        {
            // Delete old PDF if exists
            if ($hall->clearence_form && Storage::disk('public')->exists($hall->clearence_form)){
                Storage::disk('public')->delete($hall->clearence_form);
            }

            $ClearencepdfPath = $request->file('clearence_form')->store('halls/clearence', 'public');
            $halldata['clearence_form'] = $ClearencepdfPath;
        }

        // Update the main hall record
        $hall->update($halldata);

        if ($hall) {
            // Handle fixed price facilities - delete existing and create new
            $hall->fixedfacilities()->delete();
            if ($request->has('fixedpricefacility')) {
                foreach ($request->fixedpricefacility as $fpfacility) {
                    if (!empty($fpfacility['name'])) { // Only create if name is not empty
                        FixedPriceFacilitiesModel::create([
                            'hall_id' => $hall->id,
                            'name' => $fpfacility['name'],
                            'charge' => $fpfacility['charge']
                        ]);
                    }
                }
            }

            // Handle unit price facilities - delete existing and create new
            $hall->unitfacilities()->delete();
            if ($request->has('unitpricefacility')) {
                foreach ($request->unitpricefacility as $upfacility) {
                    if (!empty($upfacility['name'])) { // Only create if name is not empty
                        UnitPriceFacilitiesModel::create([
                            'hall_id' => $hall->id,
                            'name' => $upfacility['name'],
                            'charge' => $upfacility['charge']
                        ]);
                    }
                }
            }

            // Handle availability - delete existing and create new
            $hall->availability()->delete();
            foreach ($request->availability as $slot) {
                HallUnAvailability::create([
                    'hall_id' => $hall->id,
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time']
                ]);
            }

            return redirect()->route('admin.dashboard.route')->with('success', 'Hall updated successfully!');
        }
        else
        {
            return back()->with('error', 'Something went wrong. Please try again');
        }
    }



    /**
     * Store packages for a hall
     */
    public function packages_store(Request $request, HallModel $hall)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.hourly_rate' => 'nullable|numeric|min:0',
            'packages.*.discount' => 'numeric|min:0',
            'packages.*.description' => 'required|string',
            'packages.*.duration' => 'required|integer|min:1',
            'packages.*.maximum_hours' => 'required|integer|min:0',
            'packages.*.fixed_price_facilities' => 'sometimes|array',
            'packages.*.fixed_price_facilities.*' => 'exists:fixed_price_facilities_table,id',
            'packages.*.unit_price_facilities' => 'sometimes|array',
            'packages.*.unit_price_facilities.*' => 'exists:unit_price_facilities_table,id',
        ]);

        if ($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Filter out empty packages and re-index
            $validPackages = array_filter($request->packages, function ($package) {
                return !empty($package['name']);
            });

            foreach ($validPackages as $packageData){
                $package = new PackagesModel();
                $package->hall_id = $hall->id;
                $package->name = $packageData['name'];
                $package->price = $packageData['price'] ?? null;
                $package->hourly_rate = $packageData['hourly_rate'] ?? null;
                $package->discount = $packageData['discount'];
                $package->description = $packageData['description'];
                $package->duration = $packageData['duration'];
                $package->maximum_hours = $packageData['maximum_hours'];

                // Handle fixed price facilities
                $fixedFacilities = isset($packageData['fixed_price_facilities']) && is_array($packageData['fixed_price_facilities'])
                    ? $packageData['fixed_price_facilities']
                    : [];
                //$package->fixed_price_facilities = json_encode(array_values($fixedFacilities));
                $package->fixed_price_facilities = array_map('intval', array_values($fixedFacilities));

                // Handle unit price facilities
                $unitFacilities = isset($packageData['unit_price_facilities']) && is_array($packageData['unit_price_facilities'])
                    ? $packageData['unit_price_facilities']
                    : [];
                //$package->unit_price_facilities = json_encode(array_values($unitFacilities));
                $package->unit_price_facilities = array_map('intval', array_values($unitFacilities));

                $package->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Packages created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating packages: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update packages for a hall
     */
    public function update(Request $request, HallModel $hall)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'packages' => 'required|array|min:1',
            'packages.*.id' => 'sometimes|exists:packages_table,id', // For existing packages
            'packages.*.name' => 'required|string|max:255',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.hourly_rate' => 'nullable|numeric|min:0',
            'packages.*.discount' => 'numeric|min:0',
            'packages.*.description' => 'required|string',
            'packages.*.duration' => 'required|integer|min:1',
            'packages.*.maximum_hours' => 'required|integer|min:0',
            'packages.*.fixed_price_facilities' => 'sometimes|array',
            'packages.*.fixed_price_facilities.*' => 'exists:fixed_price_facilities_table,id',
            'packages.*.unit_price_facilities' => 'sometimes|array',
            'packages.*.unit_price_facilities.*' => 'exists:unit_price_facilities_table,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Filter out empty packages
            $validPackages = array_filter($request->packages, function ($package) {
                return !empty($package['name']);
            });

            $processedPackageIds = [];

            foreach ($validPackages as $packageData) {
                if (isset($packageData['id']) && !empty($packageData['id'])) {
                    // Update existing package
                    $package = PackagesModel::where('id', $packageData['id'])
                        ->where('hall_id', $hall->id)
                        ->first();

                    if ($package) {
                        $package->name = $packageData['name'];
                        $package->price = $packageData['price'] ?? null;
                        $package->hourly_rate = $packageData['hourly_rate'] ?? null;
                        $package->discount = $packageData['discount'];
                        $package->description = $packageData['description'];
                        $package->duration = $packageData['duration'];
                        $package->maximum_hours = $packageData['maximum_hours'];

                        // Handle facilities
                        $fixedFacilities = isset($packageData['fixed_price_facilities']) && is_array($packageData['fixed_price_facilities'])
                            ? $packageData['fixed_price_facilities']
                            : [];
                        //$package->fixed_price_facilities = json_encode(array_values($fixedFacilities));
                        $package->fixed_price_facilities = array_map('intval', array_values($fixedFacilities));

                        $unitFacilities = isset($packageData['unit_price_facilities']) && is_array($packageData['unit_price_facilities'])
                            ? $packageData['unit_price_facilities']
                            : [];
                        //$package->unit_price_facilities = json_encode(array_values($unitFacilities));
                        $package->unit_price_facilities = array_map('intval', array_values($unitFacilities));

                        $package->save();
                        $processedPackageIds[] = $package->id;
                    }
                } else {
                    // Create new package
                    $package = new PackagesModel();
                    $package->hall_id = $hall->id;
                    $package->name = $packageData['name'];
                    $package->price = $packageData['price'] ?? null;
                    $package->hourly_rate = $packageData['hourly_rate'] ?? null;
                    $package->discount = $packageData['discount'];
                    $package->description = $packageData['description'];
                    $package->duration = $packageData['duration'];
                    $package->maximum_hours = $packageData['maximum_hours'];

                    $fixedFacilities = isset($packageData['fixed_price_facilities']) && is_array($packageData['fixed_price_facilities'])
                        ? $packageData['fixed_price_facilities']
                        : [];
                    $package->fixed_price_facilities = json_encode(array_values($fixedFacilities));

                    $unitFacilities = isset($packageData['unit_price_facilities']) && is_array($packageData['unit_price_facilities'])
                        ? $packageData['unit_price_facilities']
                        : [];
                    $package->unit_price_facilities = json_encode(array_values($unitFacilities));

                    $package->save();
                    $processedPackageIds[] = $package->id;
                }
            }

            // Delete packages that were removed from the form
            if (!empty($processedPackageIds)) {
                PackagesModel::where('hall_id', $hall->id)
                    ->whereNotIn('id', $processedPackageIds)
                    ->delete();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Packages updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating packages: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a specific package
     */
    public function destroy(PackagesModel $package)
    {
        try {
            $package->delete();
            return redirect()->back()->with('success', 'Package deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting package: ' . $e->getMessage());
        }
    }


    public function load_back_hall_insert_page(HallModel $hall)
    {
        return view('InsertData', compact('hall'));
    }

    public function show(HallModel $hall)
    {
        // Unavailable halls must not be visible on the customer side
        if (!$hall->available) {
            abort(404);
        }
        
        $fullyUnavailableDates = DB::table('hall_availabilities')
            ->select('date')
            ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time))) as total_seconds')
            ->where('hall_id', $hall->id)
            ->groupBy('date')
            ->havingRaw('total_seconds >= 86340')
            ->get()
            ->map(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            })
            ->toArray();

        return view('show', [
            'hall' => $hall,
            'fullyUnavailableDates' => $fullyUnavailableDates,
            'availabilityRange' => [
                'start' => now()->format('Y-m-d'),
                'end' => now()->addDays(180)->format('Y-m-d')
            ]
        ]);
    }

    public function getUnavailablePeriods($date)
    {
        // Validate request parameters
        request()->validate([
            'hall_id' => 'required|exists:halls_table,id'
        ]);

        $hallId = request('hall_id');

        try {
            // Parse the date
            $dateObj = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Fetch unavailable periods
        $periods = HallUnAvailability::where('hall_id', $hallId)
            ->whereDate('date', $dateObj)
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        return response()->json($periods);
    }

    /**
     * Return a per-day availability summary between start and end for a hall.
     * Response: array of events suitable for FullCalendar (all-day events with colors)
     */
    public function getUnavailablePeriodsRange(Request $request)
    {
        $request->validate([
            'hall_id' => 'required|exists:halls_table,id',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        ]);

        $hallId = $request->hall_id;
        $start = $request->start;
        $end = $request->end;

        // Fetch summed unavailable seconds per date in range
        $rows = DB::table('hall_availabilities')
            ->select('date')
            ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time))) as total_seconds')
            ->where('hall_id', $hallId)
            ->whereBetween('date', [$start, $end])
            ->groupBy('date')
            ->get()
            ->keyBy(function ($item) { return Carbon::parse($item->date)->format('Y-m-d'); });

        $events = [];
        $current = Carbon::createFromFormat('Y-m-d', $start);
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        // iterate inclusive
        while ($current->lte($endDate)) {
            $d = $current->format('Y-m-d');
            $totalSeconds = isset($rows[$d]) ? (int)$rows[$d]->total_seconds : 0;

            // Interpret availability: 0 seconds = fully available, >= 86340 ~ full day unavailable, else partial
            if ($totalSeconds === 0) {
                $color = '#28a745'; // green - available
            } elseif ($totalSeconds >= 86340) {
                $color = '#dc3545'; // red - not available
            } else {
                $color = '#ffc107'; // yellow - partially available
            }

            // FullCalendar expects end to be exclusive for all-day events
            $nextDay = $current->copy()->addDay()->format('Y-m-d');

            $events[] = [
                'title' => '',
                'start' => $d,
                'end' => $nextDay,
                'allDay' => true,
                'display' => 'background',
                'color' => $color,
            ];

            $current->addDay();
        }

        return response()->json($events);
    }
}
