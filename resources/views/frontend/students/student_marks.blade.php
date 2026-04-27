 <div class="tab-pane fade active in" id="marks2">
     <div class="courses-page-area3">
         <div class="container">
             <div class="row">
                 <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
                     <div class="row">
                         <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                             <div class="section-divider"></div>
                             <div class="course-details-inner">
                                 <div class="course-details-comments">

                                     <h3 class="sidebar-title">Student Marks
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
                                                         <th>Progress Test 1 <br> (Units 1-3)</th>
                                                         <th>Progress Test 2<br> (Units 4-6)</th>
                                                         <th>Progress Test 3<br> (Units 7-9)</th>
                                                         <th>End of Course Test <br>(Units 1-12)</th>
                                                         <th>Activity Degree</th>
                                                         <th>Workbook Degree</th>
                                                         <th>Total Degree </th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     @foreach ($data as $group)
                                                         <tr>
                                                             <td> <?= $group->group->name !== null ? $group->group->name : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->exam1_degree !== null ? $group->exam1_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->exam2_degree !== null ? $group->exam2_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->exam3_degree !== null ? $group->exam3_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->exam4_degree !== null ? $group->exam4_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->activity_degree !== null ? $group->activity_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->workbook_degree !== null ? $group->workbook_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
                                                             </td>
                                                             <td> <?= $group->total_degree !== null ? $group->total_degree : '<span style="color:red" class="bi bi-exclamation-circle"></span>' ?>
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
