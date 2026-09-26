<?php

namespace App\Models;


use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;



class User extends Authenticatable
{


    use HasApiTokens, HasFactory, Notifiable;




    /**
     * Mass assignable attributes.
     */
    protected $fillable = [

        'name',

        'email',

        'password',

        'status',

    ];








    /**
     * Hidden attributes.
     */
    protected $hidden = [

        'password',

        'remember_token',

    ];









    /**
     * Casts.
     */
    protected function casts(): array
    {

        return [

            'email_verified_at' => 'datetime',

            'password' => 'hashed',

        ];

    }











    /**
     * User profile.
     */
    public function profile(): HasOne
    {

        return $this->hasOne(Profile::class);

    }











    /**
     * User resumes.
     */
    public function resumes(): HasMany
    {

        return $this->hasMany(Resume::class);

    }











    /**
     * User roles.
     */
    public function roles(): BelongsToMany
    {

        return $this->belongsToMany(

            Role::class,

            'role_user'

        )

        ->withTimestamps();

    }











    /**
     * Assign role to user.
     */
    public function assignRole(string $role): void
    {


        $roleModel = Role::where('slug', $role)

            ->orWhere('name', $role)

            ->first();



        if ($roleModel) {


            $this->roles()

                ->syncWithoutDetaching([

                    $roleModel->id

                ]);


        }


    }











    /**
     * Remove role from user.
     */
    public function removeRole(string $role): void
    {


        $roleModel = Role::where('slug', $role)

            ->orWhere('name', $role)

            ->first();



        if ($roleModel) {


            $this->roles()

                ->detach(

                    $roleModel->id

                );


        }


    }











    /**
     * Get user role name.
     */
    public function getRoleName(): ?string
    {


        return $this->roles()

            ->first()

            ?->name;


    }











    /**
     * Check role.
     */
    public function hasRole(string $role): bool
    {


        return $this->roles()

            ->where(

                'slug',

                $role

            )

            ->exists();


    }











    /**
     * Check owner.
     */
    public function isOwner(): bool
    {


        return $this->hasRole('owner');


    }











    /**
     * Check permission.
     */
    public function hasPermission(string $permission): bool
    {


        return $this->roles()

            ->whereHas(

                'permissions',

                function ($query) use ($permission) {


                    $query->where(

                        'slug',

                        $permission

                    );


                }

            )

            ->exists();


    }











    /**
     * Unread notifications count.
     *
     * Used by admin topbar.
     */
    public function unreadNotificationsCount(): int
    {


        return $this->unreadNotifications()

            ->count();


    }




}