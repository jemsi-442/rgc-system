<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_number',
        'envelope_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'occupation',
        'address',
        'house_number',
        'block_number',
        'city',
        'region',
        'baptism_date',
        'confirmation_date',
        'membership_date',
        'marital_status',
        'spouse_name',
        'spouse_phone',
        'children_info',
        'neighbor_name',
        'neighbor_phone',
        'church_elder',
        'pledge_number',
        'special_group',
        'ministry_groups',
        'id_number',
        'is_active',
        'notes',
        'user_id',
        'department_id',
        'jumuiya_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'baptism_date' => 'date',
        'confirmation_date' => 'date',
        'membership_date' => 'date',
        'is_active' => 'boolean',
        'children_info' => 'array',
        'ministry_groups' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jumuiya()
    {
        return $this->belongsTo(Jumuiya::class);
    }

    public function ledJumuiya()
    {
        return $this->hasOne(Jumuiya::class, 'leader_id');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }

    public function pledgePayments()
    {
        return $this->hasMany(PledgePayment::class);
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    // Scope for active members
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get age
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? \Carbon\Carbon::parse($this->date_of_birth)->age : null;
    }

    // Get age group
    public function getAgeGroupAttribute()
    {
        $age = $this->age;

        if (!$age) {
            return null;
        }

        if ($age < 18) {
            return 'Watoto';
        } elseif ($age >= 18 && $age < 35) {
            return 'Vijana';
        } elseif ($age >= 35 && $age < 60) {
            return 'Wazima';
        } else {
            return 'Wazee';
        }
    }

    // Generate unique envelope number
    public static function generateEnvelopeNumber()
    {
        $year = date('Y');
        $lastMember = static::whereYear('created_at', $year)
            ->whereNotNull('envelope_number')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMember && $lastMember->envelope_number) {
            $sequence = intval(substr($lastMember->envelope_number, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return 'ENV' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
