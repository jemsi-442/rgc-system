@extends('layouts.app')

@section('title', $otherUser->name . ' - Mazungumzo')
@section('page-title', 'Mazungumzo')
@section('page-subtitle', $otherUser->name)

@section('styles')
<style>
    /* WhatsApp Modern Design */
    :root {
        --whatsapp-primary: #360958;
        --whatsapp-primary-dark: #2a0745;
        --whatsapp-primary-light: rgba(54, 9, 88, 0.1);
        --whatsapp-secondary: #8a2be2;
        --whatsapp-secondary-light: rgba(138, 43, 226, 0.1);
        --whatsapp-bg: #f0f2f5;
        --whatsapp-header-bg: #f0f2f5;
        --whatsapp-chat-bg: #ffffff;
        --whatsapp-input-bg: #ffffff;
        --whatsapp-green: #25d366;
        --whatsapp-blue: #53bdeb;
        --whatsapp-gray: #667781;
        --whatsapp-dark: #111b21;
        --whatsapp-light-gray: #8696a0;
        --whatsapp-border: #e9edef;
        --whatsapp-message-sent: linear-gradient(135deg, #d9fdd3 0%, #c5f7bd 100%);
        --whatsapp-message-received: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        --whatsapp-shadow: rgba(11, 20, 26, 0.13);
        --whatsapp-gradient: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-secondary) 50%, var(--whatsapp-primary-dark) 100%);
    }

    /* WhatsApp Container - Modern Layout */
    .whatsapp-modern {
        display: flex;
        height: calc(100vh - 140px);
        min-height: 600px;
        background: var(--whatsapp-bg);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    /* Left Sidebar - Modern Chats */
    .whatsapp-modern-sidebar {
        width: 30%;
        min-width: 320px;
        background: var(--whatsapp-chat-bg);
        border-right: 1px solid var(--whatsapp-border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Modern Sidebar Header */
    .modern-sidebar-header {
        padding: 16px 20px;
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-primary-dark) 100%);
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
    }

    .modern-user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .modern-user-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .modern-user-avatar i {
        color: var(--whatsapp-primary);
        font-size: 20px;
    }

    .online-status {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background-color: var(--whatsapp-green);
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .modern-header-actions {
        display: flex;
        gap: 16px;
        margin-left: auto;
    }

    .modern-header-btn {
        color: white;
        cursor: pointer;
        font-size: 20px;
        transition: all 0.3s;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .modern-header-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* Modern Search Bar */
    .modern-search-bar {
        padding: 16px 20px;
        background: white;
        border-bottom: 1px solid var(--whatsapp-border);
        flex-shrink: 0;
    }

    .modern-search-container {
        position: relative;
        height: 40px;
    }

    .modern-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--whatsapp-light-gray);
        font-size: 16px;
        z-index: 2;
    }

    .modern-search-input {
        width: 100%;
        height: 40px;
        padding: 0 16px 0 48px;
        border: 2px solid var(--whatsapp-border);
        border-radius: 24px;
        background: var(--whatsapp-bg);
        color: var(--whatsapp-dark);
        font-size: 15px;
        outline: none;
        transition: all 0.3s;
    }

    .modern-search-input:focus {
        border-color: var(--whatsapp-primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(54, 9, 88, 0.1);
    }

    .modern-search-input::placeholder {
        color: var(--whatsapp-light-gray);
        font-weight: 400;
    }

    /* Modern Chats List */
    .modern-chats-list {
        flex: 1;
        overflow-y: auto;
        background: var(--whatsapp-chat-bg);
    }

    .modern-chats-list::-webkit-scrollbar {
        width: 6px;
    }

    .modern-chats-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .modern-chats-list::-webkit-scrollbar-thumb {
        background: var(--whatsapp-primary);
        border-radius: 3px;
    }

    /* Modern Chat Item */
    .modern-chat-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--whatsapp-border);
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        text-decoration: none;
        color: inherit;
        background: white;
    }

    .modern-chat-item:hover {
        background: linear-gradient(90deg, rgba(54, 9, 88, 0.05) 0%, transparent 100%);
    }

    .modern-chat-item.active {
        background: linear-gradient(90deg, rgba(54, 9, 88, 0.1) 0%, transparent 100%);
        border-left: 4px solid var(--whatsapp-primary);
    }

    .modern-chat-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
        position: relative;
        box-shadow: 0 4px 12px rgba(54, 9, 88, 0.15);
    }

    .modern-chat-avatar i {
        color: white;
        font-size: 22px;
    }

    .modern-chat-info {
        flex: 1;
        min-width: 0;
    }

    .modern-chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .modern-chat-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--whatsapp-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modern-chat-time {
        font-size: 12px;
        color: var(--whatsapp-light-gray);
        font-weight: 500;
    }

    .modern-chat-preview {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .modern-chat-message {
        font-size: 14px;
        color: var(--whatsapp-gray);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }

    .modern-chat-unread {
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-secondary) 100%);
        color: white;
        border-radius: 50%;
        min-width: 22px;
        height: 22px;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        margin-left: auto;
        box-shadow: 0 2px 8px rgba(54, 9, 88, 0.3);
    }

    /* Quick Contacts Section */
    .quick-contacts-section {
        padding: 20px;
        border-top: 1px solid var(--whatsapp-border);
        background: white;
    }

    .quick-contacts-title {
        font-size: 14px;
        color: var(--whatsapp-primary);
        margin-bottom: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-contacts-title i {
        font-size: 16px;
    }

    .quick-contacts-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .quick-contact-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
        background: white;
        border: 1px solid var(--whatsapp-border);
    }

    .quick-contact-item:hover {
        background: linear-gradient(90deg, rgba(54, 9, 88, 0.05) 0%, transparent 100%);
        border-color: var(--whatsapp-primary);
        transform: translateX(4px);
    }

    .quick-contact-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .quick-contact-avatar i {
        color: white;
        font-size: 18px;
    }

    .quick-contact-info {
        flex: 1;
        min-width: 0;
    }

    .quick-contact-name {
        font-size: 15px;
        font-weight: 600;
        color: var(--whatsapp-dark);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .quick-contact-role {
        font-size: 12px;
        color: var(--whatsapp-primary);
        font-weight: 500;
        padding: 2px 8px;
        background: var(--whatsapp-primary-light);
        border-radius: 10px;
        display: inline-block;
    }

    /* Right Side - Modern Chat Area */
    .modern-chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--whatsapp-bg);
        position: relative;
        overflow: hidden;
    }

    /* Modern Chat Header */
    .modern-chat-header-bar {
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-primary-dark) 100%);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
        box-shadow: 0 4px 20px rgba(54, 9, 88, 0.15);
        position: relative;
    }

    .modern-back-btn {
        color: white;
        cursor: pointer;
        font-size: 20px;
        transition: all 0.3s;
        padding: 8px;
        border-radius: 50%;
        display: none;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
    }

    .modern-back-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.2);
        transform: translateX(-2px);
    }

    .modern-chat-contact-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .modern-chat-contact-avatar i {
        color: var(--whatsapp-primary);
        font-size: 20px;
    }

    .modern-chat-contact-info {
        flex: 1;
        min-width: 0;
    }

    .modern-contact-name {
        font-size: 17px;
        font-weight: 600;
        color: white;
        margin-bottom: 2px;
    }

    .modern-contact-status {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .modern-chat-header-actions {
        display: flex;
        gap: 16px;
        margin-left: auto;
    }

    .modern-chat-action-btn {
        color: white;
        cursor: pointer;
        font-size: 20px;
        transition: all 0.3s;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
    }

    .modern-chat-action-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }

    /* Modern Messages Container */
    .modern-messages-container {
        flex: 1;
        padding: 24px 12% 16px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        background: var(--whatsapp-bg);
        background-image: url("data:image/svg+xml,%3Csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='smallGrid' width='40' height='40' patternUnits='userSpaceOnUse'%3E%3Cpath d='M 40 0 L 0 0 0 40' fill='none' stroke='rgba(54, 9, 88, 0.05)' stroke-width='0.5'/%3E%3C/pattern%3E%3Cpattern id='grid' width='80' height='80' patternUnits='userSpaceOnUse' patternTransform='rotate(45)'%3E%3Crect width='80' height='80' fill='url(%23smallGrid)'/%3E%3Cpath d='M 80 0 L 0 0 0 80' fill='none' stroke='rgba(54, 9, 88, 0.08)' stroke-width='1'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100%25' height='100%25' fill='url(%23grid)' opacity='0.3'/%3E%3C/svg%3E");
    }

    /* Modern Date Separator */
    .modern-date-separator {
        text-align: center;
        margin: 24px 0;
        position: relative;
    }

    .modern-date-separator span {
        background: rgba(54, 9, 88, 0.1);
        color: var(--whatsapp-primary);
        font-size: 13px;
        padding: 8px 20px;
        border-radius: 20px;
        display: inline-block;
        border: 1px solid rgba(54, 9, 88, 0.1);
        font-weight: 500;
        backdrop-filter: blur(10px);
    }

    /* Modern Message Bubbles - CLASSIC DESIGN */
    .modern-message-row {
        display: flex;
        margin-bottom: 16px;
        clear: both;
    }

    .modern-message-row.sent {
        justify-content: flex-end;
        margin-left: auto;
    }

    .modern-message-row.received {
        justify-content: flex-start;
        margin-right: auto;
    }

    /* Classic Message Bubble */
    .classic-message-bubble {
        max-width: 65%;
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        word-wrap: break-word;
        white-space: pre-wrap;
        transition: all 0.3s;
        border: 1px solid transparent;
    }

    /* Sent Message - Purple Gradient Border */
    .sent .classic-message-bubble {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 18px 4px 18px 18px;
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
    }

    .sent .classic-message-bubble::before {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        bottom: -2px;
        left: -2px;
        background: var(--whatsapp-gradient);
        border-radius: 18px 4px 18px 18px;
        z-index: -1;
    }

    /* Received Message - Light Gray Border */
    .received .classic-message-bubble {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 4px 18px 18px 18px;
        border: 2px solid #e9edef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* Message content */
    .classic-message-content {
        font-size: 15px;
        line-height: 1.5;
        color: var(--whatsapp-dark);
        margin-bottom: 6px;
    }

    /* Message time and status */
    .classic-message-time {
        text-align: right;
        font-size: 11px;
        color: var(--whatsapp-light-gray);
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .sent .classic-message-time {
        color: var(--whatsapp-primary);
    }

    .classic-message-status {
        font-size: 12px;
    }

    /* LUXURY INPUT AREA - MODERN CLASSIC DESIGN */
    .luxury-input-area {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 20px 24px;
        border-top: 1px solid rgba(54, 9, 88, 0.1);
        position: relative;
        z-index: 10;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
    }

    .luxury-input-area::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--whatsapp-primary), transparent);
    }

    .luxury-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        background: white;
        border-radius: 28px;
        padding: 4px 20px;
        border: 2px solid #e9edef;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(54, 9, 88, 0.08);
        position: relative;
        overflow: hidden;
    }

    .luxury-input-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(54, 9, 88, 0.02) 0%, rgba(138, 43, 226, 0.02) 100%);
        border-radius: 28px;
    }

    .luxury-input-wrapper:focus-within {
        border-color: var(--whatsapp-primary);
        box-shadow: 0 6px 30px rgba(54, 9, 88, 0.15);
        transform: translateY(-2px);
    }

    .luxury-input-wrapper:hover:not(:focus-within) {
        border-color: rgba(54, 9, 88, 0.3);
        box-shadow: 0 4px 25px rgba(54, 9, 88, 0.12);
    }

    /* Input Action Buttons */
    .luxury-input-action-btn {
        color: var(--whatsapp-light-gray);
        cursor: pointer;
        font-size: 22px;
        padding: 10px;
        transition: all 0.3s;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        position: relative;
        z-index: 1;
        background: rgba(54, 9, 88, 0.02);
    }

    .luxury-input-action-btn:hover {
        color: var(--whatsapp-primary);
        background: rgba(54, 9, 88, 0.08);
        transform: scale(1.1);
    }

    /* Textarea Input */
    .luxury-message-textarea {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        color: var(--whatsapp-dark);
        font-size: 15.5px;
        line-height: 1.5;
        resize: none;
        max-height: 120px;
        min-height: 24px;
        padding: 12px 0;
        font-family: inherit;
        position: relative;
        z-index: 1;
    }

    .luxury-message-textarea::placeholder {
        color: var(--whatsapp-light-gray);
        font-weight: 400;
    }

    /* Send Button - Luxury Design */
    .luxury-send-btn {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-secondary) 50%, var(--whatsapp-primary-dark) 100%);
        border: none;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.4s;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 15px rgba(54, 9, 88, 0.3);
        overflow: hidden;
    }

    .luxury-send-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        border-radius: 50%;
    }

    .luxury-send-btn:hover {
        transform: scale(1.1) rotate(15deg);
        box-shadow: 0 6px 25px rgba(54, 9, 88, 0.4);
    }

    .luxury-send-btn:active {
        transform: scale(0.95);
    }

    .luxury-send-btn:disabled {
        background: var(--whatsapp-light-gray);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .luxury-send-btn i {
        transition: transform 0.3s;
        position: relative;
        z-index: 1;
    }

    .luxury-send-btn:hover i {
        transform: translateX(2px);
    }

    /* Input Icons Container */
    .input-icons-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Empty Chat State */
    .modern-empty-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        text-align: center;
        background: white;
    }

    .modern-empty-icon {
        width: 160px;
        height: 160px;
        background: linear-gradient(135deg, var(--whatsapp-primary) 0%, var(--whatsapp-secondary) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 32px;
        box-shadow: 0 8px 32px rgba(54, 9, 88, 0.2);
    }

    .modern-empty-icon i {
        color: white;
        font-size: 64px;
    }

    .modern-empty-title {
        font-size: 28px;
        font-weight: 300;
        color: var(--whatsapp-dark);
        margin-bottom: 16px;
    }

    .modern-empty-subtitle {
        font-size: 15px;
        color: var(--whatsapp-gray);
        max-width: 400px;
        line-height: 1.5;
        margin-bottom: 32px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .whatsapp-modern {
            height: 100vh;
            border-radius: 0;
        }
        
        .modern-chat-area.hidden {
            display: none;
        }
        
        .modern-back-btn {
            display: flex;
        }
        
        .luxury-input-area {
            padding: 16px;
        }
        
        .luxury-input-wrapper {
            padding: 4px 16px;
            border-radius: 24px;
        }
        
        .luxury-message-textarea {
            font-size: 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="whatsapp-modern">
    <!-- Left Sidebar - Modern Chats -->
    <div class="whatsapp-modern-sidebar {{ $messages->isEmpty() ? '' : 'hidden' }}" id="modernSidebar">
        <!-- Sidebar Header -->
        <div class="modern-sidebar-header">
            <div class="modern-user-avatar" title="Wasifu wangu">
                <i class="fas fa-user"></i>
                <div class="online-status"></div>
            </div>
            
            <div class="modern-header-actions">
                <div class="modern-header-btn" title="Mazungumzo Mapya">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="modern-header-btn" title="Mipangilio">
                    <i class="fas fa-cog"></i>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="modern-search-bar">
            <div class="modern-search-container">
                <i class="fas fa-search modern-search-icon"></i>
                <input type="text" class="modern-search-input" placeholder="Tafuta mazungumzo...">
            </div>
        </div>

        <!-- Chats List -->
        <div class="modern-chats-list" id="modernChatsList">
            @foreach($conversations as $conv)
                <a href="{{ route('messages.conversation', $conv['user']->id) }}" 
                   class="modern-chat-item {{ $conv['user']->id == $otherUser->id ? 'active' : '' }}"
                   data-chat-id="{{ $conv['user']->id }}">
                    
                    <div class="modern-chat-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <div class="modern-chat-info">
                        <div class="modern-chat-header">
                            <h3 class="modern-chat-name">{{ $conv['user']->name }}</h3>
                            <span class="modern-chat-time">
                                {{ $conv['last_message']->created_at->format('H:i') }}
                            </span>
                        </div>
                        
                        <div class="modern-chat-preview">
                            <span class="modern-chat-message">
                                @if($conv['last_message']->sender_id == Auth::id())
                                    <span style="color: var(--whatsapp-gray);">Wewe: </span>
                                @endif
                                {{ Str::limit($conv['last_message']->content, 30) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($conv['unread_count'] > 0 && $conv['user']->id != $otherUser->id)
                        <div class="modern-chat-unread">
                            {{ $conv['unread_count'] }}
                        </div>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Quick Contacts -->
        @if(Auth::user()->isMwanachama() && $leaders->isNotEmpty())
            <div class="quick-contacts-section">
                <div class="quick-contacts-title">
                    <i class="fas fa-users"></i>
                    Viongozi wa Kanisa
                </div>
                <div class="quick-contacts-list">
                    @foreach($leaders as $leader)
                        <a href="{{ route('messages.conversation', $leader->id) }}" class="quick-contact-item">
                            <div class="quick-contact-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="quick-contact-info">
                                <div class="quick-contact-name">{{ $leader->name }}</div>
                                <div class="quick-contact-role">{{ $leader->role->name ?? 'Kiongozi' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Right Side - Modern Chat Area -->
    <div class="modern-chat-area {{ $messages->isEmpty() ? 'hidden' : '' }}" id="modernChatArea">
        <!-- Empty Chat State -->
        <div class="modern-empty-chat" id="emptyChatState">
            <div class="modern-empty-icon">
                <i class="fas fa-comments"></i>
            </div>
            
            <h2 class="modern-empty-title">Mazungumzo ya WhatsApp</h2>
            
            <p class="modern-empty-subtitle">
                Chagua mazungumzo kutoka kwenye orodha upande wa kushoto 
                au anza mazungumzo mapya na miongoni mwa viongozi wa kanisa.
            </p>
        </div>

        <!-- Chat Header -->
        <div class="modern-chat-header-bar" id="modernChatHeader" style="{{ $messages->isEmpty() ? 'display: none;' : '' }}">
            <div class="modern-back-btn" id="modernBackBtn">
                <i class="fas fa-arrow-left"></i>
            </div>
            
            <div class="modern-chat-contact-avatar" id="modernContactAvatar">
                <i class="fas fa-user"></i>
            </div>
            
            <div class="modern-chat-contact-info">
                <div class="modern-contact-name" id="modernContactName">{{ $otherUser->name }}</div>
                <div class="modern-contact-status" id="modernContactStatus">
                    <i class="fas fa-circle" style="font-size: 8px;"></i>
                    {{ $otherUser->role->name ?? 'Mwanachama' }}
                </div>
            </div>
            
            <div class="modern-chat-header-actions">
                <div class="modern-chat-action-btn">
                    <i class="fas fa-search"></i>
                </div>
                <div class="modern-chat-action-btn">
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="modern-messages-container" id="modernMessagesContainer" style="{{ $messages->isEmpty() ? 'display: none;' : '' }}">
            @php
                $currentDate = null;
            @endphp
            
            @forelse($messages as $message)
                @php
                    $messageDate = $message->created_at->format('Y-m-d');
                    $today = \Carbon\Carbon::today()->format('Y-m-d');
                    $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');
                    
                    if ($messageDate === $today) {
                        $dateLabel = 'Leo';
                    } elseif ($messageDate === $yesterday) {
                        $dateLabel = 'Jana';
                    } else {
                        $dateLabel = $message->created_at->translatedFormat('l, F j, Y');
                    }
                @endphp
                
                @if($currentDate !== $messageDate)
                    <div class="modern-date-separator">
                        <span>{{ $dateLabel }}</span>
                    </div>
                    @php $currentDate = $messageDate; @endphp
                @endif
                
                <div class="modern-message-row {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                    <div class="classic-message-bubble">
                        <div class="classic-message-content">{{ $message->content }}</div>
                        <div class="classic-message-time">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->sender_id == Auth::id())
                                <i class="fas {{ $message->is_read ? 'fa-check-double' : 'fa-check' }} classic-message-status"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- Messages will be loaded via JavaScript -->
            @endforelse
        </div>

        <!-- LUXURY INPUT AREA -->
        <div class="luxury-input-area" id="luxuryInputArea" style="{{ $messages->isEmpty() ? 'display: none;' : '' }}">
            <form id="luxuryMessageForm" action="{{ route('messages.send') }}" method="POST">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                
                <div class="luxury-input-wrapper">
                    <!-- Left Side Icons -->
                    <div class="input-icons-container">
                        <div class="luxury-input-action-btn" title="Emoji">
                            <i class="far fa-smile"></i>
                        </div>
                        <div class="luxury-input-action-btn" title="Picha">
                            <i class="fas fa-image"></i>
                        </div>
                        <div class="luxury-input-action-btn" title="Faili">
                            <i class="fas fa-paperclip"></i>
                        </div>
                    </div>
                    
                    <!-- Text Input -->
                    <textarea 
                        name="content" 
                        id="luxuryMessageContent" 
                        rows="1" 
                        required
                        class="luxury-message-textarea"
                        placeholder="Andika ujumbe hapa..."
                        autocomplete="off"
                        autofocus></textarea>
                    
                    <!-- Right Side Icons -->
                    <div class="input-icons-container">
                        <div class="luxury-input-action-btn" title="Rekodi Sauti">
                            <i class="fas fa-microphone"></i>
                        </div>
                        <button type="submit" id="luxurySendBtn" class="luxury-send-btn" title="Tuma ujumbe">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const modernSidebar = document.getElementById('modernSidebar');
    const modernChatArea = document.getElementById('modernChatArea');
    const emptyChatState = document.getElementById('emptyChatState');
    const modernChatHeader = document.getElementById('modernChatHeader');
    const modernMessagesContainer = document.getElementById('modernMessagesContainer');
    const luxuryInputArea = document.getElementById('luxuryInputArea');
    const modernBackBtn = document.getElementById('modernBackBtn');
    const luxuryMessageForm = document.getElementById('luxuryMessageForm');
    const luxuryMessageContent = document.getElementById('luxuryMessageContent');
    const luxurySendBtn = document.getElementById('luxurySendBtn');
    const modernChatsList = document.getElementById('modernChatsList');
    
    let lastMessageId = {{ $messages->last()?->id ?? 0 }};
    let isMobile = window.innerWidth <= 768;

    // Initialize UI
    function initializeUI() {
        const hasMessages = {{ $messages->isEmpty() ? 'false' : 'true' }};
        
        if (isMobile) {
            if (hasMessages) {
                modernSidebar.classList.add('hidden');
                modernChatArea.classList.remove('hidden');
                emptyChatState.style.display = 'none';
                modernChatHeader.style.display = 'flex';
                modernMessagesContainer.style.display = 'flex';
                luxuryInputArea.style.display = 'block';
            } else {
                modernSidebar.classList.remove('hidden');
                modernChatArea.classList.add('hidden');
            }
        } else {
            if (hasMessages) {
                modernSidebar.classList.remove('hidden');
                modernChatArea.classList.remove('hidden');
                emptyChatState.style.display = 'none';
                modernChatHeader.style.display = 'flex';
                modernMessagesContainer.style.display = 'flex';
                luxuryInputArea.style.display = 'block';
            }
        }
    }

    // Scroll to bottom
    function scrollToBottom() {
        if (modernMessagesContainer) {
            modernMessagesContainer.scrollTop = modernMessagesContainer.scrollHeight;
        }
    }

    // Auto-resize textarea with luxury effect
    if (luxuryMessageContent) {
        luxuryMessageContent.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            
            // Add active class to wrapper
            const wrapper = this.closest('.luxury-input-wrapper');
            if (wrapper) {
                if (this.value.trim()) {
                    wrapper.classList.add('active');
                } else {
                    wrapper.classList.remove('active');
                }
            }
        });

        // Add focus effect
        luxuryMessageContent.addEventListener('focus', function() {
            const wrapper = this.closest('.luxury-input-wrapper');
            if (wrapper) {
                wrapper.classList.add('focused');
            }
        });

        luxuryMessageContent.addEventListener('blur', function() {
            const wrapper = this.closest('.luxury-input-wrapper');
            if (wrapper) {
                wrapper.classList.remove('focused');
            }
        });

        // Submit on Enter (without Shift)
        luxuryMessageContent.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim()) {
                    luxuryMessageForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    }

    // Handle form submission
    if (luxuryMessageForm) {
        luxuryMessageForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const content = luxuryMessageContent.value.trim();
            if (!content) return;

            // Disable send button with animation
            luxurySendBtn.disabled = true;
            luxurySendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            luxurySendBtn.style.background = 'var(--whatsapp-gray)';

            // Create temporary message with animation
            const tempId = 'temp-' + Date.now();
            const messageHTML = `
                <div class="modern-message-row sent">
                    <div class="classic-message-bubble" id="${tempId}" style="opacity: 0; transform: translateY(20px);">
                        <div class="classic-message-content">${escapeHtml(content)}</div>
                        <div class="classic-message-time">
                            ${new Date().toLocaleTimeString('sw-TZ', {hour: '2-digit', minute: '2-digit'})}
                            <i class="fas fa-clock classic-message-status"></i>
                        </div>
                    </div>
                </div>
            `;
            
            // Check if we need a date separator
            const today = new Date().toISOString().split('T')[0];
            const lastDateSeparator = modernMessagesContainer.querySelector('.modern-date-separator:last-child');
            
            if (!lastDateSeparator || !modernMessagesContainer.querySelector(`.modern-date-separator span`).textContent.includes('Leo')) {
                const dateHTML = `
                    <div class="modern-date-separator">
                        <span>Leo</span>
                    </div>
                    ${messageHTML}
                `;
                modernMessagesContainer.insertAdjacentHTML('beforeend', dateHTML);
            } else {
                modernMessagesContainer.insertAdjacentHTML('beforeend', messageHTML);
            }
            
            // Animate the new message
            setTimeout(() => {
                const tempEl = document.getElementById(tempId);
                if (tempEl) {
                    tempEl.style.opacity = '1';
                    tempEl.style.transform = 'translateY(0)';
                    tempEl.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                }
            }, 10);
            
            scrollToBottom();
            
            // Clear textarea and reset
            luxuryMessageContent.value = '';
            luxuryMessageContent.style.height = 'auto';
            
            // Remove active class from wrapper
            const wrapper = luxuryMessageContent.closest('.luxury-input-wrapper');
            if (wrapper) {
                wrapper.classList.remove('active');
            }

            // Send AJAX request
            try {
                const response = await fetch(luxuryMessageForm.action, {
                    method: 'POST',
                    body: new FormData(luxuryMessageForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    const tempEl = document.getElementById(tempId);
                    if (tempEl && data.message) {
                        const statusIcon = tempEl.querySelector('.fa-clock');
                        if (statusIcon) {
                            statusIcon.className = data.message.is_read ? 
                                'fas fa-check-double classic-message-status' : 
                                'fas fa-check classic-message-status';
                        }
                        lastMessageId = data.message.id;
                        
                        // Add success animation
                        tempEl.style.borderColor = 'rgba(54, 9, 88, 0.3)';
                    }
                } else {
                    showError(tempId);
                }
            } catch (error) {
                console.error('Error:', error);
                showError(tempId);
            } finally {
                // Reset send button
                setTimeout(() => {
                    luxurySendBtn.disabled = false;
                    luxurySendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    luxurySendBtn.style.background = '';
                    luxuryMessageContent.focus();
                }, 300);
            }
        });
    }

    // Show error on failed message send
    function showError(tempId) {
        const tempEl = document.getElementById(tempId);
        if (tempEl) {
            const statusIcon = tempEl.querySelector('.classic-message-status');
            if (statusIcon) {
                statusIcon.className = 'fas fa-exclamation-circle classic-message-status';
                statusIcon.style.color = '#ff4757';
            }
            tempEl.style.borderColor = '#ff4757';
        }
    }

    // Poll for new messages
    setInterval(async function() {
        if (lastMessageId > 0 && modernMessagesContainer.style.display !== 'none') {
            try {
                const response = await fetch(`{{ route('messages.new', $otherUser->id) }}?last_id=${lastMessageId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                            const messageHTML = `
                                <div class="modern-message-row received" data-message-id="${message.id}" style="opacity: 0; transform: translateY(20px);">
                                    <div class="classic-message-bubble">
                                        <div class="classic-message-content">${escapeHtml(message.content)}</div>
                                        <div class="classic-message-time">
                                            ${formatTime(message.created_at)}
                                        </div>
                                    </div>
                                </div>
                            `;
                            modernMessagesContainer.insertAdjacentHTML('beforeend', messageHTML);
                            
                            // Animate new message
                            setTimeout(() => {
                                const newMessage = modernMessagesContainer.querySelector(`[data-message-id="${message.id}"]`);
                                if (newMessage) {
                                    newMessage.style.opacity = '1';
                                    newMessage.style.transform = 'translateY(0)';
                                    newMessage.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                                }
                            }, 10);
                        }
                    });
                    
                    lastMessageId = data.last_id;
                    
                    if (isNearBottom()) {
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }
    }, 3000);

    // Check if user is near bottom of messages
    function isNearBottom() {
        if (!modernMessagesContainer) return false;
        return modernMessagesContainer.scrollHeight - modernMessagesContainer.scrollTop - modernMessagesContainer.clientHeight < 100;
    }

    // Utility functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('sw-TZ', {hour: '2-digit', minute: '2-digit'});
    }

    // Mobile navigation
    if (modernChatsList) {
        modernChatsList.addEventListener('click', function(e) {
            const chatItem = e.target.closest('.modern-chat-item');
            if (chatItem && isMobile) {
                e.preventDefault();
                
                modernSidebar.classList.add('hidden');
                modernChatArea.classList.remove('hidden');
                emptyChatState.style.display = 'none';
                modernChatHeader.style.display = 'flex';
                modernMessagesContainer.style.display = 'flex';
                luxuryInputArea.style.display = 'block';
                
                const chatName = chatItem.querySelector('.modern-chat-name').textContent;
                document.getElementById('modernContactName').textContent = chatName;
                
                const chatId = chatItem.dataset.chatId;
                window.history.pushState({}, '', `/messages/conversation/${chatId}`);
            }
        });
    }

    // Mobile back button
    if (modernBackBtn) {
        modernBackBtn.addEventListener('click', function() {
            if (isMobile) {
                modernSidebar.classList.remove('hidden');
                modernChatArea.classList.add('hidden');
                modernChatHeader.style.display = 'none';
                modernMessagesContainer.style.display = 'none';
                luxuryInputArea.style.display = 'none';
                emptyChatState.style.display = 'flex';
                
                window.history.pushState({}, '', '/messages');
            }
        });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        isMobile = window.innerWidth <= 768;
        initializeUI();
    });

    // Initialize
    initializeUI();
    scrollToBottom();

    // Add smooth scroll
    modernMessagesContainer.style.scrollBehavior = 'smooth';
    
    // Add input animation
    const luxuryWrapper = document.querySelector('.luxury-input-wrapper');
    if (luxuryWrapper) {
        luxuryWrapper.addEventListener('mouseenter', function() {
            if (!this.classList.contains('focused')) {
                this.style.transform = 'translateY(-1px)';
            }
        });
        
        luxuryWrapper.addEventListener('mouseleave', function() {
            if (!this.classList.contains('focused')) {
                this.style.transform = 'translateY(0)';
            }
        });
    }
});
</script>
@endsection