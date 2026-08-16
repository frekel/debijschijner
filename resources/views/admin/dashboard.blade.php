@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Welkom in het admin panel</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.pages.index') }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border-l-4 border-blue-500">
            <div class="text-4xl mb-4">📄</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Pagina beheer</h2>
            <p class="text-gray-600">Beheer, bewerk en maak pagina's aan</p>
        </a>

        <a href="{{ route('admin.contact-submissions.index') }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border-l-4 border-green-500">
            <div class="text-4xl mb-4">📧</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Contact inzendingen</h2>
            <p class="text-gray-600">Bekijk en exporteer contactformulier inzendingen</p>
        </a>

        <a href="{{ route('admin.apply-submissions.index') }}" class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border-l-4 border-purple-500">
            <div class="text-4xl mb-4">📋</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Aanvraag inzendingen</h2>
            <p class="text-gray-600">Bekijk en exporteer aanvraagformulier inzendingen</p>
        </a>
    </div>
@endsection
