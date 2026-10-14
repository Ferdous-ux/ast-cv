<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;



class Role extends Model
{

    use HasFactory;




    protected $fillable = [

        'name',

        'slug',

        'description',

        'is_system',

    ];





    protected $casts = [

        'is_system' => 'boolean',

    ];









    /**
     * Users assigned to role
     */
    public function users(): BelongsToMany
    {


        return $this->belongsToMany(

            User::class,

            'role_user'

        )

        ->withTimestamps();


    }









    /**
     * Role permissions
     */
    public function permissions(): BelongsToMany
    {


        return $this->belongsToMany(

            Permission::class,

            'permission_role'

        )

        ->withTimestamps();


    }









    /**
     * Check permission
     */
    public function hasPermission(string $permission): bool
    {


        return $this->permissions()

            ->where(

                'slug',

                $permission

            )

            ->exists();


    }









    /**
     * Give permissions
     */
    public function givePermissionTo(array $permissions): void
    {


        $this->permissions()

            ->syncWithoutDetaching($permissions);


    }









    /**
     * Remove permission
     */
    public function revokePermission($permission): void
    {


        $this->permissions()

            ->detach($permission);


    }









    /**
     * Permissions count
     */
    public function permissionsCount(): int
    {


        return $this->permissions()

            ->count();


    }









    /**
     * Users count
     */
    public function usersCount(): int
    {


        return $this->users()

            ->count();


    }





}