<?php
/**
 * User: mult1mate
 * Date: 21.12.15
 * Time: 0:38
 * @var array $tasks
 */
?>
<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Time</th>
        <th>Command</th>
        <th>Status</th>
        <th>Comment</th>
        <th>Ts</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    <?php
    foreach ($tasks as $t):
        /**
         * @var Task $t
         */
        ?>
        <tr>
            <td><?= $t->task_id ?></td>
            <td><?= $t->time ?></td>
            <td><?= $t->command ?></td>
            <td><?= $t->status ?></td>
            <td><?= $t->comment ?></td>
            <td><?= $t->ts->format('Y-m-d H:i:s') ?></td>
            <td>
                <a href="?m=taskEdit&task_id=<?= $t->task_id ?>">Edit</a>
            </td>
            <td>
                <a href="?m=taskLog&task_id=<?= $t->task_id ?>">Log</a>
            </td>
            <td>
                <a href="<?= $t->task_id ?>" class="run_task">Run</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div id="output_section" style="display: none;">
    <h3>Task output</h3>
    <div class="alert alert-info" id="task_output_container">
    </div>
</div>
<script>
    $('.run_task').click(function () {
        if (confirm('Are you sure?')) {
            $('#output_section').show().text('Running...');
            $.post('?m=runTask', {task_id: $(this).attr('href')}, function (data) {
                $('#task_output_container').html(data);
            })
        }
        return false;
    });
</script>