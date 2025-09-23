<?php

namespace App\Policies;

use App\Models\ObservationDate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObservationDatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ObservationDate $observationDate)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ObservationDate $observationDate)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ObservationDate $observationDate)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ObservationDate $observationDate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationDate  $observationDate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ObservationDate $observationDate)
    {
        //
    }
}
