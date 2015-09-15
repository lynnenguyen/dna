@extends('app')
@section('content')
    <a href='/'>@include('partials.logo')</a>



    <br/>
    <h3>Assay</h3>

    <table class="table table-striped">
        <tr>
            <th>Assay</th>
            <th>Default</th>

        </tr>
        @foreach ($assays as $assay)
            <tr>
                <td>{{($assay->name)}}</td>
                <td>{{($assay->default)}}</td>

            </tr>
        @endforeach
    </table>

@endsection

@section('footer')

@endsection