@extends('layout.blankpage')

@section ('content')

<h1 class="mt-4">Completed Requests</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
    <li class="breadcrumb-item active">Complteted Requests</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">

                <h4>Completed Requests
                <a href="{{ url('panel/role') }}" class="btn btn-danger float-end">Back</a>
                </h4>

            </div>

            <div class="card-body">
                <form action="{{ route('role.insert') }}" method="POST">
                    @csrf
                <div class="mb-3">
                <label class="form-label" style="font-size: 1.25rem; font-weight: bold; color: black; text-transform: uppercase; letter-spacing: 1px;">Role Name</label>
                    <input type="text" name="role" class="form-control" />
                    @error('role') {{$message}} @enderror
                </div>

                <div class="mb-3">
                <label class="form-label" style="display:block; margin-bottom: 20px; font-size: 1.25rem; font-weight: bold; color: black; text-transform: uppercase; letter-spacing: 1px;">Permission</label>
                    @foreach($data as $value)
                    <div class="row" style="margin-bottom: 20px;">

                        <div class="col-md-4">
                            {{ $value['name'] }}
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                @foreach($value['group'] as $group)
                                <div class="col-md-3">
                                    <label for=""><input type="checkbox" value="{{$group['id']}}" name="permission_id[]">{{ $group['name'] }}</label>

                                </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                    <hr>
                    @endforeach
                </div>


                <div>
                    <button type="submit" class="btn btn-primary ">Save</button>
                </div>

                </form>
            </div>
        </div>
    </div>



    @endsection
