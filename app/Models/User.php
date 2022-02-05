<?php

namespace App\Models;

use App\Extensions\Traits\MustVerifyEmail as TraitsMustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TraitsMustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['detail'];

    const ADMIN = 'admin';
    const COMPANY = 'company';
    const APPLICANT = 'applicant';

    public function detail()
    {
        return $this->morphTo();
    }

    public function getHasCompanyDetails()
    {
        return $this->detail_type == 'App\Models\CompanyDetail';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function invoiceDetails()
    {
        return $this->hasOne(InvoiceDetail::class);
    }

    public function getHasInvoiceDetails()
    {
        return $this->invoiceDetails()->exists();
    }

    public function isAdmin()
    {
        return $this->role == User::ADMIN;
    }

    public function isCompany()
    {
        return $this->role == User::COMPANY;
    }

    public function isApplicant()
    {
        return $this->role == User::APPLICANT;
    }
}
