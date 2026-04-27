 <h3 class="title-section title-bar-high mb-40">Courses Students <button id="go-back" href="#Courses" data-toggle="tab"
         aria-expanded="false" class="btn btn-success btn-sm"> back <i class="bi bi-skip-backward-fill"></i></button></h3>
 <div class="orders-info">
     <div class="table-responsive">
         <table class="table table-bordered table-responsive">
             <thead>
                 <tr>
                     <th style="width: 120px;">Student image</th>
                     <th>Student Name</th>
                     <th>Progress Test 1 (Units 1-3)</th>
                     <th>Progress Test 2 (Units 4-6)</th>
                     <th>Progress Test 3 (Units 7-9)</th>
                     <th>End of Course Test (Units 1-12)</th>
                     <th>Activity Degree</th>
                     <th>Workbook Degree</th>
                     <th>Total Degree </th>
                     <th>Evaluation</th>
                     {{-- <th>students No.</th>
                                                <th>Exam Dates</th>
                                                <th>Chat</th>
                                                <th>Teacher Library</th> --}}
                 </tr>
             </thead>
             <tbody>
                 @foreach ($data as $group)
                     <tr>
                         <td>
                             @if ($group->student->image != null)
                                 <img src="<?= url($group->student->image) ?>"
                                     style="margin-left: 26px; width: 50%; border-radius: 50%;">
                             @else
                                 <img src="<?= url('assets/oxford/img/students/avatar.png') ?>"
                                     style="margin-left: 26px; width: 50%; border-radius: 50%;">
                             @endif
                         </td>
                         <td>{{ $group->student->name }}</td>
                         @if ($group->exam1_degree  != NULL)
                         <td><span style="color:#002147">{{$group->exam1_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                         @endif

                         @if ($group->exam2_degree != NULL)
                         <td><span style="color:#002147">{{$group->exam2_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                         @endif
                         
                         @if ($group->exam3_degree != NULL)
                         <td><span style="color:#002147">{{$group->exam3_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                        @endif
                         
                         @if ($group->exam4_degree != NULL)
                         <td><span style="color:#002147">{{$group->exam4_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                         @endif

                         @if ($group->activity_degree != NULL)
                         <td><span style="color:#002147">{{$group->activity_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                         @endif

                         @if ($group->workbook_degree != NULL)
                         <td><span style="color:#002147">{{$group->workbook_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                         @endif
                         
                         @if ($group->workbook_degree != NULL)
                         <td><span style="color:#002147">{{$group->total_degree}}</span></td>
                         @else
                         <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                         @endif
                         
                            @if ($group->has_evaluation == 0)
                              {{-- @if(now()->diffInDays($group->created_at) >= 7) --}}
                            <td><a style="background-color:#ffae00" href="{{ route('teacher.evaluate.view',[ 'group_id' => Crypt::encrypt($group_id),'student_id'=>Crypt::encrypt($group->student_id)])}}" title="Exam Dates" class="btn btn-primary btn-sm">
                                 Add Evaluation</a></td>
                             @else
                               <td style="text-align: center; font-size:larger;"> <strong style="color:#002147">evaluated</strong></td>
                            @endif
                   
                     </tr>
                 @endforeach
             </tbody>
         </table>
     </div>
 </div>

