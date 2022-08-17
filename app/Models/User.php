<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\SendCodeMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'login_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    public function userRole() {
        return $this->hasOne(UserRole::class, 'user_id', 'id');
    }
    

    public function generateCode($user)
    {
        $code = rand(1000, 9999);

        UserCode::updateOrCreate(
            [ 'user_id' =>$user->id ],
            [ 'code' => $code ]
        );

        try {

            $details = [
                'title' => 'Mail from Laravel Banking',
                'code' => $code
            ];

            Mail::to($user->email)->send(new SendCodeMail($details));

        } catch (\Exception $e) {
            return response(["Error: ". $e->getMessage()]);
        }
    }
}
