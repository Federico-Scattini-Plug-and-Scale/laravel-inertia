<?php

namespace App\Observers;

use App\Models\TagGroup;

class TagGroupObserver
{
    /**
     * Handle the TagGroup "created" event.
     *
     * @param  \App\Models\TagGroup  $tagGroup
     * @return void
     */
    public function created(TagGroup $tagGroup)
    {
        //
    }

    /**
     * Handle the TagGroup "updated" event.
     *
     * @param  \App\Models\TagGroup  $tagGroup
     * @return void
     */
    public function updated(TagGroup $tagGroup)
    {
        //
    }

    /**
     * Handle the TagGroup "deleted" event.
     *
     * @param  \App\Models\TagGroup  $tagGroup
     * @return void
     */
    public function deleted(TagGroup $tagGroup)
    {
        //
    }

    /**
     * Handle the TagGroup "restored" event.
     *
     * @param  \App\Models\TagGroup  $tagGroup
     * @return void
     */
    public function restored(TagGroup $tagGroup)
    {
        //
    }

    /**
     * Handle the TagGroup "force deleted" event.
     *
     * @param  \App\Models\TagGroup  $tagGroup
     * @return void
     */
    public function forceDeleted(TagGroup $tagGroup)
    {
        //
    }
}
