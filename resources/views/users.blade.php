@extends('layouts.default')
@section('main')
        <div class="wrapper">
            <h3 class="title">ユーザー一覧</h3>
            <div>
                <table class="users_table">
                    <tr>
                        <th>ID</th>
                        <th>ユーザー名</th>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <a href="/user/{{$user->id}}">{{$user->id}}</a>
                            </td>
                            <td>
                                <a href="/user/{{$user->id}}">{{$user->name}}</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
@endsection