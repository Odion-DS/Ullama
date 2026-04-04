<?php

namespace App\Enums;

enum OllamaPermission: string
{
    case CAN_GENERATE_RESPONSE = 'can_generate_response';
    case CAN_GENERATE_CHAT_MESSAGE = 'can_generate_chat_message';
    case CAN_GENERATE_EMBEDDINGS = 'can_generate_embeddings';
    case CAN_LIST_MODELS = 'can_list_models';
    case CAN_SHOW_MODEL_DETAIL = 'can_show_model_detail';
    case CAN_CREATE_MODEL = 'can_create_model';
    case CAN_COPY_MODEL = 'can_copy_model';
    case CAN_PULL_MODEL = 'can_pull_model';
    case CAN_PUSH_MODEL = 'can_push_model';
    case CAN_DELETE_MODEL = 'can_delete_model';

    public function label(): string
    {
        return match($this) {
            self::CAN_GENERATE_RESPONSE => 'Can Generate Response',
            self::CAN_GENERATE_CHAT_MESSAGE => 'Can Generate Chat Message',
            self::CAN_GENERATE_EMBEDDINGS => 'Can Generate Embeddings',
            self::CAN_LIST_MODELS => 'Can List Models',
            self::CAN_SHOW_MODEL_DETAIL => 'Can Show Model Detail',
            self::CAN_CREATE_MODEL => 'Can Create Model',
            self::CAN_COPY_MODEL => 'Can Copy Model',
            self::CAN_PULL_MODEL => 'Can Pull Model',
            self::CAN_PUSH_MODEL => 'Can Push Model',
            self::CAN_DELETE_MODEL => 'Can Delete Model',
        };
    }
}
