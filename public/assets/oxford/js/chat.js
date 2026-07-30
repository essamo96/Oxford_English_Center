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

    // an admin (or the teacher) deleted a message — drop it from this box too,
    // instead of leaving it on screen until the next reload
    channel.bind('message-deleted', function (data) {
        if (!data || !data.data) return;
        $("#chat_box_" + data.data.group_id)
            .find('.msg_container[data-message-id="' + data.data.message_id + '"]')
            .fadeOut(200, function () { $(this).remove(); });
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
 * Upgrade any voice notes inside a freshly injected node into the styled player.
 * No-op when voice-player.js is not on the page.
 */
function chatEnhance(scope) {
    if (window.OxVoicePlayer) {
        window.OxVoicePlayer.initAll(scope || document);
    }
}

/**
 * Switch one group's chat box between writable and read-only.
 *
 * Read-only is used when an admin freezes the group's chat or bans this student:
 * the history stays fully readable, only the composer is replaced by a notice.
 * The server enforces the same rule, so this is presentation, not security.
 *
 * Exposed on window so the notification channel in the dashboard layout can flip
 * an already-open box the moment the ban/lock event arrives.
 */
function setChatPermission(group_id, canSend, reason, canView) {
    var box = $("#chat_box_" + group_id);
    if (box.length === 0) return;

    var footer = box.find(".ox-chat__footer");
    var notice = box.find(".ox-chat__blocked");
    // canView defaults to true: only a full ban passes it as false.
    var viewable = (typeof canView === "undefined") ? true : !!canView;

    if (canSend && viewable) {
        footer.show();
        notice.remove();
        box.find(".chat-area").show();
        box.find(".ox-chat__banned").remove();
        return;
    }

    footer.hide();

    if (!viewable) {
        // A full ban withholds the history entirely. The server already refuses to
        // send it, so this only makes the box honest about why it is empty.
        box.find(".chat-area").empty().hide();
        if (box.find(".ox-chat__banned").length === 0) {
            $('<div class="ox-chat__banned">'
                + '<div class="ox-chat__banned-icon">⛔</div>'
                + '<div class="ox-chat__banned-text"></div>'
                + '</div>').appendTo(box.find(".ox-chat"));
        }
        box.find(".ox-chat__banned-text")
           .text(reason || "تم حظرك من هذه المجموعة.");
        notice.remove();
        return;
    }

    var text = reason || "لا يمكنك إرسال رسائل في هذه المجموعة حالياً.";
    if (notice.length) {
        notice.find(".ox-chat__blocked-text").text(text);
    } else {
        $('<div class="ox-chat__blocked">'
            + '<i class="fa fa-lock"></i> '
            + '<span class="ox-chat__blocked-text"></span>'
            + '</div>').appendTo(box.find(".ox-chat")).find(".ox-chat__blocked-text").text(text);
    }
}

window.oxSetChatPermission = setChatPermission;

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
                chatEnhance(chat_area[0]);

                // A frozen group or a banned student gets a read-only composer with
                // the reason shown, instead of a send button that would 403.
                if (typeof response.can_send !== 'undefined') {
                    setChatPermission(group_id, response.can_send, response.block_reason, response.can_view);
                }
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
        error: function (xhr) {
            // 403 = the group was frozen or this student was banned since the box
            // was opened. Show the reason and lock the composer rather than
            // silently swallowing the failure.
            var res = xhr.responseJSON;
            if (xhr.status === 403 && res) {
                setChatPermission(to_user, false, res.message, res.can_view);
            }
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
function chatDefaultAvatar()
{
    return base_url + '/assets/oxford/images/user-avatar.png';
}

function chatAvatarUrl(message)
{
    // The server resolves this now (App\Support\ChatAvatar): the three sender tables
    // store `image` in three different shapes — a bare filename for admins, an
    // absolute URL for teachers, a relative path for students — so prefixing base_url
    // produced a broken avatar for everyone except students.
    if (message.avatar_url) {
        return message.avatar_url;
    }
    if (!message.image) {
        return chatDefaultAvatar();
    }
    // Older payloads (or a cached tab) without avatar_url: handle the shapes inline.
    if (/^https?:\/\//i.test(message.image)) {
        return message.image;
    }
    return base_url + '/' + String(message.image).replace(/^\/+/, '');
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

// Renders an attachment (image / voice note / generic file) inside the bubble.
// Admin comments are the only sender that can currently carry one.
function chatAttachmentHtml(message)
{
    if (!message.attachment) return '';

    var url = base_url + '/' + message.attachment;
    var name = escapeHtml(message.attachment_name || 'مرفق');

    if (message.attachment_type === 'image') {
        return '<span class="ox-msg__attachment"><a href="' + url + '" target="_blank" rel="noopener">' +
               '<img class="ox-msg__image" src="' + url + '" alt="' + name + '"></a></span>';
    }
    if (message.attachment_type === 'audio') {
        // Mirrors resources/views/frontend/chat/message-line.blade.php so a live voice
        // note looks the same as one loaded from history. voice-player.js enhances it.
        var bars = '';
        for (var i = 0; i < 24; i++) {
            bars += '<span class="ox-voice__bar" style="height:' + (25 + ((message.id * (i + 3)) % 70)) + '%"></span>';
        }
        return '<span class="ox-msg__attachment">' +
               '<span class="ox-voice" data-voice-player>' +
                   '<button type="button" class="ox-voice__btn" data-voice-toggle aria-label="تشغيل">' +
                       '<i class="fa fa-play" data-voice-icon data-icon-play="fa-play" data-icon-pause="fa-pause"></i>' +
                   '</button>' +
                   '<span class="ox-voice__body">' +
                       '<span class="ox-voice__wave" data-voice-seek>' +
                           '<span class="ox-voice__progress" data-voice-progress></span>' + bars +
                       '</span>' +
                       '<span class="ox-voice__meta"><span data-voice-time>0:00</span></span>' +
                   '</span>' +
                   '<audio preload="metadata" src="' + url + '" data-voice-audio style="display:none"></audio>' +
               '</span></span>';
    }
    return '<span class="ox-msg__attachment"><a class="ox-msg__file" href="' + url + '" target="_blank" rel="noopener">' +
           '<i class="fa fa-paperclip"></i> ' + name + '</a></span>';
}

function chatBubbleHtml(message, mine)
{
    var emoji = chatGenderEmoji(message);
    // user_type: 0 = student, 1 = teacher, 2 = admin (see Message model / MessagesController)
    var type = parseInt(message.user_type, 10);
    var isTeacher = type === 1;
    var isAdmin   = type === 2;
    var text = message.content ? escapeHtml(message.content).replace(/\n/g, '<br>') : '';

    // The owning teacher can moderate students from a live bubble too — the flag
    // is set by whoever set up the moderation menu for this page.
    var canModerate = (type === 0) && window.oxChatCanModerate === true;
    var modAttr = canModerate
        ? ' data-moderate-student="' + escapeHtml(message.from_user_id || message.from_user) + '"'
        : '';

    return `
    <div class="ox-msg msg_container ${mine ? 'ox-msg--mine base_sent' : 'ox-msg--theirs base_receive'} ${isTeacher ? 'ox-msg--teacher' : ''} ${isAdmin ? 'ox-msg--admin' : ''}" data-message-id="${message.id}">
        <div class="ox-msg__avatar-wrap"${modAttr}>
            <img class="ox-msg__avatar" src="${chatAvatarUrl(message)}" alt="${escapeHtml(message.fromUserName)}"
                 onerror="this.onerror=null;this.src='${chatDefaultAvatar()}';">
            ${isTeacher ? '<span class="ox-msg__crown" title="المعلم">👑</span>' : ''}
            ${isAdmin ? '<span class="ox-msg__crown ox-msg__shield" title="الإدارة">🛡️</span>' : ''}
        </div>
        <div class="ox-msg__col">
            <div class="ox-msg__meta">
                <span class="ox-msg__name">${escapeHtml(message.fromUserName)}</span>
                ${isTeacher ? '<span class="ox-msg__role">المعلم</span>' : ''}
                ${isAdmin ? '<span class="ox-msg__role ox-msg__role--admin">الإدارة</span>' : ''}
                ${emoji ? '<span class="ox-msg__gender">' + emoji + '</span>' : ''}
            </div>
            <div class="ox-msg__bubble">
                ${text ? '<span class="ox-msg__text">' + text + '</span>' : ''}
                ${chatAttachmentHtml(message)}
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

    // Admin senders (user_type 2) come from the `users` table, whose ids overlap with
    // student/teacher ids — without the type check an admin comment whose id happened to
    // match the viewer's would render as the viewer's own outgoing message.
    let my_type = parseInt($("#user_type").val() === 'teacher' ? 1 : 0, 10);
    let msg_type = parseInt(message.user_type, 10);

    if (msg_type === my_type && $("#current_user").val() == message.from_user_id) {

        let messageLine = getMessageSenderHtml(message);

        let area = $("#chat_box_" + message.group_id).find(".chat-area");
        area.append(messageLine);
        chatEnhance(area[0]);

    } else if ($.inArray(String(message.group_id), mygroups) !== -1) {

        alert_sound.play();

        // for the receiver user check if the chat box is already opened otherwise open it
        cloneChatBox(message.group_id, message.fromUserName, function () {

            let chatBox = $("#chat_box_" + message.group_id);

            if (!chatBox.hasClass("chat-opened")) {

                chatBox.addClass("chat-opened").slideDown("fast");

                // user_type was omitted here, producing "/load-latest-messages_undefined"
                // and leaving a freshly auto-opened box permanently empty.
                loadLatestMessages(chatBox, message.group_id, $("#user_type").val());

                chatBox.find(".chat-area").animate({scrollTop: chatBox.find(".chat-area").offset().top + chatBox.find(".chat-area").outerHeight(true)}, 800, 'swing');
            } else {

                let messageLine = getMessageReceiverHtml(message);

                // append the message for the receiver user
                let rArea = $("#chat_box_" + message.group_id).find(".chat-area");
                rArea.append(messageLine);
                chatEnhance(rArea[0]);
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