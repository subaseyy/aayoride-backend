<?php

namespace Modules\ChattingManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversationFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'file_name',
        'file_type',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Modules\ChatModule\Database\factories\ConversationFileFactory::new();
    }
}
