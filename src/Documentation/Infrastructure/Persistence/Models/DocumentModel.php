<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentModel extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'project',
        'path',
        'slug',
        'title',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersionModel::class, 'document_id');
    }
}