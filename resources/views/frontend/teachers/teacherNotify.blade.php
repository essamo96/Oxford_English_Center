<div class="tab-pane fade active in" id="AdminNotify2">
    <div class="courses-page-area3">
        <div class="container">
            <div class="row">
                <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="section-divider"></div>
                            <div class="course-details-inner">
                                <div class="course-details-comments">
                                    <h3 class="sidebar-title">Admin Messages And Notices 
                                          <button href="#" class="btn btn-success btn-sm" id="go-back"
                                      onclick="location.reload()"> back <i class="bi bi-skip-backward-fill"></i></button>
                                    </h3>
                                    @foreach ($notifys as $notify)
                                        <div class="media">
                                            <a href="#" class="pull-left">
                                                <img alt="Comments" src="{{ url('assets/oxford/img/logo.png') }}"
                                                    width="50px" height="50px" class="media-object">
                                            </a>
                                            <div class="media-body">
                                                <h3 style="margin-top: 9px;"><a>{{ $notify->data['title'] }} </a></h3>
                                               
                                                <p><strong>{{ $notify->data['body'] }}</strong> </p>
                                                <small>{{ $notify->created_at->diffForHumans() }} BY {{ $notify->data['sender_name'] }}</small>
                                                <div class="replay-area">
                                                    {{-- <ul>
                                                                                <li><i class="fa fa-star"
                                                                                        aria-hidden="true"></i></li>
                                                                                <li><i class="fa fa-star"
                                                                                        aria-hidden="true"></i></li>
                                                                                <li><i class="fa fa-star"
                                                                                        aria-hidden="true"></i></li>
                                                                                <li><i class="fa fa-star"
                                                                                        aria-hidden="true"></i></li>
                                                                                <li><i class="fa fa-star"
                                                                                        aria-hidden="true"></i></li>
                                                                            </ul> --}}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="section-divider"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
