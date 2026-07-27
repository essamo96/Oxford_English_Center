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

/* ── message bubble (shared by initial render + realtime append) ── */
.ox-msg { display: flex; gap: 10px; max-width: 100%; }
.ox-msg__avatar { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; flex-shrink: 0; background: #2c3547; }
.ox-msg__col { display: flex; flex-direction: column; max-width: 78%; }
.ox-msg__meta { margin-bottom: 3px; }
.ox-msg__name { font-size: .74rem; font-weight: 700; color: #93a1b8; }
.ox-msg__bubble { background: #262f42; color: #e7ecf5; padding: 9px 13px; border-radius: 12px 12px 12px 3px; font-size: .87rem; line-height: 1.5; word-break: break-word; white-space: pre-line; }
.ox-msg__time { margin-top: 3px; font-size: .68rem; color: #6b7994; }

.ox-msg--mine { flex-direction: row-reverse; margin-inline-start: auto; }
.ox-msg--mine .ox-msg__col { align-items: flex-end; }
.ox-msg--mine .ox-msg__bubble { background: #3b82f6; color: #fff; border-radius: 12px 12px 3px 12px; }
.ox-msg--mine .ox-msg__time { align-self: flex-end; }

.chat_box .loader { color: #93a1b8; font-size: 1.2rem; align-self: center; }
</style>
