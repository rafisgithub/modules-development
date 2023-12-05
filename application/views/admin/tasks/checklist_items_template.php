<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="clearfix"></div>
<?php if (count($checklists) > 0) { ?>
<div class="tw-flex tw-justify-between tw-items-center">
    <h4 class="chk-heading th tw-font-semibold tw-text-base"><?php echo _l('task_checklist_items'); ?></h4>
    <div class=chk-toggle-buttons">
        <button class="btn btn-default btn-sm<?php echo $hide_completed_items == 1 ? ' hide': '' ?>" data-hide="1"
            onclick="toggle_completed_checklist_items_visibility(this)">
            <?php echo _l('hide_task_checklist_items_completed'); ?>
        </button>
        <?php
            $finished = array_filter($checklists, function ($item) {
                return $item['finished'] == 1;
            });
        ?>
        <button class="btn btn-default btn-sm<?php echo $hide_completed_items == 1 ? '': ' hide' ?>" data-hide="0"
            onclick="toggle_completed_checklist_items_visibility(this)">
            <?php echo _l('show_task_checklist_items_completed', '(<span class="task-total-checklist-completed">' . count($finished) . '</span>)'); ?>
        </button>
    </div>
</div>
<?php } ?>
<div class="progress mtop15 no-mbot hide">
    <div class="progress-bar not-dynamic progress-bar-default task-progress-bar" role="progressbar" aria-valuenow="40"
        aria-valuemin="0" aria-valuemax="100" style="width:0%">
    </div>
</div>
<div class="tw-flex tw-flex-col">
    <?php foreach ($checklists as $list) { ?>
    <div>
        <div class="checklist" data-checklist-id="<?php echo $list['id']; ?>">
            <div class="tw-flex">
                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="">
                    <input type="checkbox" <?php if ($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id() && !is_admin()) {
            echo ' disabled';
        } ?> name="checklist-box" <?php if ($list['finished'] == 1) {
            echo 'checked';
        }; ?>>
                    <label for=""><span class="hide"><?php echo $list['description']; ?></span></label>
                </div>
                <div class="tw-grow">
                    <textarea data-taskid="<?php echo $task_id; ?>" name="checklist-description" rows="1" <?php if ($list['addedfrom'] != get_staff_user_id() && !has_permission('tasks', '', 'edit')) {
            echo ' disabled';
        } ?>><?php echo clear_textarea_breaks($list['description']); ?></textarea>
                </div>
                <div class="mleft10 tw-inline-flex tw-items-center tw-space-x-2">
                    <?php if (($list['addedfrom'] == get_staff_user_id() || $current_user_is_creator || is_admin()) && count($task_staff_members) > 0) { ?>
                    <span class="dropdown" data-title="<?php echo _l('task_checklist_assign'); ?>"
                        data-toggle="tooltip">
                        <a href="#" class="tw-text-neutral-500 dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" id="checklist-item-<?php echo $list['id']; ?>"
                            onclick="return false;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="tw-w-5 tw-h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-left"
                            aria-labelledby="checklist-item-<?php echo $list['id']; ?>">
                            <?php foreach ($task_staff_members as $_staff) { ?>
                            <li>
                                <a href="#"
                                    onclick="save_checklist_assigned_staff('<?php echo $_staff['staffid'] ; ?>', '<?php echo $list['id']; ?>'); return false;">
                                    <?php echo  $_staff['firstname'] . ' ' . $_staff['lastname'] ?>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </span>
                    <?php } ?>

                    <?php if (has_permission('checklist_templates', '', 'create')) { ?>
                    <a href="#" class="tw-text-neutral-500 save-checklist-template<?php if ($list['description'] == '' || total_rows(db_prefix() . 'tasks_checklist_templates', ['description' => $list['description']]) > 0) {
            echo ' hide';
        } ?>" data-toggle="tooltip" data-title="<?php echo _l('save_as_template'); ?>"
                        onclick="save_checklist_item_template(<?php echo $list['id']; ?>,this); return false;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="tw-w-6 tw-h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                        </svg>
                    </a>
                    <?php } ?>
                    <?php if (has_permission('tasks', '', 'delete') || $list['addedfrom'] == get_staff_user_id()) { ?>
                    <a href="#" class="tw-text-neutral-500 remove-checklist"
                        onclick="delete_checklist_item(<?php echo $list['id']; ?>,this); return false;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="tw-w-5 tw-h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php if ($list['finished'] == 1 || $list['addedfrom'] != get_staff_user_id() || !empty($list['assigned'])) { ?>
            <p class="font-medium-xs mtop15 tw-text-neutral-500 checklist-item-info">
                <?php
                if ($list['addedfrom'] != get_staff_user_id()) {
                    echo _l('task_created_by', get_staff_full_name($list['addedfrom']));
                }
                if ($list['addedfrom'] != get_staff_user_id() && $list['finished'] == 1) {
                    echo ' - ';
                }
                if ($list['finished'] == 1) {
                    echo _l('task_checklist_item_completed_by', get_staff_full_name($list['finished_from']));
                }
                if (($list['addedfrom'] != get_staff_user_id() || $list['finished'] == 1) && !empty($list['assigned'])) {
                    echo ' - ';
                }
                if (!empty($list['assigned'])) {
                    echo _l('task_checklist_assigned', get_staff_full_name($list['assigned']));
                }

                ?>
            </p>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
<script>
$(function() {
    $("#checklist-items").sortable({
        helper: 'clone',
        items: 'div.checklist',
        update: function(event, ui) {
            update_checklist_order();
        }
    });
    setTimeout(function() {
        do_task_checklist_items_height();
    }, 200);

    init_selectpicker();
    var _hideCompletedItems = '<?php echo $hide_completed_items?>'
    if (_hideCompletedItems == 1) {
        toggle_completed_checklist_items_visibility();
    }
});

function toggle_completed_checklist_items_visibility(el, forceShow) {
    var _task_checklist_items = $("body").find("input[name='checklist-box']");
    $.each(_task_checklist_items, function() {
        var $this = $(this);
        if ($this.prop('checked') == true) {
            $this.closest('.checklist ').toggleClass('hide');
        }
    });
    if (typeof el != 'undefined') {
        var _hideCompleted = $(el).data('hide');
        $(el).addClass('hide');
        $(el).siblings().removeClass('hide');
        $.post(admin_url + 'staff/save_completed_checklist_visibility', {
            task_id: "<?php echo $task_id ?>",
            hideCompleted: _hideCompleted
        }, "json");
    }
}

function save_checklist_assigned_staff(staffId, list_id) {
    $.post(
        admin_url + 'tasks/save_checklist_assigned_staff', {
            assigned: staffId,
            checklistId: list_id,
            taskId: "<?php echo $task_id ?>",
        }
    ).done(function(response) {
        init_tasks_checklist_items(false, "<?php echo $task_id ?>");
    });
}
</script>