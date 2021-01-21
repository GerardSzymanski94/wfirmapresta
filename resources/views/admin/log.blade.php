@extends('admin.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nazwa produktu</th>
                    <th scope="col">SKU</th>
                    <th scope="col">Id produktu</th>
                    <th scope="col">Zmiana</th>
                    <th scope="col">Data</th>
                </tr>
                </thead>
                <tbody>
                @foreach($log->details as $details)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $details->product_name }}</td>
                        <td>{{ $details->product_code }}</td>
                        <td>{{ $details->product_id }}</td>
                        <td>{{ $details->count_before }} -> {{ $details->count_after }}</td>
                        <td>{{ $details->created_at }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>

        </div>
    </div>

@endsection
