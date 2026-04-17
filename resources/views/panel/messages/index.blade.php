@extends('layouts.app')

@section('title', __('Branch Chat') . ' - RGC')

@section('content')
<div class="chat-shell">
    <div class="chat-shell-header">
        <div>
            <span class="section-kicker">{{ __('Branch Chat') }}</span>
            <h1 class="mt-4 text-2xl font-semibold">{{ __('Branch Chat') }}</h1>
            <p class="mt-2 text-sm text-black/65">{{ __('Use this space for branch coordination, quick updates, replies, and file sharing with the people in your branch.') }}</p>
            <p class="mt-3 text-sm text-black/70">{{ __('Speak with grace, clarity, and care so your branch stays united in prayer, service, and communication.') }}</p>
        </div>
        <div class="chat-branch-chip">
            <span>{{ __('Active branch') }}</span>
            <strong>{{ $branchName }}</strong>
        </div>
    </div>

    <section class="chat-thread-shell">
        <div class="chat-thread-header">
            <div class="chat-thread-title-wrap">
                <span class="chat-thread-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($branchName, 0, 1)) }}</span>
                <div>
                    <strong>{{ $branchName }}</strong>
                    <p>{{ __('Shared branch conversation') }}</p>
                </div>
            </div>
            <span class="chat-thread-status" data-chat-status>
                <span>{{ __('Live now') }}</span>
                <span class="chat-thread-badge hidden" data-chat-badge>0</span>
            </span>
        </div>

        <button class="chat-jump hidden" type="button" data-chat-jump>
            {{ __('New messages') }}
        </button>

        <div class="chat-thread" id="branch-chat-thread" data-current-user-id="{{ $currentUser->id }}">
            <ul class="chat-list" id="branch-chat-list">
                @php($previousDayKey = null)
                @forelse($messages as $message)
                    @php($isMine = $message->user_id === $currentUser->id)
                    @php($timestamp = optional($message->created_at)->timezone(config('app.timezone')))
                    @php($dayKey = $timestamp?->toDateString())
                    @php($dayLabel = $timestamp?->isToday() ? __('Today') : ($timestamp?->isYesterday() ? __('Yesterday') : $timestamp?->translatedFormat('d M Y')))
                    @php($attachments = $message->attachmentItems())
                    @php($parentMessage = $message->parent)

                    @if($dayKey !== $previousDayKey)
                        <li class="chat-day-divider" data-chat-day="{{ $dayKey }}">
                            <span>{{ $dayLabel }}</span>
                        </li>
                        @php($previousDayKey = $dayKey)
                    @endif

                    <li class="chat-row {{ $isMine ? 'is-mine' : 'is-other' }}" data-message-id="{{ $message->id }}">
                        @unless($isMine)
                            <span class="chat-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($message->user->name, 0, 1)) }}</span>
                        @endunless

                        <div class="chat-bubble {{ $isMine ? 'is-mine' : 'is-other' }}">
                            <div class="chat-meta">
                                <span class="chat-author">{{ $isMine ? __('You') : $message->user->name }}</span>
                                <span class="chat-time" title="{{ $timestamp?->format('d M Y H:i') }}">{{ $timestamp?->diffForHumans() }}</span>
                            </div>

                            @if($parentMessage && $parentMessage->church_id === $message->church_id)
                                <div class="chat-parent-preview {{ $parentMessage->user_id === $currentUser->id ? 'is-mine' : '' }}">
                                    <span class="chat-parent-label">{{ __('Replying to') }} {{ $parentMessage->user_id === $currentUser->id ? __('You') : ($parentMessage->user?->name ?? __('Branch member')) }}</span>
                                    <p>{{ $parentMessage->previewText() }}</p>
                                </div>
                            @endif

                            @if($attachments !== [])
                                <div class="chat-attachments-grid">
                                    @foreach($attachments as $index => $attachment)
                                        <div class="chat-attachment">
                                            @if($message->isImageAttachment($attachment))
                                                <a class="chat-attachment-image" href="{{ route('messages.attachments.show', ['message' => $message, 'index' => $index]) }}" target="_blank" rel="noopener">
                                                    <img src="{{ route('messages.attachments.show', ['message' => $message, 'index' => $index]) }}" alt="{{ $attachment['name'] ?? 'attachment' }}">
                                                </a>
                                            @else
                                                <div class="chat-attachment-file">
                                                    <span class="chat-attachment-file-mark">{{ $message->attachmentTypeLabel($attachment) }}</span>
                                                    <span class="chat-attachment-file-meta">
                                                        <strong>{{ $attachment['name'] ?? __('File') }}</strong>
                                                        @if($message->attachmentSizeLabel($attachment))
                                                            <small>{{ $message->attachmentSizeLabel($attachment) }}</small>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="chat-attachment-actions">
                                                <div class="chat-attachment-summary">
                                                    @if($message->isImageAttachment($attachment))
                                                        <span class="chat-attachment-file-mark">{{ $message->attachmentTypeLabel($attachment) }}</span>
                                                        <span class="chat-attachment-file-meta">
                                                            <strong>{{ $attachment['name'] ?? __('File') }}</strong>
                                                            @if($message->attachmentSizeLabel($attachment))
                                                                <small>{{ $message->attachmentSizeLabel($attachment) }}</small>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="chat-attachment-action-links">
                                                    <a class="chat-attachment-action-link" href="{{ route('messages.attachments.show', ['message' => $message, 'index' => $index]) }}" target="_blank" rel="noopener">{{ __('Open') }}</a>
                                                    <a class="chat-attachment-action-link" href="{{ route('messages.attachments.show', ['message' => $message, 'index' => $index, 'download' => 1]) }}">{{ __('Download') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(filled($message->message))
                                <p class="chat-body">{{ $message->message }}</p>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="chat-empty" data-chat-empty>
                        <span class="chat-empty-mark">{{ __('RGC') }}</span>
                        <div class="chat-empty-copy">
                            <strong>{{ __('Branch Chat') }}</strong>
                            <p>{{ __('No messages yet. Start the conversation from your branch.') }}</p>
                            <p>{{ __('A simple word of encouragement, prayer, or update can help your church family stay connected.') }}</p>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>

        <form
            class="chat-composer-app"
            method="POST"
            action="{{ route('messages.store') }}"
            enctype="multipart/form-data"
            data-chat-form
            data-feed-url="{{ route('messages.feed') }}"
            data-stream-url="{{ $streamUrl }}"
            data-prefill-message="{{ request('prefill', '') }}"
        >
            @csrf
            <input type="hidden" name="parent_id" value="" data-chat-parent-input>

            <div class="chat-compose-feedback hidden" data-chat-feedback></div>

            <div class="chat-reply-shell hidden" data-chat-reply-shell>
                <div class="chat-reply-preview" data-chat-reply-preview></div>
                <button class="chat-reply-cancel" type="button" data-chat-reply-cancel>{{ __('Cancel reply') }}</button>
            </div>

            <input
                class="hidden"
                id="branch-chat-attachment"
                type="file"
                name="attachments[]"
                accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                multiple
            >

            <div class="chat-compose-tools">
                <div class="chat-selected-files hidden" data-chat-selected-files></div>
            </div>

            <div class="chat-dropzone" data-chat-dropzone>
                <div class="chat-dropzone-inner">
                    <strong>{{ __('Attach files') }}</strong>
                    <p>{{ __('Release files to attach them') }}</p>
                </div>
            </div>

            <div class="chat-composer-shell">
                <label class="chat-action-button chat-action-button--attach" for="branch-chat-attachment" aria-label="{{ __('Attach files') }}" title="{{ __('Attach files') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21.44 11.05 12.25 20.24a6 6 0 0 1-8.49-8.49l9.2-9.19a4 4 0 1 1 5.65 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.49" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    </svg>
                    <span class="sr-only">{{ __('Attach files') }}</span>
                </label>

                <div class="chat-input-shell">
                    <label class="sr-only" for="branch-chat-message">{{ __('Type a message') }}</label>
                    <textarea
                        class="textarea-rgc chat-input"
                        id="branch-chat-message"
                        name="message"
                        rows="1"
                        placeholder="{{ __('Write a message to your branch...') }}"
                    ></textarea>
                </div>

                <button class="chat-action-button chat-action-button--send" type="submit" aria-label="{{ __('Send message') }}" title="{{ __('Send message') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 11.5 19.5 4l-3.7 16-3.65-5.85L4 11.5Z" fill="currentColor"/>
                        <path d="M11.9 14.2 19.5 4" fill="none" stroke="rgba(255,255,255,.9)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4"/>
                    </svg>
                    <span class="sr-only">{{ __('Send') }}</span>
                </button>
            </div>
        </form>
    </section>
</div>

<script>
(() => {
    const thread = document.getElementById('branch-chat-thread');
    const list = document.getElementById('branch-chat-list');
    const form = document.querySelector('[data-chat-form]');
    const textarea = document.getElementById('branch-chat-message');
    const fileInput = document.getElementById('branch-chat-attachment');
    const selectedFilesWrap = document.querySelector('[data-chat-selected-files]');
    const submitButton = form?.querySelector('button[type="submit"]');
    const badge = document.querySelector('[data-chat-badge]');
    const jumpButton = document.querySelector('[data-chat-jump]');
    const feedback = document.querySelector('[data-chat-feedback]');
    const replyShell = document.querySelector('[data-chat-reply-shell]');
    const replyPreview = document.querySelector('[data-chat-reply-preview]');
    const replyCancelButton = document.querySelector('[data-chat-reply-cancel]');
    const replyInput = document.querySelector('[data-chat-parent-input]');
    const dropzone = document.querySelector('[data-chat-dropzone]');
    const currentUserId = Number(thread?.dataset.currentUserId ?? 0);
    const feedUrl = form?.dataset.feedUrl ?? @json(route('messages.feed'));
    const streamUrl = form?.dataset.streamUrl ?? @json($streamUrl);
    const prefillMessage = form?.dataset.prefillMessage ?? '';
    const emptyTitle = @json(__('Branch Chat'));
    const emptyBody = @json(__('No messages yet. Start the conversation from your branch.'));
    const todayLabel = @json(__('Today'));
    const yesterdayLabel = @json(__('Yesterday'));
    const sendingLabel = @json(__('Sending...'));
    const youLabel = @json(__('You'));
    const emptyMessage = @json(__('Write a message to your branch...'));
    const newMessagesLabel = @json(__('New messages'));
    const noFileLabel = @json(__('No file selected'));
    const fileLabel = @json(__('File'));
    const openLabel = @json(__('Open'));
    const downloadLabel = @json(__('Download'));
    const deleteLabel = @json(__('Delete'));
    const deletingLabel = @json(__('Deleting...'));
    const confirmDeleteLabel = @json(__('Delete this message?'));
    const editLabel = @json(__('Edit'));
    const saveChangesLabel = @json(__('Save changes'));
    const cancelLabel = @json(__('Cancel'));
    const editingLabel = @json(__('Editing...'));
    const editedLabel = @json(__('Edited'));
    const editMessageLabel = @json(__('Edit this message'));
    const replyLabel = @json(__('Reply'));
    const replyingToLabel = @json(__('Replying to'));
    const cancelReplyLabel = @json(__('Cancel reply'));
    const attachmentSummaryLabel = @json(__('Attachment'));
    const maxAttachmentsReachedLabel = @json(__('You can attach up to :count files.', ['count' => 5]));
    const replyBranchOnlyLabel = @json(__('You can only reply to messages from your branch.'));
    const droppedFilesLabel = @json(__('Drop files here or tap to browse'));
    const dragOverLabel = @json(__('Release to attach your files'));
    const realtimeLabel = @json(__('Live now'));
    const streamLostLabel = @json(__('Realtime connection paused. Messages will refresh again shortly.'));
    const messageSentLabel = @json(__('Message sent.'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const originalTitle = document.title;
    let audioContext = null;
    let audioUnlocked = false;
    let cachedMessages = [];
    let editingMessageId = null;
    let editingMessageDraft = '';
    let pendingMessages = null;
    let selectedAttachments = [];
    let replyingTo = null;
    let eventSource = null;
    let fallbackInterval = null;
    let reconnectTimeout = null;

    if (!thread || !list || !form || !textarea || !fileInput) {
        return;
    }

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const initials = (name) => String(name || '')
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase() || 'R';

    const scrollToBottom = () => {
        thread.scrollTop = thread.scrollHeight;
    };

    const isNearBottom = () => (thread.scrollHeight - thread.scrollTop - thread.clientHeight) < 120;

    const setStatusTone = (mode = 'live') => {
        const status = document.querySelector('[data-chat-status]');

        if (!status) {
            return;
        }

        status.classList.toggle('is-warning', mode === 'warning');
    };

    const showFeedback = (message, tone = 'error') => {
        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.classList.remove('hidden', 'is-error', 'is-success', 'is-info');
        feedback.classList.add(`is-${tone}`);
    };

    const clearFeedback = () => {
        if (!feedback) {
            return;
        }

        feedback.textContent = '';
        feedback.classList.add('hidden');
        feedback.classList.remove('is-error', 'is-success', 'is-info');
    };

    const createAudioContext = () => {
        const AudioCtor = window.AudioContext || window.webkitAudioContext;

        if (!AudioCtor) {
            return null;
        }

        if (!audioContext) {
            audioContext = new AudioCtor();
        }

        return audioContext;
    };

    const unlockAudio = async () => {
        const context = createAudioContext();

        if (!context) {
            return;
        }

        try {
            if (context.state === 'suspended') {
                await context.resume();
            }

            audioUnlocked = true;
        } catch (error) {
            audioUnlocked = false;
        }
    };

    const playNotificationSound = async () => {
        const context = createAudioContext();

        if (!context || !audioUnlocked || document.visibilityState !== 'visible') {
            return;
        }

        try {
            if (context.state === 'suspended') {
                await context.resume();
            }

            const now = context.currentTime;
            const oscillator = context.createOscillator();
            const gain = context.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, now);
            oscillator.frequency.exponentialRampToValueAtTime(660, now + 0.12);

            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(0.035, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.18);

            oscillator.connect(gain);
            gain.connect(context.destination);
            oscillator.start(now);
            oscillator.stop(now + 0.18);
        } catch (error) {
            // Keep chat usable even if the browser blocks or fails audio playback.
        }
    };

    const resetUnreadState = () => {
        if (badge) {
            badge.textContent = '0';
            badge.classList.add('hidden');
        }

        if (jumpButton) {
            jumpButton.classList.add('hidden');
        }

        document.title = originalTitle;
    };

    const setUnreadState = (count) => {
        if (count <= 0) {
            resetUnreadState();
            return;
        }

        if (badge) {
            badge.textContent = String(count);
            badge.classList.remove('hidden');
        }

        if (jumpButton) {
            jumpButton.textContent = `${newMessagesLabel} (${count})`;
            jumpButton.classList.remove('hidden');
        }

        document.title = `(${count}) ${originalTitle}`;
    };

    const normalizeAttachments = (item) => {
        if (Array.isArray(item?.attachments) && item.attachments.length > 0) {
            return item.attachments;
        }

        if (item?.attachment) {
            return [item.attachment];
        }

        return [];
    };

    const formatDayLabel = (item) => item.sent_day_label || (item.sent_day_key === new Date().toISOString().slice(0, 10)
        ? todayLabel
        : yesterdayLabel);

    const currentMessageIds = () => Array.from(list.querySelectorAll('[data-message-id]'))
        .map((node) => String(node.dataset.messageId));

    const findMessageById = (id) => cachedMessages.find((item) => String(item.id) === String(id)) ?? null;

    const replyExcerpt = (item) => {
        if (item?.parent?.excerpt) {
            return item.parent.excerpt;
        }

        const message = String(item?.message ?? '').trim();

        if (message) {
            return message.length > 120 ? `${message.slice(0, 117)}...` : message;
        }

        const attachments = normalizeAttachments(item);

        if (attachments.length === 1) {
            return attachments[0]?.name ?? attachmentSummaryLabel;
        }

        if (attachments.length > 1) {
            return `${attachments.length} ${attachmentSummaryLabel.toLowerCase()}s`;
        }

        return '';
    };

    const renderParentPreview = (parent) => {
        if (!parent) {
            return '';
        }

        const attachmentMeta = parent.has_attachments
            ? `<small>${escapeHtml(parent.attachment_count === 1 ? attachmentSummaryLabel : `${parent.attachment_count} ${attachmentSummaryLabel.toLowerCase()}s`)}</small>`
            : '';

        return `
            <div class="chat-parent-preview ${parent.is_mine ? 'is-mine' : ''}">
                <span class="chat-parent-label">${escapeHtml(replyingToLabel)} ${escapeHtml(parent.author ?? youLabel)}</span>
                <p>${escapeHtml(parent.excerpt ?? '')}</p>
                ${attachmentMeta}
            </div>
        `;
    };

    const renderAttachmentActions = (attachment) => `
        <div class="chat-attachment-actions">
            <div class="chat-attachment-summary">
                <span class="chat-attachment-file-mark">${escapeHtml(attachment.type_label || fileLabel)}</span>
                <span class="chat-attachment-file-meta">
                    <strong>${escapeHtml(attachment.name ?? 'attachment')}</strong>
                    ${attachment.size_label ? `<small>${escapeHtml(attachment.size_label)}</small>` : ''}
                </span>
            </div>
            <div class="chat-attachment-action-links">
                <a class="chat-attachment-action-link" href="${escapeHtml(attachment.url)}" target="_blank" rel="noopener">${escapeHtml(openLabel)}</a>
                <a class="chat-attachment-action-link" href="${escapeHtml(attachment.download_url || attachment.url)}">${escapeHtml(downloadLabel)}</a>
            </div>
        </div>
    `;

    const renderAttachmentCard = (attachment) => {
        if (!attachment?.url) {
            return '';
        }

        if (attachment.is_image) {
            return `
                <div class="chat-attachment">
                    <a class="chat-attachment-image" href="${escapeHtml(attachment.url)}" target="_blank" rel="noopener">
                        <img src="${escapeHtml(attachment.url)}" alt="${escapeHtml(attachment.name ?? 'attachment')}">
                    </a>
                    ${renderAttachmentActions(attachment)}
                </div>
            `;
        }

        return `
            <div class="chat-attachment">
                <div class="chat-attachment-file">
                    <span class="chat-attachment-file-mark">${escapeHtml(attachment.type_label || fileLabel)}</span>
                    <span class="chat-attachment-file-meta">
                        <strong>${escapeHtml(attachment.name ?? 'attachment')}</strong>
                        ${attachment.size_label ? `<small>${escapeHtml(attachment.size_label)}</small>` : ''}
                    </span>
                </div>
                <div class="chat-attachment-action-links">
                    <a class="chat-attachment-action-link" href="${escapeHtml(attachment.url)}" target="_blank" rel="noopener">${escapeHtml(openLabel)}</a>
                    <a class="chat-attachment-action-link" href="${escapeHtml(attachment.download_url || attachment.url)}">${escapeHtml(downloadLabel)}</a>
                </div>
            </div>
        `;
    };

    const renderAttachments = (attachments) => {
        if (!Array.isArray(attachments) || attachments.length === 0) {
            return '';
        }

        return `<div class="chat-attachments-grid">${attachments.map((attachment) => renderAttachmentCard(attachment)).join('')}</div>`;
    };

    const renderEditAction = (item) => {
        if (!item?.can_edit || !item?.edit_url) {
            return '';
        }

        return `
            <button
                class="chat-message-action chat-message-action--edit"
                type="button"
                title="${escapeHtml(editMessageLabel)}"
                aria-label="${escapeHtml(editMessageLabel)}"
                data-chat-edit
                data-message-id="${escapeHtml(item.id ?? '')}"
                data-edit-url="${escapeHtml(item.edit_url)}"
                data-message-content="${escapeHtml(item.message ?? '')}"
                data-default-label="${escapeHtml(editLabel)}"
                data-loading-label="${escapeHtml(editingLabel)}"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 20h4l10.4-10.4a2 2 0 0 0 0-2.83l-1.17-1.17a2 2 0 0 0-2.83 0L4 16v4Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="m13.5 6.5 4 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                </svg>
                <span class="sr-only">${escapeHtml(editLabel)}</span>
            </button>
        `;
    };

    const renderDeleteAction = (item) => {
        if (!item?.can_delete || !item?.delete_url) {
            return '';
        }

        return `
            <button
                class="chat-message-action chat-message-action--delete"
                type="button"
                title="${escapeHtml(deleteLabel)}"
                aria-label="${escapeHtml(deleteLabel)}"
                data-chat-delete
                data-delete-url="${escapeHtml(item.delete_url)}"
                data-default-label="${escapeHtml(deleteLabel)}"
                data-loading-label="${escapeHtml(deletingLabel)}"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="M9 7V4.8A.8.8 0 0 1 9.8 4h4.4a.8.8 0 0 1 .8.8V7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="M7.5 7 8.3 19a1 1 0 0 0 1 .93h5.4a1 1 0 0 0 1-.93L16.5 7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="M10 11.2v4.8M14 11.2v4.8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                </svg>
                <span class="sr-only">${escapeHtml(deleteLabel)}</span>
            </button>
        `;
    };

    const renderReplyAction = (item) => {
        if (!item?.id) {
            return '';
        }

        return `
            <button
                class="chat-message-action chat-message-action--reply"
                type="button"
                title="${escapeHtml(replyLabel)}"
                aria-label="${escapeHtml(replyLabel)}"
                data-chat-reply
                data-message-id="${escapeHtml(item.id)}"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 8 4 12l5 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                    <path d="M20 18c0-4.42-3.58-8-8-8H4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                </svg>
                <span class="sr-only">${escapeHtml(replyLabel)}</span>
            </button>
        `;
    };

    const renderEditedState = (item) => {
        if (!item?.was_edited) {
            return '';
        }

        return `<span class="chat-edited-label">${escapeHtml(item.edited_label || editedLabel)}</span>`;
    };

    const renderMessageBody = (item) => {
        const messageId = String(item.id ?? '');

        if (editingMessageId !== messageId) {
            return item.message ? `<p class="chat-body">${escapeHtml(item.message)}</p>` : '';
        }

        return `
            <div class="chat-edit-shell">
                <label class="sr-only" for="chat-edit-${escapeHtml(messageId)}">${escapeHtml(editMessageLabel)}</label>
                <textarea
                    class="textarea-rgc chat-edit-input"
                    id="chat-edit-${escapeHtml(messageId)}"
                    rows="3"
                    data-chat-edit-input
                    data-message-id="${escapeHtml(messageId)}"
                >${escapeHtml(editingMessageDraft)}</textarea>
                <div class="chat-edit-actions">
                    <button
                        class="btn-rgc"
                        type="button"
                        data-chat-edit-save
                        data-message-id="${escapeHtml(messageId)}"
                        data-edit-url="${escapeHtml(item.edit_url ?? '')}"
                        data-default-label="${escapeHtml(saveChangesLabel)}"
                        data-loading-label="${escapeHtml(editingLabel)}"
                    >${escapeHtml(saveChangesLabel)}</button>
                    <button class="chat-edit-cancel" type="button" data-chat-edit-cancel>${escapeHtml(cancelLabel)}</button>
                </div>
            </div>
        `;
    };

    const renderMessage = (item, options = {}) => {
        const isMine = Boolean(item.is_mine) || Number(item.user?.id ?? 0) === currentUserId;
        const author = isMine ? youLabel : (item.user?.name ?? emptyTitle);
        const avatar = initials(item.user?.name ?? emptyTitle);
        const newClass = options.isNew ? ' is-new' : '';
        const body = renderMessageBody(item);
        const attachments = renderAttachments(normalizeAttachments(item));
        const parent = renderParentPreview(item.parent ?? null);

        return `
            <li class="chat-row ${isMine ? 'is-mine' : 'is-other'}" data-message-id="${escapeHtml(item.id ?? '')}">
                ${isMine ? '' : `<span class="chat-avatar">${escapeHtml(avatar)}</span>`}
                <div class="chat-bubble ${isMine ? 'is-mine' : 'is-other'}${newClass}">
                    <div class="chat-meta">
                        <span class="chat-author">${escapeHtml(author)}</span>
                        <div class="chat-meta-actions">
                            <div class="chat-meta-status">
                                <span class="chat-time" title="${escapeHtml(item.sent_at ?? '')}">${escapeHtml(item.sent_label ?? item.sent_at ?? '')}</span>
                                ${renderEditedState(item)}
                            </div>
                            <div class="chat-meta-controls">
                                ${renderReplyAction(item)}
                                ${renderEditAction(item)}
                                ${renderDeleteAction(item)}
                            </div>
                        </div>
                    </div>
                    ${parent}
                    ${attachments}
                    ${body}
                </div>
            </li>
        `;
    };

    const renderEmptyState = () => `
        <li class="chat-empty" data-chat-empty>
            <span class="chat-empty-mark">RGC</span>
            <div class="chat-empty-copy">
                <strong>${escapeHtml(emptyTitle)}</strong>
                <p>${escapeHtml(emptyBody)}</p>
            </div>
        </li>
    `;

    const renderGroupedMessages = (messages, options = {}) => {
        let previousDayKey = null;
        const newIds = new Set((options.newIds ?? []).map((value) => String(value)));

        return messages.map((item) => {
            const dayKey = item.sent_day_key ?? 'unknown-day';
            const parts = [];

            if (dayKey !== previousDayKey) {
                parts.push(`
                    <li class="chat-day-divider" data-chat-day="${escapeHtml(dayKey)}">
                        <span>${escapeHtml(formatDayLabel(item))}</span>
                    </li>
                `);
                previousDayKey = dayKey;
            }

            parts.push(renderMessage(item, { isNew: newIds.has(String(item.id ?? '')) }));
            return parts.join('');
        }).join('');
    };

    const focusEditInput = () => {
        if (!editingMessageId) {
            return;
        }

        const input = list.querySelector(`[data-chat-edit-input][data-message-id="${editingMessageId}"]`);

        if (!input) {
            return;
        }

        input.focus();
        const length = input.value.length;
        input.setSelectionRange(length, length);
    };

    const applyPendingMessages = () => {
        if (!pendingMessages || editingMessageId) {
            return;
        }

        const nextMessages = pendingMessages;
        pendingMessages = null;
        hydrateMessages(nextMessages, { force: true });
    };

    const hydrateMessages = (messages, options = {}) => {
        if (editingMessageId && !options.force) {
            pendingMessages = messages;
            return;
        }

        const shouldStick = isNearBottom() || !list.dataset.hydrated;
        const previousIds = list.dataset.hydrated ? currentMessageIds() : [];
        const incomingIds = messages.map((item) => String(item.id ?? ''));
        const newIds = list.dataset.hydrated
            ? incomingIds.filter((id) => id && !previousIds.includes(id))
            : [];
        const newItems = newIds.length
            ? messages.filter((item) => newIds.includes(String(item.id ?? '')))
            : [];

        cachedMessages = messages;
        list.innerHTML = messages.length ? renderGroupedMessages(messages, { newIds }) : renderEmptyState();
        list.dataset.hydrated = 'true';

        if (shouldStick) {
            requestAnimationFrame(() => {
                scrollToBottom();
                resetUnreadState();
                focusEditInput();
            });
            return;
        }

        setUnreadState(newIds.length);

        if (newItems.some((item) => !Boolean(item.is_mine))) {
            playNotificationSound();
        }
    };

    const rerenderCachedMessages = () => {
        if (!cachedMessages.length) {
            list.innerHTML = renderEmptyState();
            return;
        }

        list.innerHTML = renderGroupedMessages(cachedMessages);
        requestAnimationFrame(focusEditInput);
    };

    const renderReplyPreview = () => {
        if (!replyShell || !replyPreview || !replyInput) {
            return;
        }

        if (!replyingTo) {
            replyShell.classList.add('hidden');
            replyPreview.innerHTML = '';
            replyInput.value = '';
            return;
        }

        replyInput.value = String(replyingTo.id ?? '');
        replyPreview.innerHTML = renderParentPreview({
            author: replyingTo.user?.id === currentUserId ? youLabel : (replyingTo.user?.name ?? youLabel),
            excerpt: replyExcerpt(replyingTo),
            has_attachments: normalizeAttachments(replyingTo).length > 0,
            attachment_count: normalizeAttachments(replyingTo).length,
            is_mine: replyingTo.user?.id === currentUserId || replyingTo.is_mine,
        });
        replyShell.classList.remove('hidden');
    };

    const clearReplyTarget = () => {
        replyingTo = null;
        renderReplyPreview();
    };

    const setReplyTarget = (item) => {
        if (!item?.id) {
            showFeedback(replyBranchOnlyLabel, 'error');
            return;
        }

        replyingTo = item;
        renderReplyPreview();
        textarea.focus();
    };

    const selectedFileKey = (file) => `${file.name}:${file.size}:${file.lastModified}`;

    const syncSelectedFiles = () => {
        const transfer = new DataTransfer();

        selectedAttachments.forEach((file) => {
            transfer.items.add(file);
        });

        fileInput.files = transfer.files;
    };

    const renderSelectedFiles = () => {
        if (!selectedFilesWrap) {
            return;
        }

        if (selectedAttachments.length === 0) {
            selectedFilesWrap.innerHTML = '';
            selectedFilesWrap.classList.add('hidden');
            return;
        }

        selectedFilesWrap.innerHTML = selectedAttachments.map((file, index) => `
            <span class="chat-selected-chip">
                <span>${escapeHtml(file.name)}</span>
                <button type="button" data-chat-remove-file data-file-index="${index}" aria-label="${escapeHtml(cancelLabel)}">&times;</button>
            </span>
        `).join('');
        selectedFilesWrap.classList.remove('hidden');
    };

    const mergeAttachments = (incomingFiles) => {
        const merged = [...selectedAttachments];
        const existing = new Set(merged.map((file) => selectedFileKey(file)));

        incomingFiles.forEach((file) => {
            const key = selectedFileKey(file);

            if (existing.has(key)) {
                return;
            }

            if (merged.length >= 5) {
                return;
            }

            existing.add(key);
            merged.push(file);
        });

        selectedAttachments = merged;
        syncSelectedFiles();
        renderSelectedFiles();

        if (incomingFiles.length && merged.length >= 5 && incomingFiles.length + existing.size > 5) {
            showFeedback(maxAttachmentsReachedLabel, 'info');
        }
    };

    const setDropzoneState = (isActive) => {
        if (!dropzone) {
            return;
        }

        dropzone.classList.toggle('is-active', isActive);
        const hint = dropzone.querySelector('p');

        if (hint) {
            hint.textContent = isActive ? dragOverLabel : droppedFilesLabel;
        }
    };

    const fetchLatestMessages = async (options = {}) => {
        try {
            const response = await fetch(feedUrl, {
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            hydrateMessages(Array.isArray(data) ? data : [], options);
        } catch (error) {
            // Keep current conversation visible if refresh fails temporarily.
        }
    };

    const startFallbackRefresh = () => {
        if (fallbackInterval) {
            return;
        }

        fallbackInterval = window.setInterval(() => {
            if (document.visibilityState !== 'hidden') {
                fetchLatestMessages();
            }
        }, 3500);
    };

    const stopFallbackRefresh = () => {
        if (!fallbackInterval) {
            return;
        }

        window.clearInterval(fallbackInterval);
        fallbackInterval = null;
    };

    const scheduleRealtimeReconnect = () => {
        if (reconnectTimeout) {
            return;
        }

        reconnectTimeout = window.setTimeout(() => {
            reconnectTimeout = null;
            connectRealtimeStream();
        }, 2500);
    };

    const connectRealtimeStream = () => {
        if (!window.EventSource) {
            setStatusTone('warning');
            showFeedback(streamLostLabel, 'info');
            startFallbackRefresh();
            return;
        }

        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }

        eventSource = new EventSource(streamUrl);

        eventSource.onopen = () => {
            clearFeedback();
            setStatusTone('live');
            stopFallbackRefresh();
        };

        eventSource.addEventListener('snapshot', (event) => {
            clearFeedback();
            setStatusTone('live');
            stopFallbackRefresh();

            try {
                const data = JSON.parse(event.data ?? '[]');
                hydrateMessages(Array.isArray(data) ? data : []);
            } catch (error) {
                // Ignore malformed snapshot payloads and keep the current thread visible.
            }
        });

        eventSource.onerror = () => {
            if (eventSource) {
                eventSource.close();
                eventSource = null;
            }

            setStatusTone('warning');
            showFeedback(streamLostLabel, 'info');
            fetchLatestMessages();
            startFallbackRefresh();
            scheduleRealtimeReconnect();
        };
    };

    if (textarea) {
        const autosize = () => {
            textarea.style.height = 'auto';
            textarea.style.height = `${Math.min(textarea.scrollHeight, 180)}px`;
        };

        autosize();
        textarea.addEventListener('input', autosize);
        textarea.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();

                if (!textarea.value.trim() && selectedAttachments.length === 0) {
                    textarea.placeholder = emptyMessage;
                    return;
                }

                form.requestSubmit();
            }
        });
    }

    fileInput.addEventListener('change', () => {
        mergeAttachments(Array.from(fileInput.files ?? []));
    });

    dropzone?.addEventListener('click', () => fileInput.click());
    dropzone?.addEventListener('dragenter', (event) => {
        event.preventDefault();
        setDropzoneState(true);
    });
    dropzone?.addEventListener('dragover', (event) => {
        event.preventDefault();
        setDropzoneState(true);
    });
    dropzone?.addEventListener('dragleave', (event) => {
        if (!dropzone.contains(event.relatedTarget)) {
            setDropzoneState(false);
        }
    });
    dropzone?.addEventListener('drop', (event) => {
        event.preventDefault();
        setDropzoneState(false);
        mergeAttachments(Array.from(event.dataTransfer?.files ?? []));
    });

    selectedFilesWrap?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-chat-remove-file]');

        if (!button) {
            return;
        }

        const index = Number(button.dataset.fileIndex ?? -1);

        if (index < 0) {
            return;
        }

        selectedAttachments = selectedAttachments.filter((_, itemIndex) => itemIndex !== index);
        syncSelectedFiles();
        renderSelectedFiles();
    });

    replyCancelButton?.addEventListener('click', () => {
        clearReplyTarget();
    });

    requestAnimationFrame(() => {
        scrollToBottom();
        resetUnreadState();
    });

    ['click', 'keydown', 'touchstart'].forEach((eventName) => {
        window.addEventListener(eventName, unlockAudio, { once: true, passive: true });
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            fetchLatestMessages();
        }
    });

    thread.addEventListener('scroll', () => {
        if (isNearBottom()) {
            resetUnreadState();
        }
    });

    jumpButton?.addEventListener('click', () => {
        scrollToBottom();
        resetUnreadState();
    });

    list.addEventListener('input', (event) => {
        const input = event.target.closest('[data-chat-edit-input]');

        if (!input) {
            return;
        }

        editingMessageDraft = input.value;
    });

    list.addEventListener('click', async (event) => {
        const replyButton = event.target.closest('[data-chat-reply]');
        const editButton = event.target.closest('[data-chat-edit]');
        const cancelButton = event.target.closest('[data-chat-edit-cancel]');
        const saveButton = event.target.closest('[data-chat-edit-save]');
        const deleteButton = event.target.closest('[data-chat-delete]');

        if (replyButton) {
            event.preventDefault();
            const item = findMessageById(replyButton.dataset.messageId);
            setReplyTarget(item);
            return;
        }

        if (editButton) {
            event.preventDefault();
            editingMessageId = String(editButton.dataset.messageId || '');
            editingMessageDraft = editButton.dataset.messageContent || '';
            rerenderCachedMessages();
            return;
        }

        if (cancelButton) {
            event.preventDefault();
            editingMessageId = null;
            editingMessageDraft = '';
            rerenderCachedMessages();
            applyPendingMessages();
            return;
        }

        if (saveButton) {
            event.preventDefault();

            const editUrl = saveButton.dataset.editUrl;
            const message = editingMessageDraft.trim();

            if (!editUrl || !message) {
                return;
            }

            saveButton.disabled = true;
            saveButton.textContent = saveButton.dataset.loadingLabel || editingLabel;

            try {
                const response = await fetch(editUrl, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message }),
                });

                if (!response.ok) {
                    throw new Error('Edit failed');
                }

                editingMessageId = null;
                editingMessageDraft = '';
                clearFeedback();
                await fetchLatestMessages({ force: true });
                applyPendingMessages();
                return;
            } catch (error) {
                saveButton.disabled = false;
                saveButton.textContent = saveButton.dataset.defaultLabel || saveChangesLabel;
                showFeedback(@json(__('Message updated.')) === '' ? 'Unable to update message.' : @json(__('Unable to update this message right now.')), 'error');
                return;
            }
        }

        if (!deleteButton) {
            return;
        }

        event.preventDefault();

        if (!confirm(confirmDeleteLabel)) {
            return;
        }

        const deleteUrl = deleteButton.dataset.deleteUrl;

        if (!deleteUrl) {
            return;
        }

        deleteButton.disabled = true;
        deleteButton.textContent = deleteButton.dataset.loadingLabel || deletingLabel;

        try {
            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Delete failed');
            }

            if (editingMessageId && editingMessageId === String(deleteButton.closest('[data-message-id]')?.dataset.messageId || '')) {
                editingMessageId = null;
                editingMessageDraft = '';
            }

            clearFeedback();
            await fetchLatestMessages({ force: true });
        } catch (error) {
            deleteButton.disabled = false;
            deleteButton.textContent = deleteButton.dataset.defaultLabel || deleteLabel;
            showFeedback(@json(__('Unable to delete this message right now.')), 'error');
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!submitButton) {
            return;
        }

        const message = textarea.value.trim();

        if (!message && selectedAttachments.length === 0) {
            showFeedback(@json(__('Write a message or attach a file.')), 'error');
            return;
        }

        clearFeedback();
        submitButton.disabled = true;
        submitButton.textContent = sendingLabel;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (response.status === 422) {
                const payload = await response.json();
                const firstError = Object.values(payload.errors ?? {}).flat()[0];
                showFeedback(firstError || emptyMessage, 'error');
                submitButton.disabled = false;
                submitButton.textContent = @json(__('Send'));
                return;
            }

            if (!response.ok) {
                throw new Error('Store failed');
            }

            textarea.value = '';
            textarea.style.height = 'auto';
            selectedAttachments = [];
            syncSelectedFiles();
            renderSelectedFiles();
            clearReplyTarget();
            clearFeedback();
            submitButton.disabled = false;
            submitButton.textContent = @json(__('Send'));
            await fetchLatestMessages({ force: true });
            scrollToBottom();
        } catch (error) {
            submitButton.disabled = false;
            submitButton.textContent = @json(__('Send'));
            showFeedback(@json(__('Unable to send your message right now.')), 'error');
        }
    });

    window.addEventListener('beforeunload', () => {
        if (eventSource) {
            eventSource.close();
        }

        if (fallbackInterval) {
            window.clearInterval(fallbackInterval);
        }
    });

    renderSelectedFiles();
    renderReplyPreview();
    if (prefillMessage && !textarea.value.trim()) {
        textarea.value = prefillMessage;
        textarea.style.height = 'auto';
        textarea.style.height = `${textarea.scrollHeight}px`;
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    }
    fetchLatestMessages({ force: true });
    connectRealtimeStream();
})();
</script>
@endsection
