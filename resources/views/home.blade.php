@extends('emails.company.layouts.app')

@section('content')
  	<div class="p-20">
		<a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-white bg-blue-500 rounded mr-5">Link Pannello Admin</a>
		<a href="{{ route('company.dashboard') }}" class="px-4 py-2 text-white bg-green-500 rounded">Link Pannello Aziende</a>
  	</div>
@endsection