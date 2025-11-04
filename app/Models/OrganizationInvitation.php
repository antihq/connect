<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationInvitation extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
