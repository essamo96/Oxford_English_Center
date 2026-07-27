$(function () {

    let pusher = new Pusher($("#pusher_app_key").val(), {
        cluster: $("#pusher_cluster").val(),
        encrypted: true
    });

    let channel = pusher.subscribe('chat');


    // on click on any chat btn render the chat box
    // delegated on document: chat-toggle buttons also appear inside content loaded later via
    // AJAX (e.g. the "Launch Group Chat" button on the group-details/#Info screen) — a direct
    // binding here would only ever reach the buttons that existed at page load.
    $(document).on("click", ".chat-toggle", function (e) {
        e.preventDefault();

        let ele = $(this);
        let group_id = ele.attr("data-id");
        let username = ele.attr("data-user");
        let user_type = $("#user_type").val();
       //lert(user_type);
        cloneChatBox(group_id, username, function () {

            let chatBox = $("#chat_box_" + group_id);

            if (!chatBox.hasClass("chat-opened")) {

                chatBox.addClass("chat-opened").slideDown("fast");

                loadLatestMessages(chatBox, group_id, user_type);

                chatBox.find(".chat-area").animate({scrollTop: chatBox.find(".chat-area").offset().top + chatBox.find(".chat-area").outerHeight(true)}, 800, 'swing');
            }
        });
    });

    // on close chat close the chat box but don't remove it from the dom
    // delegated for the same reason as .chat_input above — real chat boxes are clones of the
    // hidden #chat_box template, so their .close-chat is a different DOM node each time.
    $(document).on("click", ".close-chat", function (e) {

        $(this).parents("div.chat-opened").removeClass("chat-opened").slideUp("fast");
    });


    // on change chat input text toggle the chat btn disabled state
    // NOTE: delegated on document — the chat box (and its .chat_input) is cloned into the DOM
    // dynamically after the page loads, so a direct $(".chat_input").on(...) binding here would
    // never actually attach to it and the send button would stay disabled forever.
    $(document).on("change keyup input", ".chat_input", function (e) {
        let box = $(this).closest(".chat_box, [id^='chat_box_']");
        if ($(this).val() != "") {
            box.find(".btn-chat").prop("disabled", false);
        } else {
            box.find(".btn-chat").prop("disabled", true);
        }
    });


    // on click the btn send the message
    $(document).on("click", ".btn-chat", function (e) {
        send($(this).attr('data-to-user'), $("#chat_box_" + $(this).attr('data-to-user')).find(".chat_input").val());
    });

    // listen for the send event, this event will be triggered on click the send btn
    channel.bind('send', function (data) {
        displayMessage(data.data);
    });


    // handle the scroll top of any chat box
    // the idea is to load the last messages by date depending of last message
    // that's already loaded on the chat box
    let lastScrollTop = 0;

    $(".chat-area").on("scroll", function (e) {
        let st = $(this).scrollTop();

        if (st < lastScrollTop) {

            fetchOldMessages($(this).parents(".chat-opened").find("#to_user_id").val(), $(this).find(".msg_container:first-child").attr("data-message-id"));
        }

        lastScrollTop = st;
    });

    // listen for the oldMsgs event, this event will be triggered on scroll top
    channel.bind('oldMsgs', function (data) {
        displayOldMessages(data);
    });
});

/**
 * loaderHtml
 *
 * @returns {string}
 */
function loaderHtml() {
    return '<i class="glyphicon glyphicon-refresh loader"></i>';
}

/**
 * cloneChatBox
 *
 * this helper function make a copy of the html chat box depending on receiver user
 * then append it to 'chat-overlay' div
 *
 * @param user_id
 * @param username
 * @param callback
 */
function cloneChatBox(user_id, username, callback)
{
    if ($("#chat_box_" + user_id).length == 0) {

        let cloned = $("#chat_box").clone(true);

        // change cloned box id
        cloned.attr("id", "chat_box_" + user_id);

        cloned.find(".chat-user").text(username);

        cloned.find(".btn-chat").attr("data-to-user", user_id);

        cloned.find("#to_user_id").val(user_id);

        $("#chat-overlay").append(cloned);
    }

    callback();
}

/**
 * loadLatestMessages
 *
 * this function called on load to fetch the latest messages
 *
 * @param container
 * @param user_id
 */
function loadLatestMessages(container, group_id, user_type)
{
    let chat_area = container.find(".chat-area");

    chat_area.html("");

    $.ajax({
        url: base_url + "/load-latest-messages_" + user_type,
        data: {group_id: group_id, _token: $("meta[name='csrf-token']").attr("content")},
        method: "GET",
        dataType: "json",
        beforeSend: function () {
            if (chat_area.find(".loader").length == 0) {
                chat_area.html(loaderHtml());
            }
        },
        success: function (response) {
            if (response.state == 1) {
                response.messages.map(function (val, index) {
                    $(val).appendTo(chat_area);
                });
            }
        },
        complete: function () {
            chat_area.find(".loader").remove();
        }
    });
}

/**
 * send
 *
 * this function is the main function of chat as it send the message
 *
 * @param to_user
 * @param message
 */
function send(to_user, message)
{
    let chat_box = $("#chat_box_" + to_user);
    let chat_area = chat_box.find(".chat-area");
    let user_type = $("#user_type").val();

    $.ajax({
        url: base_url + "/send_"+user_type,
        data: {to_user: to_user, message: message, _token: $("meta[name='csrf-token']").attr("content")},
        method: "POST",
        dataType: "json",
        beforeSend: function () {
            if (chat_area.find(".loader").length == 0) {
                chat_area.append(loaderHtml());
            }
        },
        success: function (response) {
        },
        complete: function () {
            chat_area.find(".loader").remove();
            chat_box.find(".btn-chat").prop("disabled", true);
            chat_box.find(".chat_input").val("");
            chat_area.animate({scrollTop: chat_area.offset().top + chat_area.outerHeight(true)}, 800, 'swing');
        }
    });
}

/**
 * fetchOldMessages
 *
 * this function load the old messages if scroll up triggerd
 *
 * @param to_user
 * @param old_message_id
 */
function fetchOldMessages(to_user, old_message_id)
{
    let chat_box = $("#chat_box_" + to_user);
    let chat_area = chat_box.find(".chat-area");

    $.ajax({
        url: base_url + "/fetch-old-messages",
        data: {to_user: to_user, old_message_id: old_message_id, _token: $("meta[name='csrf-token']").attr("content")},
        method: "GET",
        dataType: "json",
        beforeSend: function () {
            if (chat_area.find(".loader").length == 0) {
                chat_area.prepend(loaderHtml());
            }
        },
        success: function (response) {
        },
        complete: function () {
            chat_area.find(".loader").remove();
        }
    });
}

/**
 * getMessageSenderHtml
 *
 * this is the message template for the sender
 *
 * @param message
 * @returns {string}
 */
function chatAvatarUrl(message)
{
    return message.image ? (base_url + '/' + message.image) : (base_url + '/assets/oxford/images/user-avatar.png');
}

function escapeHtml(str)
{
    return String(str == null ? '' : str).replace(/[&<>"']/g, function (m) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
    });
}

// gender: 1 = male (🦁), 2 or legacy 0 = female (🦋) — only ever set for students, teachers
// have no gender column so their messages simply carry no emoji.
function chatGenderEmoji(message)
{
    var g = parseInt(message.gender, 10);
    if (g === 1) return '🦁';
    if (g === 2 || g === 0) return '🦋';
    return '';
}

function chatBubbleHtml(message, mine)
{
    var emoji = chatGenderEmoji(message);
    // user_type: 0 = student, 1 = teacher (see Message model / MessagesController)
    var isTeacher = parseInt(message.user_type, 10) === 1;
    return `
    <div class="ox-msg msg_container ${mine ? 'ox-msg--mine base_sent' : 'ox-msg--theirs base_receive'} ${isTeacher ? 'ox-msg--teacher' : ''}" data-message-id="${message.id}">
        <div class="ox-msg__avatar-wrap">
            <img class="ox-msg__avatar" src="${chatAvatarUrl(message)}" alt="${escapeHtml(message.fromUserName)}">
            ${isTeacher ? '<span class="ox-msg__crown" title="المعلم">👑</span>' : ''}
        </div>
        <div class="ox-msg__col">
            <div class="ox-msg__meta">
                <span class="ox-msg__name">${escapeHtml(message.fromUserName)}</span>
                ${isTeacher ? '<span class="ox-msg__role">المعلم</span>' : ''}
                ${emoji ? '<span class="ox-msg__gender">' + emoji + '</span>' : ''}
            </div>
            <div class="ox-msg__bubble">
                <span class="ox-msg__text">${escapeHtml(message.content).replace(/\n/g, '<br>')}</span>
                <time class="ox-msg__time" datetime="${message.dateTimeStr}">${message.dateHumanReadable}</time>
            </div>
        </div>
    </div>
    `;
}

function getMessageSenderHtml(message)
{
    return chatBubbleHtml(message, true);
}

/**
 * getMessageReceiverHtml
 *
 * this is the message template for the receiver
 *
 * @param message
 * @returns {string}
 */
function getMessageReceiverHtml(message)
{
    return chatBubbleHtml(message, false);
}

/**
 * This function called by the send event triggered from pusher to display the message
 *
 * @param message
 */
function displayMessage(message)
{
    let current_group = $("#current_group").val();
    let alert_sound = document.getElementById("chat-alert-sound");
    mygroups = current_group.split(",");
    if ($("#current_user").val() == message.from_user_id) {

        let messageLine = getMessageSenderHtml(message);

        $("#chat_box_" + message.group_id).find(".chat-area").append(messageLine);

    } else if ($.inArray(String(message.group_id), mygroups) !== -1) {

        alert_sound.play();

        // for the receiver user check if the chat box is already opened otherwise open it
        cloneChatBox(message.group_id, message.fromUserName, function () {

            let chatBox = $("#chat_box_" + message.group_id);

            if (!chatBox.hasClass("chat-opened")) {

                chatBox.addClass("chat-opened").slideDown("fast");

                loadLatestMessages(chatBox, message.group_id);

                chatBox.find(".chat-area").animate({scrollTop: chatBox.find(".chat-area").offset().top + chatBox.find(".chat-area").outerHeight(true)}, 800, 'swing');
            } else {

                let messageLine = getMessageReceiverHtml(message);

                // append the message for the receiver user
                $("#chat_box_" + message.group_id).find(".chat-area").append(messageLine);
            }
        });
    }
}

function displayOldMessages(data)
{
    if (data.data.length > 0) {

        data.data.map(function (val, index) {
            $("#chat_box_" + data.to_user).find(".chat-area").prepend(val);
        });
    }
}