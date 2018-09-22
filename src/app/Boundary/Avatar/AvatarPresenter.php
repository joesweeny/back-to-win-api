<?php

namespace BackToWin\Boundary\Avatar;

use BackToWin\Domain\Avatar\Entity\Avatar;

class AvatarPresenter
{
    /**
     * Convert a domain Avatar object into a scalar data transfer object
     *
     * @param Avatar $avatar
     * @return \stdClass
     */
    public function toDto(Avatar $avatar): \stdClass
    {
        return (object) [
            'user_id' => (string) $avatar->getUserId(),
            'filename' => $avatar->getFilename(),
            'contents' => $avatar->getFileContents()
        ];
    }
}
