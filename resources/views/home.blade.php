<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
          crossorigin="anonymous">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->

                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="row">
                    <div class="col-md-12">
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endforeach
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('info'))
                            <div class="alert alert-info">{{ session('info') }}</div>
                        @endif
                        @if(session('warning'))
                            <div class="alert alert-warning">{{ session('warning') }}</div>
                        @endif
                    </div>
                </div>
                <form method="post" action="mentions">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-11">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-at" aria-hidden="true"></i></div>
                                <input type="text"
                                       name="twitter_handle"
                                       class="form-control input-lg"
                                       id="twitter_handle"
                                       placeholder="Twitter Handle"
                                       value="{{ ($twitterUser ? $twitterUser->twitter_handle : '') }}"
                                >
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                @if($twitterUser)
                    <h3 class="text-center">{{$twitterUser->tweets()->count() }} Tweets Searched</h3>
                @endif
                @if (count($mentions))
                    @if($twitterUser)
                        <div class="text-right">
                            <small>
                                <strong>
                                    Results cached
                                    for <mark>{{ $twitterUser->cached_since->diffForHumans(null, true) }}</mark>
                                </strong>
                            </small>
                        </div>
                    @endif
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Username</th>
                            <th>Count</th>
                            <th>Recent Tweets</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($mentions as $mention)
                            <tr>
                                <th scope="row">
                                    <div><h4><a data-toggle="modal" class="openProfile"
                                            data-twitter-handle="{{ $mention->mentioned->twitter_handle }}"
                                            data-target="#myModal">
                                            {{ $mention->mentioned->twitter_handle }}
                                        </a>
                                            </h4>
                                    </div>
                                    <div>
                                        <form method="post" action="mentions">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="twitter_handle" value="{{ $mention->mentioned->twitter_handle }}">
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fa fa-line-chart" aria-hidden="true"></i> Analyze
                                            </button>
                                        </form>
                                    </div>

                                </th>
                                <td>{{ $mention->total }}</td>
                                <td>
                                    <table>
                                        @foreach($mention->mentioned->mentions()->orderBy('tweet_created_at', 'DESC')->get()->take(5) as $mention)
                                            <tr>
                                                <td style="white-space:nowrap;vertical-align: top">{{ $mention->tweet_created_at }}</td>
                                                <td>{{ $mention->text }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h1 id="myModalLabel" class="text-center"></h1>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer bg-info">
                    <button class="btn btn-lg btn-default center-block" data-dismiss="modal" aria-hidden="true"> OK
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    $('.openProfile').on('click', function () {
        var twitter_handle = $(this).attr('data-twitter-handle');
        $('.modal-body').load('profile/' + twitter_handle, function () {
            $('#myModal').modal({show: true});
            $('#myModalLabel').text(twitter_handle)
        });
    });
</script>
</body>
</html>
