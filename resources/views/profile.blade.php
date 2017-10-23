<div class="container-fluid">
    <div class="row">
        <div class="col-md-4"><br>
            <hr>
        </div>
        <div class="col-xs-4"><img id="profile_image" src="{{$profile['profile_image_url_https']}}"
                                   class="img-thumbnail img-responsive img-circle"></div>
        <div class="col-md-4"><br>
            <hr>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">

            <h3 class="text-center">{{$profile['name']}}</h3>

            <p class="text-center">
                {{$profile['description']}}
            </p>
            <h3 class="text-center"><a href="{{$profile['url']}}" target="_blank">{{$profile['url']}}</a></h3>
            <hr>
            <p class="text-center">
                <a href="#"><i class="fa fa-twitter-square fa-fw fa-3x"></i></a> &nbsp;
                <a href="#"><i class="fa fa-facebook-square fa-fw  fa-3x"></i></a> &nbsp;
                <a href="#"><i class="fa fa-google-plus-square fa-fw  fa-3x"></i></a>
            </p>

        </div>
        <div class="col-md-1"></div>
    </div><!--/row-->
</div><!--/container-->