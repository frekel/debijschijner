@extends('admin.layout')

@section('title', 'Contact inzendingen')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Contact inzendingen</h1>
            <p class="text-gray-600 mt-2">Overzicht van alle contactformulier inzendingen</p>
        </div>
        <a href="{{ route('admin.contact-submissions.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
            📥 CSV Export
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Naam</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Telefoon</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Bericht</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Datum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($submissions as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $submission->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $submission->first_name }} {{ $submission->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600"><a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:underline">{{ $submission->email }}</a></td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $submission->phone }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $submission->message }}">{{ $submission->message }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $submission->created_at?->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-600">Nog geen inzendingen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $submissions->links() }}
    </div>
@endsection
