<div id="chat_box" class="chat_box" style="display: none">
    <div class="ox-chat">
        <div class="ox-chat__header">
            <div class="ox-chat__header-info">
                <span class="ox-chat__header-icon"><i class="fa fa-comments"></i></span>
                <div class="ox-chat__header-text">
                    <strong class="chat-user"></strong>
                    <span class="ox-chat__header-sub">محادثة المجموعة</span>
                </div>
            </div>
            
            <span class="ox-chat__close close-chat" title="إغلاق"><i class="fa fa-times"></i></span>
        </div>
        <div class="ox-chat__body chat-area"></div>
        <div class="ox-chat__footer">
            <textarea class="ox-chat__input chat_input" rows="1" placeholder="اكتب رسالتك هنا..."></textarea>
            <button class="ox-chat__send btn-chat" type="button" data-to-user="" disabled title="إرسال">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </div>
    <input type="hidden" id="to_user_id" value="" />
</div>

<style>
.chat_box { position: fixed; bottom: 20px; inset-inline-end: 20px; width: 360px; max-width: calc(100vw - 24px); z-index: 9999; }
.ox-chat { display: flex; flex-direction: column; height: 480px; max-height: calc(100vh - 100px); background: #1b2130; border: 1px solid #2c3547; border-radius: 14px; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,.45); font-family: 'Cairo', 'Poppins', sans-serif; }
.ox-chat__header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: linear-gradient(135deg, #1e3a5f, #17263b); border-bottom: 1px solid #2c3547; }
.ox-chat__header-info { display: flex; align-items: center; gap: 10px; min-width: 0; }
.ox-chat__header-icon { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.12); color: #4da3ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ox-chat__header-text { display: flex; flex-direction: column; min-width: 0; }
.ox-chat__header-text strong { color: #fff; font-size: .95rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ox-chat__header-sub { color: #93a1b8; font-size: .72rem; }
.ox-chat__close { cursor: pointer; color: #93a1b8; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background .15s, color .15s; flex-shrink: 0; }
.ox-chat__close:hover { background: rgba(255,255,255,.1); color: #fff; }
.ox-chat__body { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 14px; }
.ox-chat__footer { display: flex; align-items: flex-end; gap: 8px; padding: 10px 12px; border-top: 1px solid #2c3547; background: #161b27; }
.ox-chat__input { flex: 1; resize: none; max-height: 90px; background: #1f2635; border: 1px solid #2c3547; border-radius: 10px; color: #e7ecf5; padding: 9px 12px; font-size: .88rem; font-family: inherit; line-height: 1.4; }
.ox-chat__input:focus { outline: none; border-color: #3b82f6; }
.ox-chat__send { width: 38px; height: 38px; border-radius: 10px; border: none; background: #3b82f6; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; transition: background .15s, opacity .15s; }
.ox-chat__send:disabled { opacity: .4; cursor: not-allowed; }
.ox-chat__send:not(:disabled):hover { background: #2563eb; }

/* ── message bubble (WhatsApp-style: avatar, name+gender, bubble with inline time) ── */
.ox-msg { display: flex; gap: 8px; max-width: 100%; align-items: flex-end; }
.ox-msg__avatar-wrap { position: relative; flex-shrink: 0; }
.ox-msg__avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; display: block; background: #2c3547; border: 1px solid #2c3547; }
.ox-msg__col { display: flex; flex-direction: column; max-width: 80%; min-width: 0; }
.ox-msg__meta { display: flex; align-items: center; gap: 4px; margin-bottom: 2px; padding: 0 4px; }
.ox-msg__name { font-size: .72rem; font-weight: 700; color: #93a1b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ox-msg__gender { font-size: .78rem; line-height: 1; }
.ox-msg__role { font-size: .62rem; font-weight: 700; color: #17263b; background: #f5c518; border-radius: 999px; padding: 1px 7px; line-height: 1.5; flex-shrink: 0; }
.ox-msg__bubble {
    position: relative;
    background: #262f42; color: #e7ecf5;
    padding: 8px 10px 6px 10px; border-radius: 14px 14px 14px 4px;
    font-size: .89rem; line-height: 1.45; word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,.25);
    display: inline-flex; flex-wrap: wrap; align-items: flex-end; gap: 6px 10px;
}
.ox-msg__text { white-space: pre-line; flex: 1 1 auto; }
.ox-msg__time { flex-shrink: 0; font-size: .64rem; color: #93a1b8; opacity: .85; margin-inline-start: auto; align-self: flex-end; }

/* incoming (other members) */
.ox-msg--theirs .ox-msg__bubble { border-inline-start: 2px solid #3b82f6; }

/* ── teacher: gold crown badge on the avatar + a distinct gold-accented bubble so the
   teacher always stands out from regular students, for every viewer in the group ── */
.ox-msg__crown {
    position: absolute; top: -6px; inset-inline-end: -6px;
    font-size: .72rem; line-height: 1;
    background: #f5c518; border: 2px solid #1b2130; border-radius: 50%;
    width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;
}
.ox-msg--teacher .ox-msg__avatar { border-color: #f5c518; }
.ox-msg--teacher.ox-msg--theirs .ox-msg__bubble { background: #3a2f14; border-inline-start: 2px solid #f5c518; }
.ox-msg--teacher.ox-msg--theirs .ox-msg__name { color: #f5c518; }

/* my own messages: WhatsApp hides the name on your own bubbles and flips the tail */
.ox-msg--mine { flex-direction: row-reverse; margin-inline-start: auto; }
.ox-msg--mine .ox-msg__col { align-items: flex-end; }
.ox-msg--mine .ox-msg__meta { display: none; }
.ox-msg--mine .ox-msg__bubble { background: #3b82f6; color: #fff; border-radius: 14px 14px 4px 14px; }
.ox-msg--mine .ox-msg__time { color: rgba(255,255,255,.75); }
/* even on your own bubble (teacher viewing their own message) keep the crown on the avatar */
.ox-msg--mine.ox-msg--teacher .ox-msg__bubble { background: linear-gradient(135deg, #3b82f6, #2f6fe0); }

.chat_box .loader { color: #93a1b8; font-size: 1.2rem; align-self: center; }
</style>
