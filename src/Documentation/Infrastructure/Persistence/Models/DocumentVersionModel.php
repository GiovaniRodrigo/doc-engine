<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersionModel extends Model
{
    protected $table = 'document_versions';

    protected $fillable = [
        'document_id',
        'markdown',
        'html',
        'hash',
        'commit_hash',
        'state',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(DocumentModel::class, 'document_id');
    }
}