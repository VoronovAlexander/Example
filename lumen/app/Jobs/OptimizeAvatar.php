<?php

namespace App\Jobs;

use App\User;
use Spatie\Image\Image;

class OptimizeAvatar extends Job
{

    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imagePath = storage_path("app/" . $this->user->getOriginal('avatar'));

        Image::load($imagePath)
            ->width(256)
            ->height(256)
            ->quality(75)
            ->save();
            
    }
}
