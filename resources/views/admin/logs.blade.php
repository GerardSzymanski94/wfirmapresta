@extends('admin.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2>LOGI</h2>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Data startu</th>
                    <th scope="col">Data końca</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at }}</td>
                        <td>{{ $log->updated_at }}</td>
                        <td><span class="{{ $log->status[1] }}">{{ $log->status[0] }}</span></td>
                        <td><a href="{{ route('admin.log', ['log'=>$log->id]) }}" class="btn btn-primary">Szczegóły</a>
                        </td>
                    </tr>
                @endforeach

                {{ $logs->links() }}
                </tbody>
            </table>

        </div>
    </div>

@endsection
