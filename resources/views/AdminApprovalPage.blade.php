<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration Request - Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-lg">

    @isset($error)
        <!-- Invalid / missing request -->
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center space-y-4">
            <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Request Not Found</h1>
            <p class="text-gray-600">{{ $error }}</p>
            <a href="{{ route('home_route') }}" class="inline-block mt-2 text-indigo-600 font-semibold hover:underline">Back to Home</a>
        </div>
    @endisset

    @isset($applicantAdmin)

        @if(in_array($applicantAdmin->approval_status, ['approved', 'rejected']))
            <!-- Already decided (covers re-opening an old email link AND the post-approve/reject result screen) -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center space-y-4">
                @if($applicantAdmin->approval_status === 'approved')
                    <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-green-100">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Registration Approved</h1>
                    <p class="text-gray-600">
                        <strong>{{ $applicantAdmin->company_name }}</strong> ({{ $applicantAdmin->email }})
                        has been <span class="font-semibold text-green-600">approved</span>.
                        They can now verify their account and sign in.
                    </p>
                    @if(isset($result) && $result === 'approved')
                        <p class="text-sm text-gray-500">An approval notification has been sent to the applicant's email.</p>
                    @endif
                @else
                    <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
                        <i class="fas fa-times-circle text-3xl text-red-500"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Registration Rejected</h1>
                    <p class="text-gray-600">
                        <strong>{{ $applicantAdmin->company_name }}</strong> ({{ $applicantAdmin->email }})
                        has been <span class="font-semibold text-red-600">rejected</span>.
                    </p>
                    @if(isset($result) && $result === 'rejected')
                        <p class="text-sm text-gray-500">A rejection notification has been sent to the applicant's email.</p>
                    @endif
                @endif

                @if($applicantAdmin->approval_status === 'approved' && $applicantAdmin->approved_at)
                    <p class="text-xs text-gray-400">Approved on {{ $applicantAdmin->approved_at->format('Y-m-d H:i') }}</p>
                @endif

                <a href="{{ route('home_route') }}" class="inline-block mt-2 text-indigo-600 font-semibold hover:underline">Back to Home</a>
            </div>
        @else
            <!-- Pending: show details + approve / reject buttons -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-indigo-600 px-8 py-6 text-white">
                    <h1 class="text-2xl font-bold">New Admin Registration Request</h1>
                    <p class="text-indigo-200 text-sm mt-1">Review the details below, then approve or reject.</p>
                </div>

                <div class="p-8 space-y-6">
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500 font-medium">Company Name</span>
                            <span class="text-gray-800 font-semibold text-right">{{ $applicantAdmin->company_name }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500 font-medium">Email</span>
                            <span class="text-gray-800 font-semibold text-right">{{ $applicantAdmin->email }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500 font-medium">Contact Number</span>
                            <span class="text-gray-800 font-semibold text-right">{{ $applicantAdmin->telephone_number }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500 font-medium">Requested On</span>
                            <span class="text-gray-800 font-semibold text-right">{{ $applicantAdmin->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 font-medium">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 uppercase">Pending</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <!-- Approve -->
                        <form action="{{ route('admin.approval.approve', $applicantAdmin->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        </form>

                        <!-- Reject -->
                        <form action="{{ route('admin.approval.reject', $applicantAdmin->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Reject this registration request?');"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endisset

</div>
</body>
</html>
