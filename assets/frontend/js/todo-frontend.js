jQuery(document).ready(function () {
  const action = "to_do_frontend_ajax";
  const to_do_id = jQuery("#to-do-id").val();

  let draggedToDoId;
  let draggedDoneId;

  jQuery("#add-button").on("click", function (e) {
    e.preventDefault();

    if (jQuery("#task-input").val() !== "") {
      const task_title = jQuery("#task-input").val();
      const task_status = "in_progress";

      jQuery.ajax({
        method: "POST",
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "add_custom_todo",
          task_title: task_title,
          task_status: task_status,
          to_do_id: to_do_id,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          jQuery("#add-form")[0].reset();
          // location.reload();
          load_tasks(to_do_id);
        },
        error: function (response) {
          if (response.responseText) {
            alert(JSON.parse(response.responseText).data.message);
          }
        },
      });
    }
  });

  jQuery(document).on('drag', '#todo-item', function () {
    draggedToDoId = jQuery(this).data('id');
  })

  jQuery(document).on('drag', '#done-item', function () {
    draggedDoneId = jQuery(this).data('id');
  })

  jQuery(document).on('drop', '#done-lists', function (e) {

    setTimeout(function (){
      jQuery.ajax({
        method: 'POST',
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "update_custom_todo",
          id: draggedToDoId,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          if (response) {
            load_tasks(to_do_id);
          }
        },
        error: function (error) {
          console.log(error);
        },
      })
    },100)
  })

  jQuery(document).on('drop', '#todo-lists', function (e) {
    setTimeout(function (){
      jQuery.ajax({
        method: 'POST',
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "undo_completion",
          id: draggedDoneId,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          if (response) {
            load_tasks(to_do_id);
          }
        },
        error: function (error) {
          console.log(error);
        },
      })
    },100)
  })

  jQuery(document).on("click", ".delete-task", function () {
    const id = jQuery(this).data("id");

    setTimeout(function () {
      jQuery.ajax({
        method: "POST",
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "delete_custom_todo",
          id: id,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          if (response) {
            load_tasks(to_do_id);
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    }, 100);
  });

  jQuery(document).on("click", ".done-task", function () {
    const id = jQuery(this).data("id");
    setTimeout(function () {
      jQuery.ajax({
        method: "POST",
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "update_custom_todo",
          id: id,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          if (response) {
            load_tasks(to_do_id);
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    }, 100);
  });

  function load_tasks(to_do_id) {
    //load uncompleted todos...
    jQuery(".lds-roller").css("display", "inline-block");
    setTimeout(() => {
      jQuery.ajax({
        method: "POST",
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "get_all_custom_todo",
          to_do_id: to_do_id,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          if (response.data) {
            jQuery("#todo-lists").html(response.data);
            jQuery(".lds-roller").css("display", "none");
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    }, 100);

    //load completed todos..
    setTimeout(() => {
      jQuery.ajax({
        method: "POST",
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: action,
          route: "get_all_done_todo",
          to_do_id: to_do_id,
          fetch: 1,
          nonce: ajax_obj.nonce,
        },
        success: function (response) {
          if (response.data) {
            jQuery("#done-lists").html(response.data);
            jQuery(".lds-roller").css("display", "none");
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    }, 100);
  }
});
