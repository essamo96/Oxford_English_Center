 <div class="tab-pane fade active in" id="Progress2">
     <div class="courses-page-area3">
         <div class="container">
             <div class="row">
                 <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
                     <div class="row">
                         <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                             <div class="section-divider"></div>
                             <div class="course-details-inner">
                                 <div class="course-details-comments">

                                     <h3 class="sidebar-title">Student Progress
                                         <button href="#" class="btn btn-success btn-sm" id="go-back"
                                             onclick="location.reload()"> back <i
                                                 class="bi bi-skip-backward-fill"></i></button>
                                     </h3>
                                     <div class="orders-info">
                                         <div class="table-responsive">
                                             <table class="table table-bordered table-responsive">
                                                 <thead>
                                                     <tr>
                                                         <th style="width: 120px;">Grope Name</th>
                                                         <th>Progress</th>
                                                         <th>Units</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     @foreach ($data as $group)
                                                         <tr>
                                                             <td> <?= $group->group->name !== null ? $group->group->name : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td>
                                                                 @if ($group->progress == null)
                                                                         <div class="progress"  style="color: orange ;height: 16px;">
                                                                             <div class="progress-bar"
                                                                                 role="progressbar" style="width: 0% ;background-color: orange; height: 16px;"
                                                                                 aria-valuenow="0" aria-valuemin="0"
                                                                                 aria-valuemax="100">0%</div>
                                                                         </div>
                                                                         @else
                                                                           <div class="progress"  style="color: orange ;height: 16px;">
                                                                             <div class="progress-bar"
                                                                                 role="progressbar" style="width: {{ $group->progress }}% ;background-color: orange; height: 16px;"
                                                                                 aria-valuenow="{{ $group->progress }}" aria-valuemin="0"
                                                                                 aria-valuemax="100">{{ $group->progress }}%</div>
                                                                         </div>
                                                                 @endif

                                                             </td>

                                                             <td>
                                                                 @if ($group->progress == 30 || $group->progress == null)
                                                                     Units 1 to 3
                                                                 @elseif ($group->progress == 60)
                                                                     Units 4 to 6
                                                                 @elseif ($group->progress == 90)
                                                                     Units 7 to 9
                                                                 @else
                                                                     Units 10
                                                                 @endif
                                                             </td>

                                                         </tr>
                                                     @endforeach
                                                 </tbody>
                                             </table>
                                         </div>
                                     </div>

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
