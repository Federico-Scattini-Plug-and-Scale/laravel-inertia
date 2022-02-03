@extends('invoices.layout')

@section('content')
	<div>
		<h1 style="text-align: center">{{ __('Invoice nr.') }} {{ $invoice->invoice_number }}</h1>
		<br>
		<table width="100%">
			<tr>
				<td>
					{{ $user->invoice_name }}<br>
					{{ $user->invoice_street }}<br>
					{{ $user->invoice_postal_code }}, {{ $user->invoice_city }}<br>
					{{ $user->invoice_country }}<br>
					{{ $user->invoice_email }}<br>
					{{ $user->invoice_phone }}
				</td>
				<td>
					{{ $user->invoice_name }}<br>
					{{ $user->invoice_street }}<br>
					{{ $user->invoice_postal_code }}, {{ $user->invoice_city }}<br>
					{{ $user->invoice_country }}<br>
					{{ $user->invoice_email }}<br>
					{{ $user->invoice_phone }}
				</td>
			</tr>
		</table>
		<br><br>
		<table border="1" width="100%">
			<thead>
				<tr>
					<th></th>
					<th>{{ __('Date') }}</th>
					<th>{{ __('Price') }}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="text-align: center">1</td>
					<td style="text-align: center">{{ $invoice->created_at->format('d-m-Y') }}</td>
					<td style="text-align: center">{{ $invoice->amount/100 }} {{ $invoice->currency }}</td>
				</tr>
			</tbody>
		</table>
	</div>
@endsection