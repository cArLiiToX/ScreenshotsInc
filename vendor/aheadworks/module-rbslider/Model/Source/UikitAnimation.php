<?php
namespace Aheadworks\Rbslider\Model\Source;

use Aheadworks\Rbslider\Model\Source\AnimationEffect;

/**
 * Class UikitAnimation
 * @package Aheadworks\Rbslider\Model\Source
 */
class UikitAnimation
{
    /**
     * @var array
     */
    private $animation = [
        AnimationEffect::SLIDE => 'scroll',
        AnimationEffect::FADE_OUT_IN => 'fade',
        AnimationEffect::SCALE => 'scale'
    ];

    /**
     * Retrieve animation effect name by key
     *
     * @param int $key
     * @return string
     */
    public function getAnimationEffectByKey($key)
    {
        return $this->animation[$key];
    }
}
