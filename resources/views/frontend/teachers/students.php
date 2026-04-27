<div class="modal-body">
    <div class="table-responsive">
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Name</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($group_students as $student): ?>
                    <tr>
                        <td><?= $student->student->name ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>